<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Single entry point for every balance change. Each change is row-locked and written to the ledger.
 */
class WalletService
{
    public const WALLETS = ['invest' => 'invest_balance', 'commission' => 'commission_balance'];

    public function credit(User $user, string $wallet, string $amount, string $type, ?Model $ref = null, array $meta = []): Transaction
    {
        return $this->apply($user, $wallet, bcadd('0', $amount, 6), $type, $ref, $meta);
    }

    public function debit(User $user, string $wallet, string $amount, string $type, ?Model $ref = null, array $meta = []): Transaction
    {
        return $this->apply($user, $wallet, bcmul('-1', $amount, 6), $type, $ref, $meta);
    }

    private function apply(User $user, string $wallet, string $signed, string $type, ?Model $ref, array $meta): Transaction
    {
        $col = self::WALLETS[$wallet] ?? throw new RuntimeException('Unknown wallet');

        return DB::transaction(function () use ($user, $col, $wallet, $signed, $type, $ref, $meta) {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $new = bcadd((string) $locked->{$col}, $signed, 6);
            if (bccomp($new, '0', 6) < 0) {
                throw new RuntimeException('Insufficient balance');
            }
            $locked->{$col} = $new;
            $locked->save();
            $user->setRawAttributes($locked->getAttributes(), true);

            return Transaction::create([
                'user_id' => $user->id, 'wallet' => $wallet, 'type' => $type,
                'amount' => $signed, 'balance_after' => $new,
                'ref_type' => $ref?->getMorphClass(), 'ref_id' => $ref?->getKey(), 'meta' => $meta,
            ]);
        });
    }

    /** Principal that is still inside its plan window and therefore not withdrawable. */
    public function lockedPrincipal(User $user): string
    {
        return (string) (Deposit::where('user_id', $user->id)->active()->sum('amount') ?: '0');
    }

    public function withdrawable(User $user, string $wallet): string
    {
        $user->refresh();
        if ($wallet === 'commission') {
            return bcadd((string) $user->commission_balance, '0', 6);
        }
        $free = bcsub((string) $user->invest_balance, $this->lockedPrincipal($user), 6);

        return bccomp($free, '0', 6) < 0 ? '0.000000' : $free;
    }

    public function totalAssets(User $user): string
    {
        return bcadd((string) $user->invest_balance, (string) $user->commission_balance, 6);
    }

    public function creditAndDebit(User $user, string $wallet, string $amount, string $creditType, string $debitType, ?Model $ref = null, array $meta = []): void
    {
        $this->credit($user, $wallet, $amount, $creditType, $ref, $meta);
        $this->debit($user, $wallet, $amount, $debitType, $ref, $meta);
    }
}
