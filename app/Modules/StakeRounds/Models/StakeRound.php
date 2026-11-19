<?php

namespace App\Modules\StakeRounds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StakeRound extends Model
{
    protected $guarded = [];

    protected $hidden = ['server_seed'];

    protected $casts = ['server_seed' => 'encrypted', 'pool_total' => 'decimal:6', 'cap_total' => 'decimal:6', 'paid_total' => 'decimal:6', 'house_take' => 'decimal:6', 'opens_at' => 'datetime', 'starts_at' => 'datetime', 'ended_at' => 'datetime'];

    public function stakes(): HasMany
    {
        return $this->hasMany(Stake::class);
    }
}
