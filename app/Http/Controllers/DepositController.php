<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Setting;
use App\Services\DepositService;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function options()
    {
        $methods = Setting::get('payment_methods') ?: config('payments.methods', []);
        $nets = collect(config('platform.deposit.networks'))->filter()->keys()->values();

        return response()->json([
            'methods' => array_values($methods),
            'networks' => $nets,
            'min' => config('platform.deposit.min', 5000),
            'currency' => config('platform.currency', 'UGX'),
        ]);
    }

    public function index(Request $r)
    {
        return Deposit::where('user_id', $r->user()->id)->latest()->paginate(20);
    }

    /** Declare intent / submit deposit payment proof (TLS flow supported). */
    public function store(Request $r)
    {
        $methods = Setting::get('payment_methods') ?: config('payments.methods', []);
        $allowedMethods = array_keys($methods);
        $allowedNets = array_keys(config('platform.deposit.networks', []));
        $allKeys = array_unique(array_merge($allowedMethods, $allowedNets));

        $d = $r->validate([
            'payment_method' => ['nullable', 'string', 'in:'.implode(',', $allKeys)],
            'network' => ['nullable', 'string', 'in:'.implode(',', $allKeys)],
            'amount' => ['required', 'numeric', 'min:'.config('platform.deposit.min', 1), 'max:100000000'],
            'txid' => ['nullable', 'string', 'min:4', 'max:120', 'unique:deposits,txid'],
            'transaction_id' => ['nullable', 'string', 'min:4', 'max:120', 'unique:deposits,txid'],
            'payer_name' => ['nullable', 'string', 'max:80'],
        ]);

        $selectedKey = $d['payment_method'] ?? $d['network'] ?? ($allowedMethods[0] ?? 'mtn');
        $methodConfig = $methods[$selectedKey] ?? config("payments.methods.{$selectedKey}");
        $cryptoAddr = config("platform.deposit.networks.{$selectedKey}");

        $addr = $methodConfig['number'] ?? $cryptoAddr ?? ($methods['trc20']['number'] ?? 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t');

        $txid = $d['transaction_id'] ?? $d['txid'] ?? null;
        $payerName = $d['payer_name'] ?? $r->user()->name ?? $r->user()->phone ?? 'Member';

        $dep = Deposit::create([
            'user_id' => $r->user()->id,
            'network' => $selectedKey,
            'payment_method' => $methodConfig['key'] ?? $selectedKey,
            'payment_number' => $methodConfig['number'] ?? $addr,
            'payer_name' => $payerName,
            'currency' => $methodConfig['currency'] ?? 'USDT',
            'amount' => $d['amount'],
            'address' => $addr,
            'txid' => $txid,
            'status' => 'pending',
        ]);

        return response()->json($dep, 201);
    }

    /** Step 2: user submits the transaction id for review if not provided at creation. */
    public function submitTxid(Request $r, Deposit $deposit)
    {
        abort_unless($deposit->user_id === $r->user()->id && $deposit->status === 'pending', 404);
        $r->validate(['txid' => ['required', 'string', 'min:4', 'max:120', 'unique:deposits,txid']]);
        $deposit->update(['txid' => $r->txid]);

        return response()->json($deposit);
    }

    /** Signed webhook from a payment processor / chain watcher. */
    public function webhook(Request $r, DepositService $svc)
    {
        $secret = (string) config('platform.deposit.webhook_secret');
        abort_if($secret === '', 503, 'Webhook not configured');
        $expected = hash_hmac('sha256', $r->getContent(), $secret);
        abort_unless(hash_equals($expected, (string) $r->header('X-Signature')), 401, 'Bad signature');

        $p = $r->validate(['txid' => 'required|string', 'amount' => 'required|numeric', 'network' => 'required|string']);
        $dep = Deposit::where('txid', $p['txid'])->where('network', $p['network'])->first();
        if (! $dep) {
            return response()->json(['message' => 'Unknown txid'], 202);
        }
        // Never credit less than was actually received.
        if (bccomp((string) $p['amount'], (string) $dep->amount, 6) < 0) {
            $dep->update(['note' => 'Received amount lower than declared; manual review.']);

            return response()->json(['message' => 'Amount mismatch, flagged'], 202);
        }
        $svc->confirm($dep);

        return response()->json(['message' => 'ok']);
    }
}
