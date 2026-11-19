<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VipPurchase extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'principal' => 'decimal:2',
            'monthly_salary' => 'decimal:6',
            'recharge_requirement' => 'decimal:2',
            'salary_paid' => 'decimal:6',
            'requested_at' => 'datetime',
            'activated_at' => 'datetime',
            'matures_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vipLevel(): BelongsTo
    {
        return $this->belongsTo(VipLevel::class);
    }

    public function availableSalary(?\DateTimeInterface $at = null): string
    {
        if ($this->status !== 'active' || ! $this->activated_at) {
            return '0.000000';
        }

        $end = $at ? Carbon::instance($at) : now();
        $days = min($this->duration_days, max(0, $this->activated_at->diffInDays($end)));
        $earned = bcdiv(bcmul((string) $this->monthly_salary, (string) $days, 8), '30', 6);

        return bcsub($earned, (string) $this->salary_paid, 6);
    }
}
