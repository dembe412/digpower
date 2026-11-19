<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class TransferController extends Controller
{
    use Concerns;

    /** Move funds between the user's own wallets (e.g. commission -> invest). */
    public function store(Request $r, WalletService $wallet)
    {
        $d = $r->validate([
            'from' => 'required|in:invest,commission',
            'to' => 'required|in:invest,commission|different:from',
            'amount' => 'required|numeric|min:0.01',
        ]);
        $u = $r->user();
        $this->requireSecurity($r, $u);
        $amount = bcadd((string) $d['amount'], '0', 6);

        try {
            DB::transaction(function () use ($u, $wallet, $d, $amount) {
                if (bccomp($wallet->withdrawable($u, $d['from']), $amount, 6) < 0) {
                    throw new RuntimeException('Insufficient transferable balance.');
                }
                $wallet->debit($u, $d['from'], $amount, 'transfer_out', null, ['to' => $d['to']]);
                $wallet->credit($u, $d['to'], $amount, 'transfer_in', null, ['from' => $d['from']]);
            });
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['amount' => $e->getMessage()]);
        }

        return response()->json(['message' => 'Transferred', 'invest' => (string) $u->invest_balance, 'commission' => (string) $u->commission_balance]);
    }
}
