<?php

namespace App\Http\Controllers;

use App\Services\ForexService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ForexController extends Controller
{
    public function rates(ForexService $forex)
    {
        return response()->json($forex->rates());
    }

    public function convert(Request $request, ForexService $forex)
    {
        $data = $request->validate([
            'from' => ['required', 'string', 'in:UGX,USD,EUR,ugx,usd,eur'],
            'to' => ['required', 'string', 'in:UGX,USD,EUR,ugx,usd,eur'],
            'amount' => ['required', 'numeric', 'min:0.000001'],
        ]);

        try {
            $result = $forex->convert(
                $request->user(),
                $data['from'],
                $data['to'],
                (float) $data['amount']
            );

            return response()->json([
                'message' => 'Converted successfully.',
                ...$result,
            ]);
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['amount' => $e->getMessage()]);
        }
    }
}
