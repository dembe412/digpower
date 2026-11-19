<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatementController extends Controller
{
    public function index(Request $r)
    {
        $r->validate(['type' => 'nullable|string', 'wallet' => 'nullable|in:invest,commission', 'from' => 'nullable|date', 'to' => 'nullable|date']);

        return $r->user()->transactions()
            ->when($r->type, fn ($q) => $q->where('type', $r->type))
            ->when($r->wallet, fn ($q) => $q->where('wallet', $r->wallet))
            ->when($r->from, fn ($q) => $q->where('created_at', '>=', $r->from))
            ->when($r->to, fn ($q) => $q->where('created_at', '<=', $r->to.' 23:59:59'))
            ->latest()->paginate(30);
    }
}
