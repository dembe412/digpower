<?php

namespace App\Modules\StakeRounds\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Modules\StakeRounds\Models\Stake;
use App\Modules\StakeRounds\Models\StakeRound;
use Illuminate\Http\Request;

class StakeAdminController extends Controller
{
    public function summary()
    {
        $done = StakeRound::where('status', 'settled');

        return response()->json([
            'rounds_settled' => (clone $done)->count(),
            'rounds_void' => StakeRound::where('status', 'void')->count(),
            'total_staked' => (string) ((clone $done)->sum('pool_total') ?: '0'),
            'total_paid_out' => (string) ((clone $done)->sum('paid_total') ?: '0'),
            'house_take' => (string) ((clone $done)->sum('house_take') ?: '0'),
            'in_play' => (string) (Stake::whereIn('status', ['active', 'cashout_requested'])->sum('amount') ?: '0'),
            'paused' => (bool) (Setting::get('stake_paused')['v'] ?? false),
        ]);
    }

    public function rounds()
    {
        return StakeRound::latest('id')->paginate(30);
    }

    public function stakes(StakeRound $round)
    {
        return Stake::with('user:id,email,phone')->where('stake_round_id', $round->id)->get();
    }

    public function pause(Request $r)
    {
        $d = $r->validate(['paused' => 'required|boolean']);
        Setting::put('stake_paused', ['v' => $d['paused']]); // a running round still finishes; no new round opens
        AuditLog::record($r->user()->id, 'stake.paused', null, ['paused' => $d['paused']]);

        return response()->json(['paused' => $d['paused']]);
    }
}
