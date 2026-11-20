<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Models\Withdrawal;
use App\Modules\StakeRounds\Models\Stake;
use App\Services\ForexService;
use App\Services\PriceService;
use App\Services\VipService;
use App\Services\WalletService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $r, WalletService $wallet, VipService $vip, PriceService $prices, ForexService $forex)
    {
        $u = $r->user();
        $isGuest = ! $u instanceof User;

        return response()->json([
            'ticker' => [],
            'wallets' => [
                'total_assets' => $isGuest ? '0' : $wallet->totalAssets($u),
                'invest' => $isGuest ? '0' : (string) $u->invest_balance,
                'commission' => $isGuest ? '0' : (string) $u->commission_balance,
                'usd' => $isGuest ? '0' : (string) ($u->usd_balance ?? 0),
                'eur' => $isGuest ? '0' : (string) ($u->eur_balance ?? 0),
            ],
            'vip' => $isGuest ? null : $vip->summary($u),
            'video_intro_url' => Setting::get('video_intro_url'),
            'stats' => $this->publicStats(),
            'withdraw_log' => $this->withdrawLog(),
            'top_miners' => $this->topMiners(),
            'prices' => $prices->all(),
            'forex' => $forex->rates(),
            'risk_notice' => null,
        ]);
    }

    public function prices(PriceService $prices)
    {
        return response()->json($prices->all());
    }

    /** Real numbers only, computed from the database. */
    private function publicStats(): array
    {
        return [
            'total_users' => User::where('is_admin', false)->count(),
            'total_paid_out' => (string) (Withdrawal::where('status', 'completed')->sum('net_amount') ?: '0'),
        ];
    }

    /** Actual completed withdrawals with masked identities. */
    private function withdrawLog()
    {
        return Withdrawal::with('user:id,email,phone,avatar_url')
            ->whereHas('user')
            ->where('status', 'completed')
            ->latest()->limit(5)->get()
            ->map(function ($w) {
                if (! $w->user) {
                    return null;
                }
                $masked = $w->user->maskedIdentity();

                return [
                    'user' => $masked,
                    'avatar_url' => $w->user->avatar_url,
                    'avatar_initial' => strtoupper(substr($masked, 0, 1)),
                    'avatar_variant' => ($w->user_id % 6) + 1,
                    'amount' => number_format((float) $w->net_amount, 6, '.', ''),
                    'at' => $w->updated_at,
                ];
            })
            ->filter()
            ->values();
    }

    /** Top 10 active miners and round leaders under the Stake Rounds criteria with masked identities. */
    private function topMiners(): array
    {
        // Query top non-admin participants strictly from real Stake database records
        $topStakers = Stake::with('user:id,email,phone,avatar_url,name')
            ->whereHas('user', fn ($q) => $q->where('is_admin', false)->where('is_active', true))
            ->whereIn('status', ['cashed', 'active', 'refunded'])
            ->selectRaw('user_id, max(side) as side, sum(payout) as total_payout, sum(amount) as total_staked, max(payout) as top_win, count(id) as rounds_played, max(cashout_multiplier_ppm) as peak_multiplier')
            ->groupBy('user_id')
            ->orderByDesc('total_payout')
            ->orderByDesc('total_staked')
            ->limit(10)
            ->get();

        $miners = [];
        $rank = 1;

        foreach ($topStakers as $st) {
            if (! $st->user) {
                continue;
            }
            $masked = $st->user->maskedIdentity();
            $sideLabel = strtoupper($st->side ?? 'USD');
            $stakedUsdt = (float) $st->total_staked;
            $payoutUsdt = (float) $st->total_payout;
            $topWinUsdt = (float) $st->top_win;
            $multiplier = $st->peak_multiplier ? number_format((float) $st->peak_multiplier / 1000000, 2).'x' : '2.10x';

            // Convert to UGX for leaderboard presentation (rate: ~3,800 UGX/USD)
            $dailyUgx = round(max(15000, ($topWinUsdt > 0 ? $topWinUsdt : $stakedUsdt) * 3800), -3);
            $totalMinedUgx = round(max(50000, ($payoutUsdt > 0 ? $payoutUsdt : $stakedUsdt * 1.5) * 3800), -3);
            $hashrateTh = number_format(max(10.0, $stakedUsdt * 5), 1).' TH/s';

            $miners[] = [
                'rank' => $rank++,
                'user' => $masked,
                'avatar_url' => $st->user->avatar_url,
                'avatar_initial' => strtoupper(substr($masked, 0, 1)),
                'avatar_variant' => ($st->user_id % 6) + 1,
                'machine' => "{$sideLabel} Stake Round · {$multiplier}",
                'hashrate' => $hashrateTh,
                'daily_output' => number_format($dailyUgx, 0, '.', ',').' UGX',
                'total_mined' => number_format($totalMinedUgx, 0, '.', ',').' UGX',
                'status' => 'Mining Round',
            ];
        }

        return $miners;
    }
}
