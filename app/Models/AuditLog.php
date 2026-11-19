<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $guarded = [];

    protected $casts = ['meta' => 'array'];

    public static function record(?int $userId, string $action, ?Model $subject = null, array $meta = []): void
    {
        static::create([
            'user_id' => $userId, 'action' => $action,
            'subject_type' => $subject?->getMorphClass(), 'subject_id' => $subject?->getKey(),
            'meta' => $meta, 'ip' => request()->ip(),
        ]);
    }
}
