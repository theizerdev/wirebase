<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class PlanPago extends Model
{
    use HasFactory, Multitenantable;

    protected $table = 'plan_pagos';

    protected $fillable = [
        'contrato_id',
        'numero_cuota',
        'tipo_cuota',
        'fecha_vencimiento',
        'fecha_pago_real',
        'monto_capital',
        'monto_interes',
        'monto_total',
        'saldo_pendiente',
        'monto_pagado',
        'mora_calculada',
        'mora_pagada',
        'dias_retraso',
        'estado',
        'empresa_id',
        'sucursal_id'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_pago_real' => 'date',
        'monto_capital' => 'decimal:2',
        'monto_interes' => 'decimal:2',
        'monto_total' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'mora_calculada' => 'decimal:2',
        'mora_pagada' => 'decimal:2',
        'numero_cuota' => 'integer',
        'dias_retraso' => 'integer'
    ];

    // Relaciones
    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }
    
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    
    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente')->orWhere('estado', 'parcial');
    }
    
    public function scopeVencidas($query)
    {
        return $query->where('fecha_vencimiento', '<', now()->toDateString())
                     ->whereIn('estado', ['pendiente', 'parcial']);
    }
}
