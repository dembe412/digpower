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
        $megaWeekend = Setting::get('stake_mega_weekend', [
            'enabled' => false,
            'payout_cap_pct' => 96,
            'banner_text' => '⚡ MEGA WEEKEND MINING: Boosted Payouts & Reduced House Margins Active!',
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
            'mega_weekend' => $megaWeekend,
        ]);
    }

    public function toggleMegaWeekend(Request $r)
    {
        $d = $r->validate([
            'enabled' => 'required|boolean',
            'payout_cap_pct' => 'nullable|numeric|min:80|max:99',
            'banner_text' => 'nullable|string|max:255',
        ]);

        $current = Setting::get('stake_mega_weekend', [
            'enabled' => false,
            'payout_cap_pct' => 96,
            'banner_text' => '⚡ MEGA WEEKEND MINING: Boosted Payouts & Reduced House Margins Active!',
        ]);

        $updated = [
            'enabled' => (bool) $d['enabled'],
            'payout_cap_pct' => isset($d['payout_cap_pct']) ? (float) $d['payout_cap_pct'] : ($current['payout_cap_pct'] ?? 96),
            'banner_text' => ! empty($d['banner_text']) ? $d['banner_text'] : ($current['banner_text'] ?? '⚡ MEGA WEEKEND MINING: Boosted Payouts & Reduced House Margins Active!'),
        ];

        Setting::put('stake_mega_weekend', $updated);
        AuditLog::record($r->user()->id, 'stake.mega_weekend_toggled', null, $updated);

        return response()->json([
            'mega_weekend' => $updated,
            'message' => $updated['enabled'] ? 'Mega Weekend mode is now ACTIVE!' : 'Mega Weekend mode has been deactivated.',
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
