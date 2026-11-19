<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiningRecord extends Model
{
    protected $guarded = [];

    protected $casts = ['expires_at' => 'datetime', 'claimed_at' => 'datetime'];
}
