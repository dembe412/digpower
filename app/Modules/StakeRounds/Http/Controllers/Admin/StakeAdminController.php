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
        $settings = Setting::get('stake_settings', [
            'payout_cap_pct' => config('stake_rounds.payout_cap_pct', '90'),
            'open_seconds' => config('stake_rounds.open_seconds', 15),
            'min_stake' => config('stake_rounds.min_stake', '1'),
            'max_stake' => config('stake_rounds.max_stake', '500'),
            'default_daily_limit' => config('stake_rounds.default_daily_limit', '100'),
            'volatility_bp' => config('stake_rounds.rate.volatility_bp', 60),
        ]);

        return response()->json([
            'rounds_settled' => (clone $done)->count(),
            'rounds_void' => StakeRound::where('status', 'void')->count(),
            'total_staked' => (string) ((clone $done)->sum('pool_total') ?: '0'),
            'total_paid_out' => (string) ((clone $done)->sum('paid_total') ?: '0'),
            'house_take' => (string) ((clone $done)->sum('house_take') ?: '0'),
            'in_play' => (string) (Stake::whereIn('status', ['active', 'cashout_requested'])->sum('amount') ?: '0'),
            'paused' => (bool) (Setting::get('stake_paused')['v'] ?? false),
            'settings' => $settings,
        ]);
    }

    public function updateSettings(Request $r)
    {
        $d = $r->validate([
            'payout_cap_pct' => 'required|numeric|min:50|max:99',
            'open_seconds' => 'required|integer|min:5|max:120',
            'min_stake' => 'required|numeric|min:0.1',
            'max_stake' => 'required|numeric|min:1',
            'default_daily_limit' => 'required|numeric|min:1',
            'volatility_bp' => 'required|integer|min:10|max:500',
        ]);

        Setting::put('stake_settings', $d);
        AuditLog::record($r->user()->id, 'stake.settings_updated', null, $d);

        return response()->json(['settings' => $d, 'message' => 'Mining strategy settings updated.']);
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
