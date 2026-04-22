<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sorteo extends Model
{
    protected $fillable = [
        'nombre', 'fecha_sorteo', 'numero_contrato_ganador',
        'hash_validacion', 'total_contratos_elegibles',
        'ejecutado_por', 'empresa_id', 'estado'
    ];

    protected $casts = [
        'fecha_sorteo' => 'datetime',
    ];

    public function ganador()
    {
        return $this->hasOne(SorteoContratoGanador::class);
    }

    public function auditorias()
    {
        return $this->hasMany(SorteoAuditoria::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function ejecutadoPor()
    {
        return $this->belongsTo(User::class, 'ejecutado_por');
    }
}
