<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'principal' => 'decimal:2',
            'daily_income' => 'decimal:6',
            'cashed_out_amount' => 'decimal:6',
            'requested_at' => 'datetime',
            'activated_at' => 'datetime',
            'matures_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function earnedAmount(?\DateTimeInterface $at = null): string
    {
        if ($this->status !== 'active' || ! $this->activated_at) {
            return '0.000000';
        }

        $end = $at ? Carbon::instance($at) : now();
        $days = max(0, $this->activated_at->diffInDays($end));

        return bcmul((string) $this->daily_income, (string) $days, 6);
    }

    public function availableToCashOut(?\DateTimeInterface $at = null): string
    {
        $available = bcsub($this->earnedAmount($at), (string) $this->cashed_out_amount, 6);

        return bccomp($available, '0', 6) > 0 ? $available : '0.000000';
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['pending', 'active'], true);
    }
}
