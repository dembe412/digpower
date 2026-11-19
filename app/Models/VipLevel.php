<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VipLevel extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'recharge_requirement' => 'decimal:2',
            'target_yield_pct' => 'decimal:2',
            'monthly_salary' => 'decimal:6',
            'is_active' => 'boolean',
            'rebate_rates' => 'array',
        ];
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(VipPurchase::class);
    }
}
