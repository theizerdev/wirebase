<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Multitenantable;

class Contrato extends Model
{
    use HasFactory, SoftDeletes, Multitenantable;

    protected $fillable = [
        'numero_contrato',
        'cliente_id',
        'moto_unidad_id',
        'vendedor_id',
        'empresa_id',
        'sucursal_id',
        'fecha_inicio',
        'fecha_fin_estimada',
        'precio_venta_final',
        'cuota_inicial',
        'monto_financiado',
        'tasa_interes_anual',
        'plazo_semanas',
        'plazo_meses',
        'dia_pago_mensual',
        'frecuencia_pago',
        'estado',
        'saldo_pendiente',
        'cuotas_pagadas',
        'cuotas_totales',
        'cuotas_vencidas',
        'observaciones'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin_estimada' => 'date',
        'precio_venta_final' => 'decimal:2',
        'cuota_inicial' => 'decimal:2',
        'monto_financiado' => 'decimal:2',
        'tasa_interes_anual' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'plazo_semanas' => 'integer',
        'plazo_meses' => 'integer',
        'dia_pago_mensual' => 'integer',
        'cuotas_pagadas' => 'integer',
        'cuotas_totales' => 'integer',
        'cuotas_vencidas' => 'integer'
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    
    public function unidad()
    {
        return $this->belongsTo(MotoUnidad::class, 'moto_unidad_id');
    }
    
    public function planPagos()
    {
        return $this->hasMany(PlanPago::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }
    
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    
    // Scopes
    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['activo', 'mora']);
    }
    
    public function scopeVencidos($query)
    {
        return $query->where('cuotas_vencidas', '>', 0);
    }
}
