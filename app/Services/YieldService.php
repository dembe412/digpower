<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\MiningRecord;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\YieldBatch;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Distributes REAL reported revenue pro-rata by hashrate. No revenue entered => nothing is paid.
 */
class YieldService
{
    public function distribute(YieldBatch $batch): YieldBatch
    {
        return DB::transaction(function () use ($batch) {
            $batch = YieldBatch::whereKey($batch->id)->lockForUpdate()->firstOrFail();
            if ($batch->status === 'distributed') {
                throw new RuntimeException('Batch already distributed');
            }

            $rate = (string) config('platform.hashrate_per_usd');
            $principalByUser = Deposit::active()->selectRaw('user_id, SUM(amount) as principal')
                ->groupBy('user_id')->pluck('principal', 'user_id');

            $weights = [];
            $total = '0';
            foreach ($principalByUser as $uid => $principal) {
                $h = bcmul((string) $principal, $rate, 6);
                $weights[$uid] = $h;
                $total = bcadd($total, $h, 6);
            }
            if (bccomp($total, '0', 6) <= 0) {
                throw new RuntimeException('No active hashrate to distribute to');
            }

            $net = bcmul((string) $batch->gross_revenue, bcsub('1', bcdiv((string) $batch->fee_pct, '100', 8), 8), 6);
            $paid = '0';
            $expires = now()->addDays(config('platform.claim_window_days'));

            foreach ($weights as $uid => $h) {
                $amount = bcdiv(bcmul($net, $h, 10), $total, 6); // truncated, remainder stays with platform
                if (bccomp($amount, '0', 6) <= 0) {
                    continue;
                }
                MiningRecord::create([
                    'user_id' => $uid, 'yield_batch_id' => $batch->id, 'hashrate' => $h,
                    'amount' => $amount, 'status' => 'to_claim', 'expires_at' => $expires,
                ]);
                UserNotification::create(['user_id' => $uid, 'title' => 'Earnings available', 'body' => "{$amount} UGX is ready to claim."]);
                $paid = bcadd($paid, $amount, 6);
            }

            $batch->update(['status' => 'distributed', 'distributed' => $paid, 'total_hashrate' => $total]);

            return $batch;
        });
    }

    public function expireStale(): int
    {
        return MiningRecord::where('status', 'to_claim')->where('expires_at', '<', now())->update(['status' => 'expired']);
    }

    public function claim(User $user, WalletService $wallet, ?int $recordId = null): string
    {
        return DB::transaction(function () use ($user, $wallet, $recordId) {
            $records = MiningRecord::where('user_id', $user->id)->where('status', 'to_claim')
                ->where('expires_at', '>=', now())
                ->when($recordId, fn ($q) => $q->whereKey($recordId))
                ->lockForUpdate()->get();

            $sum = '0';
            foreach ($records as $r) {
                $wallet->credit($user, 'invest', (string) $r->amount, 'mining_claim', $r);
                $r->update(['status' => 'claimed', 'claimed_at' => now()]);
                $sum = bcadd($sum, (string) $r->amount, 6);
            }

            return $sum;
        });
    }
}
