<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function scopeBetween(Builder $query, int $firstUserId, int $secondUserId): Builder
    {
        return $query->where(function (Builder $conversation) use ($firstUserId, $secondUserId) {
            $conversation
                ->where('sender_id', $firstUserId)
                ->where('recipient_id', $secondUserId);
        })->orWhere(function (Builder $conversation) use ($firstUserId, $secondUserId) {
            $conversation
                ->where('sender_id', $secondUserId)
                ->where('recipient_id', $firstUserId);
        });
    }
}
