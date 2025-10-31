<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class PaymentSchedule extends Model
{
    use HasFactory, Multitenantable;

    protected $fillable = [
        'matricula_id',
        'numero_cuota',
        'monto',
        'monto_pagado',
        'fecha_vencimiento',
        'estado',
        'empresa_id',
        'sucursal_id'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'fecha_vencimiento' => 'date'
    ];

    protected $attributes = [
        'estado' => 'pendiente',
        'monto_pagado' => 0
    ];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }
}
