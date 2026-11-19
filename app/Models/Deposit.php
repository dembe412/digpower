<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    protected $guarded = [];

    protected $casts = ['confirmed_at' => 'datetime', 'window_ends_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'confirmed')
            ->where(fn ($w) => $w->whereNull('window_ends_at')->orWhere('window_ends_at', '>', now()));
    }
}
