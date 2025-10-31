<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class AuditLog extends Model
{
    use SoftDeletes;

    protected $table = 'audit_logs';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'tags',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'tags' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Uuid::uuid4()->toString();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByModel($query, string $modelType, $modelId = null)
    {
        $query->where('auditable_type', $modelType);

        if ($modelId !== null) {
            $query->where('auditable_id', $modelId);
        }

        return $query;
    }

    public function scopeByIpAddress($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    public function scopeByTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeSecurityEvents($query)
    {
        return $query->where('action', 'like', 'security.%');
    }

    public function scopeFailedLogins($query)
    {
        return $query->where('action', 'auth.failed_login');
    }

    public function scopeBulkOperations($query)
    {
        return $query->where('action', 'like', 'bulk.%');
    }

    public function scopeApiRequests($query)
    {
        return $query->where('action', 'api.request');
    }

    public function scopeDatabaseTransactions($query)
    {
        return $query->where('action', 'like', 'database.%');
    }

    public function getActionCategory(): string
    {
        $parts = explode('.', $this->action);
        return $parts[0] ?? 'unknown';
    }

    public function getActionSubcategory(): string
    {
        $parts = explode('.', $this->action);
        return $parts[1] ?? 'unknown';
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags ?? [], true);
    }

    public function getChanges(): array
    {
        $changes = [];

        if (!empty($this->old_values) || !empty($this->new_values)) {
            $allKeys = array_unique(array_merge(
                array_keys($this->old_values ?? []),
                array_keys($this->new_values ?? [])
            ));

            foreach ($allKeys as $key) {
                $oldValue = $this->old_values[$key] ?? null;
                $newValue = $this->new_values[$key] ?? null;

                if ($oldValue !== $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        return $changes;
    }

    public function isSecurityEvent(): bool
    {
        return str_starts_with($this->action, 'security.');
    }

    public function isFailedLogin(): bool
    {
        return $this->action === 'auth.failed_login';
    }

    public function isBulkOperation(): bool
    {
        return str_starts_with($this->action, 'bulk.');
    }

    public function isApiRequest(): bool
    {
        return $this->action === 'api.request';
    }

    public function isDatabaseTransaction(): bool
    {
        return str_starts_with($this->action, 'database.');
    }

    public function getMetadataValue(string $key, $default = null)
    {
        return data_get($this->metadata, $key, $default);
    }

    public function setMetadataValue(string $key, $value): void
    {
        $metadata = $this->metadata ?? [];
        data_set($metadata, $key, $value);
        $this->metadata = $metadata;
    }

    public function appendToMetadata(string $key, $value): void
    {
        $metadata = $this->metadata ?? [];
        $currentValue = data_get($metadata, $key, []);

        if (!is_array($currentValue)) {
            $currentValue = [$currentValue];
        }

        $currentValue[] = $value;
        data_set($metadata, $key, $currentValue);
        $this->metadata = $metadata;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_email' => $this->user?->email,
            'action' => $this->action,
            'category' => $this->getActionCategory(),
            'subcategory' => $this->getActionSubcategory(),
            'auditable_type' => $this->auditable_type,
            'auditable_id' => $this->auditable_id,
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
            'changes' => $this->getChanges(),
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'url' => $this->url,
            'method' => $this->method,
            'tags' => $this->tags,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
            'is_security_event' => $this->isSecurityEvent(),
            'is_failed_login' => $this->isFailedLogin(),
            'is_bulk_operation' => $this->isBulkOperation(),
            'is_api_request' => $this->isApiRequest(),
            'is_database_transaction' => $this->isDatabaseTransaction(),
        ];
    }
}
