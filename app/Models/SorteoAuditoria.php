<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SorteoAuditoria extends Model
{
    protected $table = 'sorteo_auditoria';

    protected $fillable = [
        'sorteo_id', 'accion', 'detalle', 'ip_address',
        'user_agent', 'ejecutado_por', 'empresa_id'
    ];

    protected $casts = [
        'detalle' => 'array',
    ];

    public function sorteo()
    {
        return $this->belongsTo(Sorteo::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
