<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SorteoContratoGanador extends Model
{
    protected $table = 'sorteo_contratos_ganadores';

    protected $fillable = [
        'sorteo_id', 'contrato_id', 'numero_contrato',
        'fecha_ganador', 'hash_verificacion', 'empresa_id'
    ];

    protected $casts = [
        'fecha_ganador' => 'datetime',
    ];

    public function sorteo()
    {
        return $this->belongsTo(Sorteo::class);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
