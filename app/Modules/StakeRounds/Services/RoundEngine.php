<?php

namespace App\Modules\StakeRounds\Services;

use App\Models\Setting;
use App\Modules\StakeRounds\Contracts\WalletGateway;
use App\Modules\StakeRounds\Models\Stake;
use App\Modules\StakeRounds\Models\StakeRound;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Drives the round lifecycle: open -> running -> settled (or void). Call advance() repeatedly.
 * Money rule: total cash-outs can never exceed payout_cap_pct of the pool, so a round cannot lose money.
 */
class RoundEngine
{
    public function __construct(private WalletGateway $wallet, private RateIndex $rate) {}

    public function paused(): bool
    {
        return ! config('stake_rounds.enabled') || (Setting::get('stake_paused')['v'] ?? false);
    }

    public function advance(?Carbon $now = null): ?StakeRound
    {
        $now ??= now();
        if ($this->paused()) {
            return null;
        }

        return DB::transaction(function () use ($now) {
            $round = StakeRound::whereIn('status', ['open', 'running'])->orderBy('id')->lockForUpdate()->first();
            if (! $round) {
                return $this->openRound($now);
            }
            if ($round->status === 'open' && $now >= $round->starts_at) {
                $round = $this->startRound($round, $now);
            }
            if ($round->status === 'running') {
                $round = $this->runRound($round, $now);
            }

            return in_array($round->status, ['settled', 'void'], true) ? $this->openRound($now) : $round;
        });
    }

    public function nowMs(Carbon $now): int
    {
        return (int) floor((float) $now->getPreciseTimestamp(3));
    }

    public function tickFor(StakeRound $r, Carbon $now): int
    {
        return intdiv(max(0, $this->nowMs($now) - (int) $r->started_ms), $r->tick_ms);
    }

    public function maxTick(StakeRound $r): int
    {
        return intdiv(config('stake_rounds.max_run_seconds') * 1000, $r->tick_ms);
    }

    public function multipliers(StakeRound $r, int $tick): array
    {
        return $this->rate->multipliers($r->server_seed, (string) $r->client_seed, $r->id, $r->volatility_bp, $tick);
    }

    public function series(StakeRound $r, int $toTick): array
    {
        return $this->rate->series($r->server_seed, (string) $r->client_seed, $r->id, $r->volatility_bp, $toTick);
    }

    private function openRound(Carbon $now): StakeRound
    {
        $seed = bin2hex(random_bytes(32));

        return StakeRound::create([
            'status' => 'open', 'opens_at' => $now, 'starts_at' => $now->copy()->addSeconds(config('stake_rounds.open_seconds')),
            'server_seed' => $seed, 'server_seed_hash' => hash('sha256', $seed),
            'volatility_bp' => config('stake_rounds.rate.volatility_bp'), 'tick_ms' => config('stake_rounds.tick_ms'),
        ]);
    }

    private function startRound(StakeRound $round, Carbon $now): StakeRound
    {
        $stakes = Stake::where('stake_round_id', $round->id)->orderBy('id')->get();
        $pool = $stakes->reduce(fn ($c, $s) => bcadd($c, (string) $s->amount, 6), '0');

        if ($stakes->count() < config('stake_rounds.min_players')) {
            foreach ($stakes as $s) {
                $s->update(['status' => 'refunded']);
                $this->wallet->credit($s->user, config('stake_rounds.wallet'), (string) $s->amount, 'stake_refund', $s, ['round' => $round->id]);
            }
            $round->update(['status' => 'void', 'ended_at' => $now, 'players' => $stakes->count(), 'pool_total' => $pool, 'end_reason' => 'void_min_players']);

            return $round;
        }

        $cap = bcdiv(bcmul($pool, (string) config('stake_rounds.payout_cap_pct'), 8), '100', 6);
        $clientSeed = hash('sha256', $stakes->map(fn ($s) => "{$s->id}:{$s->user_id}:{$s->side}:{$s->amount}")->implode('|'));

        $round->update([
            'status' => 'running', 'started_ms' => $this->nowMs($now), 'client_seed' => $clientSeed,
            'pool_total' => $pool, 'cap_total' => $cap, 'players' => $stakes->count(),
        ]);

        return $round;
    }

    private function runRound(StakeRound $round, Carbon $now): StakeRound
    {
        $cur = $this->tickFor($round, $now);
        $timedOut = $cur >= $this->maxTick($round);

        // Only fully elapsed ticks are processed, so order inside a tick is irrelevant (no speed advantage).
        $before = $timedOut ? PHP_INT_MAX : $cur;
        $ticks = Stake::where('stake_round_id', $round->id)->where('status', 'cashout_requested')
            ->where('cashout_tick', '<', $before)->orderBy('cashout_tick')->distinct()->pluck('cashout_tick');

        foreach ($ticks as $t) {
            $batch = Stake::where('stake_round_id', $round->id)->where('status', 'cashout_requested')->where('cashout_tick', $t)->orderBy('id')->get();
            if ($this->payBatch($round, $batch, (int) $t)) {
                return $this->settle($round, $now, 'cap_reached', (int) $t);
            }
        }

        if ($timedOut) {
            $end = $this->maxTick($round);
            $rest = Stake::where('stake_round_id', $round->id)->where('status', 'active')->orderBy('id')->get();
            if ($rest->isNotEmpty()) {
                $this->payBatch($round, $rest, $end); // auto cash-out at the final rate, still bound by the cap
            }

            return $this->settle($round, $now, 'timeout', $end);
        }

        return $round;
    }

    /** @return bool true when the payout cap has been reached (round must end) */
    private function payBatch(StakeRound $round, $stakes, int $tick): bool
    {
        $m = $this->multipliers($round, $tick);
        $gross = [];
        $sum = '0';
        foreach ($stakes as $s) {
            $g = bcdiv(bcmul((string) $s->amount, (string) $m[$s->side], 6), '1000000', 6);
            $gross[$s->id] = $g;
            $sum = bcadd($sum, $g, 6);
        }

        $remaining = bcsub((string) $round->cap_total, (string) $round->paid_total, 6);
        $trim = bccomp($sum, $remaining, 6) > 0; // batch would breach the cap: pay everyone proportionally
        $paid = '0';

        foreach ($stakes as $s) {
            $pay = $trim ? bcdiv(bcmul($gross[$s->id], $remaining, 12), $sum, 6) : $gross[$s->id];
            $s->update(['status' => 'cashed', 'payout' => $pay, 'cashout_multiplier_ppm' => $m[$s->side], 'cashed_at' => now()]);
            if (bccomp($pay, '0', 6) > 0) {
                $this->wallet->credit($s->user, config('stake_rounds.wallet'), $pay, 'stake_payout', $s, ['round' => $round->id, 'tick' => $tick]);
            }
            $paid = bcadd($paid, $pay, 6);
        }

        $round->paid_total = bcadd((string) $round->paid_total, $paid, 6);
        $round->save();

        return $trim || bccomp((string) $round->paid_total, (string) $round->cap_total, 6) >= 0;
    }

    private function settle(StakeRound $round, Carbon $now, string $reason, int $endTick): StakeRound
    {
        Stake::where('stake_round_id', $round->id)->whereIn('status', ['active', 'cashout_requested'])->update(['status' => 'lost', 'payout' => 0]);
        $round->update([
            'status' => 'settled', 'ended_at' => $now, 'end_reason' => $reason, 'end_tick' => $endTick,
            'house_take' => bcsub((string) $round->pool_total, (string) $round->paid_total, 6),
        ]);

        return $round;
    }
}
