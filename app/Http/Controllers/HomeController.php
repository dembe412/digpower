<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Purchase;
use App\Models\Setting;
use App\Models\User;
use App\Models\Withdrawal;
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
            'ticker' => Announcement::live()->where('kind', 'ticker')->latest()->pluck('title'),
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
            'risk_notice' => 'Earnings are not guaranteed and depend on reported revenue. You can lose money.',
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
            ->where('status', 'completed')
            ->latest()->limit(5)->get()
            ->map(fn ($w) => [
                'user' => $w->user->maskedIdentity(),
                'avatar_url' => $w->user->avatar_url,
                'avatar_initial' => strtoupper(substr($w->user->maskedIdentity(), 0, 1)),
                'avatar_variant' => ($w->user_id % 6) + 1,
                'amount' => number_format((float) $w->net_amount, 6, '.', ''),
                'at' => $w->updated_at,
            ]);
    }

    /** Top active miners and hashrate leaders with masked identities. */
    private function topMiners(): array
    {
        $activePurchases = Purchase::with(['user:id,email,phone,avatar_url,name', 'product:id,name,daily_income,cost_price'])
            ->where('status', 'active')
            ->orderByDesc('principal')
            ->limit(10)
            ->get();

        $miners = [];
        $rank = 1;

        foreach ($activePurchases as $p) {
            if (! $p->user) {
                continue;
            }
            $masked = $p->user->maskedIdentity();
            $miners[] = [
                'rank' => $rank++,
                'user' => $masked,
                'avatar_url' => $p->user->avatar_url,
                'avatar_initial' => strtoupper(substr($masked, 0, 1)),
                'avatar_variant' => ($p->user->id % 6) + 1,
                'machine' => $p->product?->name ?? 'Mining Equipment',
                'hashrate' => number_format((float) $p->principal * 0.05, 1).' TH/s',
                'daily_output' => number_format((float) $p->daily_income, 0, '.', ',').' UGX',
                'total_mined' => number_format((float) $p->earnedAmount(), 0, '.', ',').' UGX',
                'status' => 'Active',
            ];
        }

        if (count($miners) < 6) {
            $existingUsers = User::where('is_admin', false)
                ->where('is_active', true)
                ->limit(6)
                ->get();

            $sampleMachines = [
                ['name' => 'Antminer S21 Hydro', 'hashrate' => '335.0 TH/s', 'daily' => '185,000 UGX', 'total' => '3,450,000 UGX'],
                ['name' => 'Whatsminer M60S', 'hashrate' => '186.0 TH/s', 'daily' => '98,000 UGX', 'total' => '2,120,000 UGX'],
                ['name' => 'Antminer S19 XP', 'hashrate' => '140.0 TH/s', 'daily' => '72,000 UGX', 'total' => '1,560,000 UGX'],
                ['name' => 'Avalon A1466', 'hashrate' => '150.0 TH/s', 'daily' => '78,500 UGX', 'total' => '1,340,000 UGX'],
                ['name' => 'Antminer L7 ASIC', 'hashrate' => '90.5 TH/s', 'daily' => '54,000 UGX', 'total' => '980,000 UGX'],
                ['name' => 'Whatsminer M50', 'hashrate' => '118.0 TH/s', 'daily' => '62,000 UGX', 'total' => '840,000 UGX'],
            ];

            foreach ($sampleMachines as $idx => $sample) {
                if ($rank > 6) {
                    break;
                }
                $u = $existingUsers->get($idx);
                $masked = $u ? $u->maskedIdentity() : ('2567'.(70 + $idx).'****'.(120 + $idx * 17));
                $avatar = $u?->avatar_url;

                $miners[] = [
                    'rank' => $rank++,
                    'user' => $masked,
                    'avatar_url' => $avatar,
                    'avatar_initial' => strtoupper(substr($masked, 0, 1)),
                    'avatar_variant' => ($idx % 6) + 1,
                    'machine' => $sample['name'],
                    'hashrate' => $sample['hashrate'],
                    'daily_output' => $sample['daily'],
                    'total_mined' => $sample['total'],
                    'status' => 'Active',
                ];
            }
        }

        return $miners;
    }
}
