<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMessage extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'message_id',
        'template_id',
        'recipient_phone',
        'recipient_name',
        'message_content',
        'variables',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
        'error_message',
        'direction',
        'created_by',
        'cost',
        'metadata',
        'retry_count'
    ];

    protected $casts = [
        'variables' => 'array',
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'cost' => 'decimal:4',
        'retry_count' => 'integer'
    ];

    protected $attributes = [
        'status' => 'pending',
        'direction' => 'outbound',
        'retry_count' => 0
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(WhatsAppTemplate::class, 'template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOutbound($query)
    {
        return $query->where('direction', 'outbound');
    }

    public function scopeInbound($query)
    {
        return $query->where('direction', 'inbound');
    }

    public function scopeRetryable($query)
    {
        return $query->where(function ($query) {
            // Mensajes fallidos o con errores
            $query->where('status', 'failed')
                  ->orWhereNotNull('error_message');
                      
            // Mensajes simulados (éxito pero sin message_id real)
            $query->orWhere(function ($q) {
                $q->where('status', 'sent')
                  ->whereNull('message_id')
                  ->orWhere('message_id', 'like', 'msg_%'); // IDs generados internamente
            });
        })
        ->where('retry_count', '<', 3); // No exceder el máximo de reintentos
    }

    public function scopeSimulated($query)
    {
        return $query->where('status', 'sent')
                    ->where(function ($q) {
                        $q->whereNull('message_id')
                          ->orWhere('message_id', 'like', 'msg_%');
                    });
    }

    public function scopeWithErrors($query)
    {
        return $query->where(function ($query) {
            $query->where('status', 'failed')
                  ->orWhereNotNull('error_message');
        });
    }

    public function scopeRetried($query)
    {
        return $query->whereNotNull('metadata->retried_at');
    }

    public function scopeMaxRetriesExceeded($query)
    {
        return $query->where('retry_count', '>=', 3);
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month);
    }

    public function isSent(): bool
    {
        return in_array($this->status, ['sent', 'delivered', 'read']);
    }

    public function isDelivered(): bool
    {
        return in_array($this->status, ['delivered', 'read']);
    }

    public function isRead(): bool
    {
        return $this->status === 'read';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isSimulated(): bool
    {
        return $this->status === 'sent' && 
               (!$this->message_id || str_starts_with($this->message_id, 'msg_'));
    }

    public function isRetryable(): bool
    {
        if ($this->direction !== 'outbound') {
            return false;
        }

        // Verificar si es fallido o simulado
        $isFailedOrSimulated = $this->isFailed() || 
                              $this->isSimulated() || 
                              !empty($this->error_message);

        if (!$isFailedOrSimulated) {
            return false;
        }

        // Verificar límite de reintentos
        $retryCount = $this->metadata['retry_count'] ?? 0;
        return $retryCount < 3;
    }

    public function getRetryCount(): int
    {
        return $this->retry_count ?? 0;
    }

    public function incrementRetryCount(): void
    {
        $this->increment('retry_count');
    }

    public function markAsRetried(array $result): void
    {
        $metadata = $this->metadata ?? [];
        
        if ($result['success']) {
            // Si el reenvío fue exitoso, actualizar el mensaje
            $this->update([
                'status' => 'sent',
                'message_id' => $result['message_id'] ?? $this->message_id,
                'error_message' => null,
                'sent_at' => now(),
                'metadata' => array_merge($metadata, [
                    'retried_at' => now()->toDateTimeString(),
                    'retry_count' => ($metadata['retry_count'] ?? 0) + 1,
                    'original_status' => $this->status,
                    'original_error' => $this->error_message,
                    'retry_successful' => true
                ])
            ]);
        } else {
            // Si falló, solo incrementar el contador y registrar el error
            $this->update([
                'metadata' => array_merge($metadata, [
                    'retried_at' => now()->toDateTimeString(),
                    'retry_count' => ($metadata['retry_count'] ?? 0) + 1,
                    'last_retry_error' => $result['message'] ?? 'Error desconocido',
                    'retry_successful' => false
                ])
            ]);
        }
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);
    }

    public function markAsRead(): void
    {
        $this->update([
            'status' => 'read',
            'read_at' => now()
        ]);
    }

    public function markAsFailed(string $errorMessage = null): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }

    public function getDeliveryTimeAttribute(): ?string
    {
        if ($this->delivered_at && $this->sent_at) {
            return $this->delivered_at->diffInSeconds($this->sent_at) . 's';
        }
        return null;
    }

    public function getReadTimeAttribute(): ?string
    {
        if ($this->read_at && $this->delivered_at) {
            return $this->read_at->diffInSeconds($this->delivered_at) . 's';
        }
        return null;
    }
}