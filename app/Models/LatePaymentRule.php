<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class LatePaymentRule extends Model
{
    use Multitenantable;

    protected $fillable = [
        'nombre',
        'tipo',
        'valor',
        'dias_gracia',
        'activo',
        'empresa_id',
        'sucursal_id'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'activo' => 'boolean'
    ];

    public function calcularRecargo($montoOriginal, $diasVencido)
    {
        if ($diasVencido <= $this->dias_gracia) {
            return 0;
        }

        return match($this->tipo) {
            'porcentaje' => $montoOriginal * ($this->valor / 100),
            'monto_fijo' => $this->valor,
            default => 0
        };
    }

    public static function getActiveRule()
    {
        return static::where('activo', true)
            ->where('empresa_id', auth()->user()->empresa_id)
            ->where('sucursal_id', auth()->user()->sucursal_id)
            ->first();
    }
}