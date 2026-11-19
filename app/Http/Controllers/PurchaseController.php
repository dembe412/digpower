<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Services\ReferralService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PurchaseController extends Controller
{
    public function store(Request $request, Product $product, WalletService $wallet, ReferralService $referrals)
    {
        abort_unless($product->is_active, 404, 'Product is unavailable.');

        $request->merge(['payment_source' => $request->input('payment_source', 'manual')]);
        $data = $request->validate([
            'payment_source' => ['required', 'in:manual,invest,commission'],
            'transaction_id' => ['required_if:payment_source,manual', 'nullable', 'string', 'max:120', 'unique:purchases,transaction_id'],
        ]);

        $data['payment_source'] ??= 'manual';

        abort_if($request->user()->purchases()
            ->where('product_id', $product->id)
            ->whereIn('status', ['pending', 'active'])
            ->exists(), 422, 'You already have an open purchase for this product.');

        try {
            $purchase = DB::transaction(function () use ($request, $product, $data, $wallet, $referrals) {
                $fromBalance = $data['payment_source'] !== 'manual';
                $purchase = $request->user()->purchases()->create([
                    'product_id' => $product->id,
                    'status' => $fromBalance ? 'active' : 'pending',
                    'payment_method' => $fromBalance ? 'wallet' : 'manual',
                    'transaction_id' => $data['transaction_id'] ?? 'BAL-'.strtoupper(Str::random(12)),
                    'principal' => $product->cost_price,
                    'daily_income' => $product->daily_income,
                    'duration_days' => $product->duration_days,
                    'requested_at' => now(),
                    'activated_at' => $fromBalance ? now() : null,
                    'matures_at' => $fromBalance && $product->duration_days ? now()->addDays($product->duration_days) : null,
                ]);
                if ($fromBalance) {
                    $wallet->debit($request->user(), $data['payment_source'], (string) $product->cost_price, 'purchase', $purchase, ['product_id' => $product->id]);
                    $referrals->payPurchaseRewards($purchase->fresh('user'));
                }

                return $purchase;
            });
        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages(['payment_source' => $exception->getMessage()]);
        }

        return response()->json($purchase->load('product'), 201);
    }

    public function cashOut(Request $request, Purchase $purchase, WalletService $wallet)
    {
        abort_unless($purchase->user_id === $request->user()->id, 403);
        $data = $request->validate([
            'phone' => ['nullable', 'string', 'max:30'],
            'payout_method' => ['nullable', 'string', 'max:32'],
            'account_name' => ['nullable', 'string', 'max:80'],
            'network' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string', 'max:120'],
        ]);

        $u = $request->user();
        $phone = $data['phone'] ?? $u->phone;
        $payoutMethod = $data['payout_method'] ?? 'mobile_money';
        $accountName = $data['account_name'] ?? $u->name;
        $network = $data['network'] ?? $payoutMethod;
        $address = $data['address'] ?? $phone;

        $withdrawal = DB::transaction(function () use ($request, $purchase, $wallet, $phone, $payoutMethod, $accountName, $network, $address) {
            $locked = Purchase::whereKey($purchase->id)->lockForUpdate()->firstOrFail();
            $amount = $locked->availableToCashOut();
            abort_unless($locked->status === 'active' && bccomp($amount, '0', 6) > 0, 422, 'No product earnings are available to cash out.');
            $fee = bcdiv(bcmul($amount, (string) config('platform.withdrawal.fee_pct'), 8), '100', 6);
            $net = bcsub($amount, $fee, 6);
            $withdrawal = $request->user()->withdrawals()->create([
                'purchase_id' => $locked->id,
                'wallet' => 'commission',
                'phone' => $phone,
                'payout_method' => $payoutMethod,
                'account_name' => $accountName,
                'network' => $network,
                'address' => $address,
                'amount' => $amount,
                'fee' => $fee,
                'net_amount' => $net,
                'status' => 'pending',
            ]);
            $locked->update(['cashed_out_amount' => bcadd((string) $locked->cashed_out_amount, $amount, 6)]);
            $wallet->creditAndDebit($request->user(), 'commission', $amount, 'product_earnings', 'withdrawal', $withdrawal, ['purchase_id' => $locked->id]);

            return $withdrawal;
        });

        return response()->json($withdrawal->load('purchase'), 201);
    }
}
