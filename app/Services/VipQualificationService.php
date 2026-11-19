<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Purchase;
use App\Models\User;
use App\Models\VipLevel;

class VipQualificationService
{
    public function for(User $user, VipLevel $level): array
    {
        $members = app(ReferralService::class)->levels($user);
        $counts = [];
        foreach ([1, 2, 3] as $depth) {
            $ids = $members[$depth] ?? [];
            $counts[$depth] = $ids ? User::whereIn('id', $ids)
                ->where('is_active', true)
                ->whereHas('purchases', fn ($query) => $query->where('status', 'active'))
                ->count() : 0;
        }

        $recharge = (string) Deposit::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->sum('amount');

        return [
            'eligible' => Purchase::where('user_id', $user->id)->where('status', 'active')->exists()
                && bccomp($recharge, (string) $level->recharge_requirement, 2) >= 0
                && $counts[1] >= $level->required_level_1_members
                && $counts[2] >= $level->required_level_2_members
                && $counts[3] >= $level->required_level_3_members,
            'active_product' => Purchase::where('user_id', $user->id)->where('status', 'active')->exists(),
            'confirmed_recharge' => $recharge,
            'required_recharge' => (string) $level->recharge_requirement,
            'members' => [
                1 => ['count' => $counts[1], 'required' => $level->required_level_1_members],
                2 => ['count' => $counts[2], 'required' => $level->required_level_2_members],
                3 => ['count' => $counts[3], 'required' => $level->required_level_3_members],
            ],
        ];
    }
}
