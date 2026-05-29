<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialVerificacionPastor extends Model
{
    protected $table = 'historial_verificaciones_pastores';
    
    protected $fillable = [
        'pastor_id',
        'ip_address',
        'user_agent',
        'metodo_verificacion',
        'exitoso',
        'pregunta_mostrada',
        'verificado_en',
    ];
    
    protected $casts = [
        'exitoso' => 'boolean',
        'verificado_en' => 'datetime',
    ];
    
    /**
     * Relación con el pastor
     */
    public function pastor(): BelongsTo
    {
        return $this->belongsTo(Pastor::class);
    }
}
