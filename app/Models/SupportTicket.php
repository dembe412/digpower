<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $guarded = [];

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class);
    }
}
