<?php

namespace App\Modules\StakeRounds\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stake extends Model
{
    protected $guarded = [];

    protected $casts = ['cashed_at' => 'datetime', 'amount' => 'decimal:6', 'payout' => 'decimal:6'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(StakeRound::class, 'stake_round_id');
    }
}
