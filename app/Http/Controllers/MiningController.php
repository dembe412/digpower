<?php

namespace App\Http\Controllers;

use App\Models\MiningRecord;
use App\Services\VipService;
use App\Services\WalletService;
use App\Services\YieldService;
use Illuminate\Http\Request;

class MiningController extends Controller
{
    public function show(Request $r, VipService $vip, YieldService $yield)
    {
        $yield->expireStale();
        $u = $r->user();
        $s = $vip->summary($u);
        $pending = (string) (MiningRecord::where('user_id', $u->id)->where('status', 'to_claim')->sum('amount') ?: '0');

        return response()->json([
            'claimable' => $pending, 'level' => $s['name'], 'hashrate' => $s['hashrate'],
            'daily_estimate' => $s['estimated_daily'], 'unit' => 'USDT',
            'note' => 'Daily figure is an estimate from the last distribution and may change.',
        ]);
    }

    /** status: to_claim | claimed | expired */
    public function records(Request $r, YieldService $yield)
    {
        $r->validate(['status' => 'required|in:to_claim,claimed,expired']);
        $yield->expireStale();

        return MiningRecord::where('user_id', $r->user()->id)->where('status', $r->status)->latest()->paginate(20);
    }

    public function claim(Request $r, WalletService $wallet, YieldService $yield)
    {
        $yield->expireStale();
        $r->validate(['record_id' => 'nullable|integer']);
        $sum = $yield->claim($r->user(), $wallet, $r->record_id);
        abort_if(bccomp($sum, '0', 6) <= 0, 422, 'Nothing to claim.');

        return response()->json(['claimed' => $sum]);
    }

    public function boost()
    {
        // "Boost hashrate" = go deposit more; the frontend routes the user to the deposit flow.
        return response()->json(['action' => 'deposit', 'message' => 'Hashrate scales with your active deposit.']);
    }
}
