<?php

namespace App\Modules\StakeRounds\Services;

use App\Models\User;
use App\Modules\StakeRounds\Contracts\WalletGateway;
use App\Modules\StakeRounds\Models\Stake;
use App\Modules\StakeRounds\Models\StakeProfile;
use App\Modules\StakeRounds\Models\StakeRound;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class StakeService
{
    public function __construct(private WalletGateway $wallet, private RoundEngine $engine) {}

    private function fail(string $msg, string $key = 'stake'): never
    {
        throw ValidationException::withMessages([$key => $msg]);
    }

    // ---------- Responsible-gaming profile
    public function profile(User $u): StakeProfile
    {
        $p = StakeProfile::firstOrCreate(['user_id' => $u->id], ['daily_limit' => config('stake_rounds.default_daily_limit')]);
        if ($p->pending_daily_limit !== null && $p->pending_limit_applies_at && $p->pending_limit_applies_at->isPast()) {
            $p->update(['daily_limit' => $p->pending_daily_limit, 'pending_daily_limit' => null, 'pending_limit_applies_at' => null]);
        }

        return $p;
    }

    public function confirmAdult(User $u, string $dob): StakeProfile
    {
        $age = Carbon::parse($dob)->age;
        if ($age < config('stake_rounds.min_age')) {
            $this->fail('You must be at least '.config('stake_rounds.min_age').' to take part.', 'date_of_birth');
        }
        $p = $this->profile($u);
        $p->update(['adult_confirmed_at' => now(), 'date_of_birth' => $dob]);

        return $p;
    }

    /** Lowering a limit is immediate; raising it only applies after a cooling-off delay. */
    public function setDailyLimit(User $u, string $limit): StakeProfile
    {
        $p = $this->profile($u);
        if (bccomp($limit, (string) $p->daily_limit, 6) <= 0) {
            $p->update(['daily_limit' => $limit, 'pending_daily_limit' => null, 'pending_limit_applies_at' => null]);
        } else {
            $p->update(['pending_daily_limit' => $limit, 'pending_limit_applies_at' => now()->addHours(config('stake_rounds.limit_increase_delay_hours'))]);
        }

        return $p->refresh();
    }

    public function selfExclude(User $u, int $days): StakeProfile
    {
        $p = $this->profile($u);
        $until = now()->addDays($days);
        if ($p->self_excluded_until && $p->self_excluded_until->gt($until)) {
            $until = $p->self_excluded_until; // an exclusion can only be extended, never shortened
        }
        $p->update(['self_excluded_until' => $until]);

        return $p;
    }

    public function stakedToday(User $u): string
    {
        return (string) (Stake::where('user_id', $u->id)->where('status', '!=', 'refunded')
            ->where('created_at', '>=', now()->startOfDay())->sum('amount') ?: '0');
    }

    // ---------- Actions
    public function enter(User $u, string $side, string $amount): Stake
    {
        $cfg = config('stake_rounds');
        $amount = bcadd($amount, '0', 6);
        if (! in_array($side, ['usd', 'ugx'], true)) {
            $this->fail('Invalid side.', 'side');
        }
        if ($this->engine->paused()) {
            $this->fail('Stake Rounds is currently unavailable.');
        }
        if (bccomp($amount, $cfg['min_stake'], 6) < 0 || bccomp($amount, $cfg['max_stake'], 6) > 0) {
            $this->fail("Stake must be between {$cfg['min_stake']} and {$cfg['max_stake']} USDT.", 'amount');
        }

        $p = $this->profile($u);
        if (! $p->adult_confirmed_at) {
            $this->fail('Please confirm you are 18+ before taking part.');
        }
        if ($p->self_excluded_until && $p->self_excluded_until->isFuture()) {
            $this->fail('You have self-excluded until '.$p->self_excluded_until->toDateString().'.');
        }
        if (bccomp(bcadd($this->stakedToday($u), $amount, 6), (string) $p->daily_limit, 6) > 0) {
            $this->fail('This would exceed your daily stake limit of '.$p->daily_limit.' USDT.', 'amount');
        }

        try {
            return DB::transaction(function () use ($u, $side, $amount) {
                $round = StakeRound::where('status', 'open')->lockForUpdate()->first();
                if (! $round || now() >= $round->starts_at) {
                    $this->fail('No round is open for staking right now. Try again in a moment.');
                }
                if (Stake::where('stake_round_id', $round->id)->where('user_id', $u->id)->exists()) {
                    $this->fail('You already have a stake in this round.');
                }
                if (bccomp($this->wallet->spendable($u, config('stake_rounds.wallet')), $amount, 6) < 0) {
                    $this->fail('Insufficient available balance (locked deposits cannot be staked).', 'amount');
                }
                $stake = Stake::create(['stake_round_id' => $round->id, 'user_id' => $u->id, 'side' => $side, 'amount' => $amount, 'status' => 'active']);
                $this->wallet->debit($u, config('stake_rounds.wallet'), $amount, 'stake', $stake, ['round' => $round->id, 'side' => $side]);

                return $stake;
            });
        } catch (RuntimeException $e) {
            $this->fail($e->getMessage(), 'amount');
        }
    }

    public function cashOut(User $u): Stake
    {
        return DB::transaction(function () use ($u) {
            $round = StakeRound::where('status', 'running')->lockForUpdate()->first();
            if (! $round) {
                $this->fail('No round is running.');
            }
            $stake = Stake::where('stake_round_id', $round->id)->where('user_id', $u->id)->lockForUpdate()->first();
            if (! $stake) {
                $this->fail('You have no stake in the running round.');
            }
            if ($stake->status !== 'active') {
                $this->fail('Your cash-out is already requested or settled.');
            }
            $tick = $this->engine->tickFor($round, now());
            if ($tick >= $this->engine->maxTick($round)) {
                $this->fail('The round is ending; remaining stakes are settled automatically.');
            }
            $stake->update(['status' => 'cashout_requested', 'cashout_tick' => $tick]);

            return $stake; // paid when this tick's batch closes (~ one tick later)
        });
    }

    // ---------- Read model
    public function state(User $u): array
    {
        $cfg = config('stake_rounds');
        $round = StakeRound::whereIn('status', ['open', 'running'])->orderBy('id')->first();
        $p = $this->profile($u);
        $base = [
            'name' => $cfg['name'], 'notice' => $cfg['notice'], 'paused' => $this->engine->paused(),
            'limits' => ['daily_limit' => (string) $p->daily_limit, 'staked_today' => $this->stakedToday($u),
                'pending_limit' => $p->pending_daily_limit, 'self_excluded_until' => $p->self_excluded_until],
            'adult_confirmed' => (bool) $p->adult_confirmed_at,
            'rules' => ['payout_cap_pct' => $cfg['payout_cap_pct'], 'tick_ms' => $cfg['tick_ms'], 'min_players' => $cfg['min_players'],
                'min_stake' => $cfg['min_stake'], 'max_stake' => $cfg['max_stake']],
        ];
        if (! $round) {
            return $base + ['round' => null];
        }

        $stakes = Stake::where('stake_round_id', $round->id);
        $mine = (clone $stakes)->where('user_id', $u->id)->first();
        $live = [
            'usd' => (string) ((clone $stakes)->where('side', 'usd')->sum('amount') ?: '0'),
            'ugx' => (string) ((clone $stakes)->where('side', 'ugx')->sum('amount') ?: '0'),
        ];

        $r = [
            'id' => $round->id, 'status' => $round->status, 'server_seed_hash' => $round->server_seed_hash,
            'side_totals' => $live, 'players' => (clone $stakes)->count(),
        ];
        if ($round->status === 'open') {
            $r['seconds_to_start'] = max(0, now()->diffInSeconds($round->starts_at, false));
        } else {
            $tick = $this->engine->tickFor($round, now());
            $m = $this->engine->multipliers($round, $tick);
            $r += [
                'tick' => $tick, 'pool_total' => (string) $round->pool_total, 'cap_total' => (string) $round->cap_total,
                'paid_total' => (string) $round->paid_total,
                'multipliers' => ['usd' => bcdiv((string) $m['usd'], '1000000', 4), 'ugx' => bcdiv((string) $m['ugx'], '1000000', 4)],
                'seconds_left' => max(0, $cfg['max_run_seconds'] - intdiv($tick * $round->tick_ms, 1000)),
            ];
        }
        $r['my_stake'] = $mine ? ['side' => $mine->side, 'amount' => (string) $mine->amount, 'status' => $mine->status, 'payout' => (string) $mine->payout] : null;

        return $base + ['round' => $r];
    }
}
