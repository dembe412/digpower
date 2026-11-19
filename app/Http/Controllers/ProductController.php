<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('cost_price')
            ->get());
    }

    public function owned(Request $request)
    {
        return response()->json($request->user()->purchases()->with('product')->latest()->get()->map(function ($purchase) {
            return $purchase->toArray() + [
                'earned_amount' => $purchase->earnedAmount(),
                'available_to_cash_out' => $purchase->availableToCashOut(),
            ];
        }));
    }
}
