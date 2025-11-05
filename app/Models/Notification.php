<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): bool
    {
        if ($this->isUnread()) {
            return $this->update(['read_at' => now()]);
        }
        return false;
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function getIconTypeAttribute(): string
    {
        return $this->data['icon_type'] ?? 'icon';
    }

    public function getIconAttribute(): string
    {
        return $this->data['icon'] ?? $this->getDefaultIcon();
    }

    public function getAvatarAttribute(): ?string
    {
        return $this->data['avatar'] ?? null;
    }

    private function getDefaultIcon(): string
    {
        return match($this->type) {
            'success' => 'ri-check-line',
            'warning' => 'ri-alert-line',
            'error' => 'ri-close-line',
            'info' => 'ri-information-line',
            default => 'ri-notification-line'
        };
    }
}
