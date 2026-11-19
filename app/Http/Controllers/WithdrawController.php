<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Withdrawal;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class WithdrawController extends Controller
{
    use Concerns;

    public function options(Request $r, WalletService $wallet)
    {
        $u = $r->user();

        return response()->json([
            'min' => config('platform.withdrawal.min', 5000),
            'currency' => config('platform.currency', 'UGX'),
            'fee_pct' => config('platform.withdrawal.fee_pct'),
            'providers' => [
                ['key' => 'mtn', 'name' => 'MTN Mobile Money'],
                ['key' => 'airtel', 'name' => 'Airtel Money'],
            ],
            'phone' => $u->phone,
            'networks' => config('platform.withdrawal.networks'),
            'available' => ['invest' => $wallet->withdrawable($u, 'invest'), 'commission' => $wallet->withdrawable($u, 'commission')],
            'locked_principal' => $wallet->lockedPrincipal($u),
        ]);
    }

    public function index(Request $r)
    {
        return Withdrawal::where('user_id', $r->user()->id)->latest()->paginate(20);
    }

    public function store(Request $r, WalletService $wallet)
    {
        $minWithdrawal = config('platform.withdrawal.min', 5000);
        $d = $r->validate([
            'wallet' => 'required|in:invest,commission',
            'amount' => ['required', 'numeric', 'min:'.$minWithdrawal],
            'payout_method' => ['nullable', 'string', 'max:32'],
            'phone' => ['nullable', 'string', 'max:30'],
            'account_name' => ['nullable', 'string', 'max:80'],
            'network' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string', 'max:120'],
        ], [
            'amount.min' => 'The minimum withdrawal amount is '.number_format((float) $minWithdrawal).' UGX.',
        ]);
        $u = $r->user();
        $this->requireSecurity($r, $u);

        $phone = $d['phone'] ?? $u->phone;
        if (! $phone && empty($d['address'])) {
            throw ValidationException::withMessages(['phone' => ['Please enter a Mobile Money phone number for payout.']]);
        }

        $payoutMethod = $d['payout_method'] ?? 'mobile_money';
        $accountName = $d['account_name'] ?? $u->name;
        $network = $d['network'] ?? $payoutMethod;
        $address = $d['address'] ?? $phone;

        $amount = bcadd((string) $d['amount'], '0', 6);
        $fee = bcdiv(bcmul($amount, (string) config('platform.withdrawal.fee_pct'), 8), '100', 6);
        $net = bcsub($amount, $fee, 6);

        try {
            $w = DB::transaction(function () use ($u, $wallet, $d, $amount, $fee, $net, $phone, $payoutMethod, $accountName, $network, $address) {
                if (bccomp($wallet->withdrawable($u, $d['wallet']), $amount, 6) < 0) {
                    throw new RuntimeException('Amount exceeds your withdrawable balance (deposits are locked during the plan window).');
                }
                $w = Withdrawal::create([
                    'user_id' => $u->id,
                    'wallet' => $d['wallet'],
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
                $wallet->debit($u, $d['wallet'], $amount, 'withdrawal', $w);

                return $w;
            });
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['amount' => $e->getMessage()]);
        }

        AuditLog::record($u->id, 'withdrawal.requested', $w, ['amount' => $amount]);

        return response()->json($w, 201);
    }
}
