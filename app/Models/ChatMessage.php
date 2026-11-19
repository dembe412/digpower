<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatMessage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'mentions' => 'array',
        'is_staff' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reply_to_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'reply_to_id');
    }

    public function toResponseArray(?User $viewer = null): array
    {
        $sender = $this->user;
        $senderName = $this->is_staff ? 'Admin · Support' : ($sender?->name ?: ($sender ? $sender->maskedIdentity() : 'Member'));
        $senderAvatar = strtoupper(substr($senderName, 0, 1) ?: 'U');
        $senderAvatarUrl = $sender?->avatar_url;

        $reply = null;
        if ($this->relationLoaded('replyTo') && $this->replyTo) {
            $replySender = $this->replyTo->user;
            $replySenderName = $this->replyTo->is_staff ? 'Admin · Support' : ($replySender?->name ?: ($replySender ? $replySender->maskedIdentity() : 'Member'));
            $reply = [
                'id' => $this->replyTo->id,
                'user_name' => $replySenderName,
                'user_avatar_url' => $replySender?->avatar_url,
                'message' => $this->replyTo->message,
                'image_url' => $this->replyTo->image_url,
            ];
        }

        return [
            'id' => $this->id,
            'type' => $this->type,
            'room' => $this->room,
            'user_id' => $this->user_id,
            'user_name' => $senderName,
            'user_avatar' => $senderAvatar,
            'user_avatar_url' => $senderAvatarUrl,
            'is_staff' => (bool) $this->is_staff,
            'is_me' => $viewer ? $this->user_id === $viewer->id : false,
            'message' => $this->message,
            'image_url' => $this->image_url,
            'mentions' => $this->mentions ?: [],
            'reply_to' => $reply,
            'created_at' => $this->created_at?->toIso8601String(),
            'time_formatted' => $this->created_at?->diffForHumans(null, true).' ago',
            'time_clock' => $this->created_at?->format('H:i'),
        ];
    }
}
