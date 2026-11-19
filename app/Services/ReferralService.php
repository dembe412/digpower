<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;

class ReferralService
{
    public function __construct(private WalletService $wallet) {}

    /** Reward upline once per confirmed deposit, paid from the platform margin. */
    public function payDepositRewards(Deposit $deposit): void
    {
        $user = $deposit->user;
        $cursor = $user;
        foreach (config('platform.referral_rates') as $lvl => $rate) {
            $cursor = $cursor->referrer;
            if (! $cursor) {
                break;
            }

            if ($rate <= 0 || ! $cursor->is_active) {
                continue;
            }
            $amount = bcdiv(bcmul((string) $deposit->amount, (string) $rate, 8), '100', 6);
            if (bccomp($amount, '0', 6) > 0) {
                $this->wallet->credit($cursor, 'commission', $amount, 'referral', $deposit, ['level' => $lvl, 'from_user' => $user->id]);
            }
        }

    }

    /** Reward the upline strictly only on the member's first product purchase. */
    public function payPurchaseRewards(Purchase $purchase): void
    {
        $user = $purchase->user;
        if (! $user) {
            return;
        }

        // 1. Only pay referral rebates on the member's first product purchase (never on subsequent/later products)
        $alreadyRewarded = Transaction::where('type', 'referral')
            ->where(function ($query) use ($user) {
                $query->where('meta->from_user', $user->id)
                    ->orWhere('meta->from_user', (string) $user->id);
            })
            ->where('meta->source', 'product_purchase')
            ->exists();

        if ($alreadyRewarded) {
            return;
        }

        // 2. Also ensure the user does not have any prior activated purchase
        $hasPriorActivated = Purchase::where('user_id', $user->id)
            ->where('id', '!=', $purchase->id)
            ->whereNotNull('activated_at')
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($hasPriorActivated) {
            return;
        }

        $cursor = $user;

        foreach (config('platform.referral_rates') as $level => $rate) {
            $cursor = $cursor->referrer;
            if (! $cursor) {
                break;
            }
            if ($rate <= 0 || ! $cursor->is_active) {
                continue;
            }

            $amount = bcdiv(bcmul((string) $purchase->principal, (string) $rate, 8), '100', 6);
            if (bccomp($amount, '0', 6) > 0) {
                $this->wallet->credit($cursor, 'commission', $amount, 'referral', $purchase, [
                    'level' => $level,
                    'from_user' => $user->id,
                    'source' => 'product_purchase',
                ]);
            }
        }
    }

    /** @return array<int, array<int>> level => user ids */
    public function levels(User $user, int $depth = 3): array
    {
        $out = [];
        $ids = [$user->id];
        for ($l = 1; $l <= $depth; $l++) {
            $ids = $ids ? User::whereIn('referrer_id', $ids)->pluck('id')->all() : [];
            $out[$l] = $ids;
        }

        return $out;
    }

    public function stats(User $user, ?string $from = null, ?string $to = null): array
    {
        $levels = $this->levels($user);
        $all = array_merge(...array_values($levels));
        $range = fn ($q, $col = 'created_at') => $q
            ->when($from, fn ($x) => $x->where($col, '>=', Carbon::parse($from)->startOfDay()))
            ->when($to, fn ($x) => $x->where($col, '<=', Carbon::parse($to)->endOfDay()));

        $commissions = $range($user->transactions()->where('type', 'referral'))->sum('amount');
        $deposits = $range(Deposit::whereIn('user_id', $all)->where('status', 'confirmed'), 'confirmed_at')->sum('amount');
        $withdrawals = $range(Withdrawal::whereIn('user_id', $all)->where('status', 'completed'))->sum('amount');

        $perLevel = [];
        foreach ($levels as $l => $ids) {
            $valid = $ids ? Deposit::whereIn('user_id', $ids)->active()->distinct('user_id')->count('user_id') : 0;
            $perLevel[] = ['level' => $l, 'count' => count($ids), 'valid' => $valid];
        }

        return [
            'team_size' => count($all),
            'ref_commissions' => (string) $commissions,
            'team_deposits' => (string) $deposits,
            'team_withdrawals' => (string) $withdrawals,
            'levels' => $perLevel,
        ];
    }

    public function members(User $user, int $level)
    {
        $ids = $this->levels($user)[$level] ?? [];

        return User::whereIn('id', $ids)->latest()->paginate(20)->through(fn (User $u) => [
            'user' => $u->maskedIdentity(),
            'joined_at' => $u->created_at,
            'valid' => Deposit::where('user_id', $u->id)->active()->exists(),
            'deposited' => (string) Deposit::where('user_id', $u->id)->where('status', 'confirmed')->sum('amount'),
        ]);
    }
}
