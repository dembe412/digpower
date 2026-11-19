<?php

namespace App\Modules\StakeRounds\Http\Controllers;

use App\Http\Controllers\Concerns;
use App\Http\Controllers\Controller;
use App\Modules\StakeRounds\Models\Stake;
use App\Modules\StakeRounds\Models\StakeRound;
use App\Modules\StakeRounds\Services\RoundEngine;
use App\Modules\StakeRounds\Services\StakeService;
use Illuminate\Http\Request;

class StakeController extends Controller
{
    use Concerns;

    public function __construct(private StakeService $svc, private RoundEngine $engine) {}

    public function current(Request $r)
    {
        return response()->json($this->svc->state($r->user()));
    }

    public function enter(Request $r)
    {
        $d = $r->validate(['side' => 'required|in:usd,ugx', 'amount' => 'required|numeric|min:0.000001']);
        $stake = $this->svc->enter($r->user(), $d['side'], (string) $d['amount']);

        return response()->json(['stake' => $stake->only('id', 'side', 'amount', 'status')], 201);
    }

    public function cashOut(Request $r)
    {
        $stake = $this->svc->cashOut($r->user());

        return response()->json(['status' => $stake->status, 'message' => 'Cash-out received. It is settled when the current batch closes.']);
    }

    public function history(Request $r)
    {
        return Stake::where('user_id', $r->user()->id)->latest()->paginate(20);
    }

    /** Settled rounds with revealed seeds, so anyone can verify them. */
    public function rounds()
    {
        return StakeRound::whereIn('status', ['settled', 'void'])->latest('id')->paginate(20)->through(fn ($x) => $this->publicRound($x));
    }

    public function verify(StakeRound $round)
    {
        abort_unless(in_array($round->status, ['settled', 'void'], true), 404, 'Round not finished yet.');
        $series = $round->status === 'settled' ? $this->engine->series($round, (int) $round->end_tick) : [];

        return response()->json($this->publicRound($round) + [
            'volatility_bp' => $round->volatility_bp, 'tick_ms' => $round->tick_ms, 'index_series_ppm' => $series,
            'algorithm' => 'step_bp(k) = (uint32_be(HMAC_SHA256(key=server_seed, msg="client_seed:round_id:k")[0..3]) mod (2*V+1)) - V; '
                .'index(k) = index(k-1) + trunc(index(k-1)*step_bp(k)/10000); index(0)=1000000. '
                .'USD multiplier = index/1e6; UGX multiplier = 1e6/index. Check sha256(server_seed) equals the hash published before the round.',
            'stakes' => Stake::where('stake_round_id', $round->id)->orderBy('id')->get(['id', 'side', 'amount', 'status', 'payout', 'cashout_tick', 'cashout_multiplier_ppm']),
        ]);
    }

    public function profile(Request $r)
    {
        $p = $this->svc->profile($r->user());

        return response()->json($p->only('adult_confirmed_at', 'daily_limit', 'pending_daily_limit', 'pending_limit_applies_at', 'self_excluded_until'));
    }

    public function confirmAdult(Request $r)
    {
        $d = $r->validate(['date_of_birth' => 'required|date|before:today', 'accept' => 'accepted']);
        $this->svc->confirmAdult($r->user(), $d['date_of_birth']);

        return response()->json(['message' => 'Confirmed']);
    }

    public function setLimit(Request $r)
    {
        $d = $r->validate(['daily_limit' => 'required|numeric|min:1|max:100000']);

        return response()->json($this->svc->setDailyLimit($r->user(), (string) $d['daily_limit']));
    }

    public function selfExclude(Request $r)
    {
        $d = $r->validate(['days' => 'required|in:1,7,30,90,180,365']);
        $this->requireSecurity($r, $r->user());

        return response()->json($this->svc->selfExclude($r->user(), (int) $d['days']));
    }

    private function publicRound(StakeRound $x): array
    {
        return [
            'id' => $x->id, 'status' => $x->status, 'end_reason' => $x->end_reason, 'players' => $x->players,
            'pool_total' => (string) $x->pool_total, 'paid_total' => (string) $x->paid_total, 'house_take' => (string) $x->house_take,
            'server_seed_hash' => $x->server_seed_hash, 'server_seed' => $x->server_seed, 'client_seed' => $x->client_seed,
            'ended_at' => $x->ended_at,
        ];
    }
}
