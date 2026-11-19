<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YieldBatch extends Model
{
    protected $guarded = [];

    protected $casts = ['period_date' => 'date'];
}
