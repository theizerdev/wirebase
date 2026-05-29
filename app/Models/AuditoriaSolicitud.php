<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditoriaSolicitud extends Model
{
    protected $table = 'auditoria_solicitudes';
    
    protected $fillable = [
        'solicitud_id',
        'accion',
        'usuario_id',
        'usuario_nombre',
        'usuario_rol',
        'ip_address',
        'user_agent',
        'metadata',
    ];
    
    protected $casts = [
        'metadata' => 'array',
    ];
    
    /**
     * Relación con la solicitud
     */
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(SolicitudModificacionPastor::class, 'solicitud_id');
    }
    
    /**
     * Relación con el usuario (si aplica)
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }
    
    /**
     * Scope para filtrar por tipo de acción
     */
    public function scopeAccion($query, string $accion)
    {
        return $query->where('accion', $accion);
    }
    
    /**
     * Scope para filtrar por solicitud
     */
    public function scopePorSolicitud($query, int $solicitudId)
    {
        return $query->where('solicitud_id', $solicitudId);
    }
}
