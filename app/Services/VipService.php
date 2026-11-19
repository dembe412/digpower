<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\User;
use App\Models\VipLevel;
use App\Models\YieldBatch;

class VipService
{
    public function levelFor(string|float $amount): VipLevel
    {
        return VipLevel::where('level', '>', 0)
            ->where('min_deposit', '<=', $amount)
            ->orderByDesc('min_deposit')
            ->first() ?? VipLevel::where('level', 0)->firstOrFail();
    }

    public function activePrincipal(User $user): string
    {
        return (string) (Deposit::where('user_id', $user->id)->active()->sum('amount') ?: '0');
    }

    public function hashrate(User $user): string
    {
        return bcmul($this->activePrincipal($user), (string) config('platform.hashrate_per_usd'), 6);
    }

    /** Estimated from the most recent real distribution, never from a fixed promised rate. */
    public function estimatedDaily(string $hashrate): string
    {
        $b = YieldBatch::where('status', 'distributed')->orderByDesc('period_date')->first();
        if (! $b || bccomp((string) $b->total_hashrate, '0', 6) <= 0) {
            return '0.000000';
        }

        return bcmul($hashrate, bcdiv((string) $b->distributed, (string) $b->total_hashrate, 10), 6);
    }

    public function summary(User $user): array
    {
        $principal = $this->activePrincipal($user);
        $level = $this->levelFor($principal);
        $hash = $this->hashrate($user);

        return [
            'level' => $level->level,
            'name' => $level->name,
            'deposit_amount' => $principal,
            'hashrate' => $hash,
            'estimated_daily' => $this->estimatedDaily($hash),
        ];
    }
}
