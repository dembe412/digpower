<?php

namespace App\Modules\StakeRounds\Models;

use Illuminate\Database\Eloquent\Model;

class StakeProfile extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'daily_limit' => 'decimal:6', 'pending_daily_limit' => 'decimal:6',
        'adult_confirmed_at' => 'datetime', 'self_excluded_until' => 'datetime',
        'pending_limit_applies_at' => 'datetime', 'date_of_birth' => 'date',
    ];
}
