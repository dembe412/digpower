<?php

namespace App\Http\Controllers;

use App\Services\ReferralService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function show(Request $r, ReferralService $ref)
    {
        $r->validate(['from' => 'nullable|date', 'to' => 'nullable|date|after_or_equal:from']);
        $u = $r->user();

        return response()->json([
            'invite_code' => $u->invite_code,
            'invite_link' => rtrim(config('platform.frontend_url'), '/').'/#/reg?invite_code='.$u->invite_code,
            'stats' => $ref->stats($u, $r->from, $r->to),
            'reward_rates' => config('platform.referral_rates'),
        ]);
    }

    public function members(Request $r, ReferralService $ref, int $level)
    {
        abort_unless(in_array($level, [1, 2, 3], true), 404);

        return $ref->members($r->user(), $level);
    }

    public function commissions(Request $r)
    {
        return $r->user()->transactions()->where('type', 'referral')->latest()->paginate(20);
    }
}
