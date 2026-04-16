<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'avatar',
        'name',
        'reference',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(ChatConversation::class, 'chat_room_id');
    }

    public function hasUser(int $userId): bool
    {
        return $this->users->contains('id', $userId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->whereHas('users', fn ($q) => $q->where('users.id', $userId));
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar ?? asset('images/default-room.png');
    }
}
