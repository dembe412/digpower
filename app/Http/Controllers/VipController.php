<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VipLevel;
use App\Models\VipPurchase;
use App\Services\VipQualificationService;
use App\Services\VipService;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class VipController extends Controller
{
    public function index(Request $r, VipService $vip): JsonResponse
    {
        return response()->json([
            'current' => $r->user() instanceof User ? $vip->summary($r->user()) : null,
            'levels' => VipLevel::orderBy('level')->get()->map(fn ($l) => [
                'level' => $l->level, 'name' => $l->name,
                'min_deposit' => $l->min_deposit, 'max_deposit' => $l->max_deposit,
                'target_yield_pct' => $l->target_yield_pct, // indicative target, not a guarantee
                'mining_window' => $l->window_days ? $l->window_days.'days' : 'Forever',
                'deposit_rebate' => collect(config('platform.referral_rates'))->map(fn ($v, $k) => ['level' => $k, 'pct' => $v])->values(),
            ]),
            'packages' => VipLevel::where('is_active', true)->where('price', '>', 0)->orderBy('sort_order')->orderBy('price')->get()->map(fn (VipLevel $level) => [
                'id' => $level->id, 'level' => $level->level, 'name' => $level->name,
                'price' => (string) $level->price, 'monthly_salary' => (string) $level->monthly_salary,
                'duration_days' => $level->duration_days, 'recharge_requirement' => (string) $level->recharge_requirement,
                'required_members' => [$level->required_level_1_members, $level->required_level_2_members, $level->required_level_3_members],
                'tagline' => $level->tagline, 'image_path' => $level->image_path,
            ]),
            'owned' => $r->user() instanceof User ? VipPurchase::with('vipLevel:id,name,level')->where('user_id', $r->user()->id)->latest()->get() : [],
            'disclaimer' => 'Target yields are indicative only. Actual earnings depend on reported revenue and may be lower or zero.',
        ]);
    }

    public function qualification(Request $request, VipLevel $level, VipQualificationService $qualification): JsonResponse
    {
        abort_unless($level->is_active && bccomp((string) $level->price, '0', 2) > 0, 404);

        return response()->json($qualification->for($request->user(), $level));
    }

    public function store(Request $request, VipLevel $level, WalletService $wallet, VipQualificationService $qualification): JsonResponse
    {
        abort_unless($level->is_active && bccomp((string) $level->price, '0', 2) > 0, 404);
        $data = $request->validate([
            'payment_source' => ['required', 'in:manual,invest,commission'],
            'transaction_id' => ['required_if:payment_source,manual', 'nullable', 'string', 'max:120', 'unique:vip_purchases,transaction_id'],
        ]);
        abort_unless($qualification->for($request->user(), $level)['eligible'], 422, 'You do not meet this VIP package qualification.');
        abort_if(VipPurchase::where('user_id', $request->user()->id)->where('vip_level_id', $level->id)
            ->whereIn('status', ['pending', 'active'])->exists(), 422, 'You already have an open purchase for this VIP package.');

        try {
            $purchase = DB::transaction(function () use ($request, $level, $data, $wallet) {
                $walletPayment = $data['payment_source'] !== 'manual';
                $purchase = VipPurchase::create([
                    'user_id' => $request->user()->id, 'vip_level_id' => $level->id,
                    'status' => $walletPayment ? 'active' : 'pending',
                    'payment_method' => $walletPayment ? 'wallet' : 'manual',
                    'transaction_id' => $data['transaction_id'] ?? 'VIP-'.strtoupper(Str::random(12)),
                    'principal' => $level->price, 'monthly_salary' => $level->monthly_salary,
                    'duration_days' => $level->duration_days, 'recharge_requirement' => $level->recharge_requirement,
                    'required_level_1_members' => $level->required_level_1_members,
                    'required_level_2_members' => $level->required_level_2_members,
                    'required_level_3_members' => $level->required_level_3_members,
                    'requested_at' => now(), 'activated_at' => $walletPayment ? now() : null,
                    'matures_at' => $walletPayment ? now()->addDays($level->duration_days) : null,
                ]);
                if ($walletPayment) {
                    $wallet->debit($request->user(), $data['payment_source'], (string) $level->price, 'vip_purchase', $purchase, ['vip_level_id' => $level->id]);
                }

                return $purchase;
            });
        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages(['payment_source' => $exception->getMessage()]);
        }

        return response()->json($purchase->load('vipLevel'), 201);
    }

    public function owned(Request $request): JsonResponse
    {
        return response()->json(VipPurchase::with('vipLevel')->whereBelongsTo($request->user())->latest()->get());
    }

    public function claimSalary(Request $request, VipPurchase $vipPurchase, WalletService $wallet): JsonResponse
    {
        abort_unless($vipPurchase->user_id === $request->user()->id, 403);
        $claimed = DB::transaction(function () use ($request, $vipPurchase, $wallet) {
            $locked = VipPurchase::whereKey($vipPurchase->id)->lockForUpdate()->firstOrFail();
            $amount = $locked->availableSalary();
            abort_unless(bccomp($amount, '0', 6) > 0, 422, 'No VIP salary is available to claim.');
            $wallet->credit($request->user(), 'commission', $amount, 'vip_salary', $locked, ['vip_level_id' => $locked->vip_level_id]);
            $locked->increment('salary_paid', $amount);

            return $amount;
        });

        return response()->json(['message' => 'VIP salary claimed.', 'amount' => $claimed]);
    }
}
