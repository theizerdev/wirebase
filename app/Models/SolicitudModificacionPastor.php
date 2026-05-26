<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SolicitudModificacionPastor extends Model
{
    use HasFactory;

    protected $table = 'solicitud_modificacion_pastores';

    protected $fillable = [
        'pastor_id',
        'presbitero_user_id',
        'token',
        'datos_solicitados',
        'descripcion_solicitud',
        'estado',
        'aprobado_por',
        'aprobado_en',
        'ip_aprobacion',
        'token_expires_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'aprobado_en' => 'datetime',
        'datos_solicitados' => 'array',
    ];

    /**
     * Relación con el Pastor
     */
    public function pastor()
    {
        return $this->belongsTo(Pastor::class, 'pastor_id');
    }

    /**
     * Relación con el usuario Presbítero (a quien se le envía la solicitud)
     */
    public function presbiteroUser()
    {
        return $this->belongsTo(User::class, 'presbitero_user_id');
    }

    /**
     * Relación con el usuario que aprobó
     */
    public function aprobadoPorUser()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    /**
     * Generar un token único para la solicitud
     */
    public static function generarToken(): string
    {
        return Str::random(64);
    }

    /**
     * Verificar si el token ha expirado
     */
    public function estaExpirada(): bool
    {
        return now()->greaterThan($this->token_expires_at);
    }

    /**
     * Verificar si la solicitud está pendiente
     */
    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Verificar si la solicitud fue aprobada
     */
    public function estaAprobada(): bool
    {
        return $this->estado === 'aprobado';
    }

    /**
     * Verificar si la solicitud fue rechazada
     */
    public function estaRechazada(): bool
    {
        return $this->estado === 'rechazado';
    }

    /**
     * Verificar si la solicitud está completada
     */
    public function estaCompletada(): bool
    {
        return $this->estado === 'completado';
    }

    /**
     * Scope para solicitudes pendientes
     */ 
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para solicitudes por pastor
     */
    public function scopePorPastor($query, $pastorId)
    {
        return $query->where('pastor_id', $pastorId);
    }

    /**
     * Scope para solicitudes no expiradas
     */
    public function scopeNoExpiradas($query)
    {
        return $query->where('token_expires_at', '>', now());
    }
}
