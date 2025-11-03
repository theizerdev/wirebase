<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Mensaje extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'remitente_id',
        'asunto',
        'contenido',
        'prioridad',
        'leido',
        'leido_en',
        'empresa_id',
        'sucursal_id',
    ];

    protected $casts = [
        'leido' => 'boolean',
        'leido_en' => 'datetime',
    ];

    const PRIORIDAD_BAJA = 'baja';
    const PRIORIDAD_MEDIA = 'media';
    const PRIORIDAD_ALTA = 'alta';
    const PRIORIDAD_URGENTE = 'urgente';

    public static function getPrioridades(): array
    {
        return [
            self::PRIORIDAD_BAJA => 'Baja',
            self::PRIORIDAD_MEDIA => 'Media',
            self::PRIORIDAD_ALTA => 'Alta',
            self::PRIORIDAD_URGENTE => 'Urgente',
        ];
    }

    public function remitente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }

    public function destinatarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'mensaje_destinatarios')
            ->withPivot('leido', 'leido_en', 'archivado', 'archivado_en')
            ->withTimestamps();
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function archivos()
    {
        return $this->hasMany(MensajeArchivo::class);
    }

    public function scopeForUser($query, $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('empresa_id', $user->empresa_id)
              ->where('sucursal_id', $user->sucursal_id);
        });
    }

    public function scopeNoLeidos($query, $userId = null)
    {
        if ($userId) {
            return $query->whereHas('destinatarios', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->where('leido', false);
            });
        }
        
        return $query->where('leido', false);
    }

    public function marcarComoLeido($userId): void
    {
        $this->destinatarios()->updateExistingPivot($userId, [
            'leido' => true,
            'leido_en' => now(),
        ]);
    }

    public function esDestinatario($userId): bool
    {
        return $this->destinatarios()->where('user_id', $userId)->exists();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Mensaje {$eventName}")
            ->useLogName('mensajeria');
    }
}