<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Deposit;
use Illuminate\Support\Facades\DB;

class DepositService
{
    public function __construct(
        private WalletService $wallet,
        private VipService $vip,
        private ReferralService $referrals,
    ) {}

    /** Idempotent: only a pending deposit can be confirmed, exactly once. */
    public function confirm(Deposit $deposit, ?int $adminId = null): Deposit
    {
        return DB::transaction(function () use ($deposit, $adminId) {
            $d = Deposit::whereKey($deposit->id)->lockForUpdate()->firstOrFail();
            if ($d->status !== 'pending') {
                return $d;
            }
            $tier = $this->vip->levelFor((string) $d->amount);
            $d->update([
                'status' => 'confirmed', 'confirmed_at' => now(), 'reviewed_by' => $adminId,
                'window_ends_at' => $tier->window_days ? now()->addDays($tier->window_days) : null,
            ]);
            $this->wallet->credit($d->user, 'invest', (string) $d->amount, 'deposit', $d, ['network' => $d->network, 'txid' => $d->txid]);
            $this->referrals->payDepositRewards($d);
            AuditLog::record($adminId, 'deposit.confirmed', $d);

            return $d;
        });
    }
}
