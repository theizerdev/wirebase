<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $table = 'inventory_movements';

    protected $fillable = [
        'moto_unidad_id',
        'tipo', // entrada, salida, transferencia
        'origen_sucursal_id',
        'destino_sucursal_id',
        'responsable_id',
        'cantidad',
        'observaciones',
        'occurred_at'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'occurred_at' => 'datetime'
    ];

    public function unidad()
    {
        return $this->belongsTo(MotoUnidad::class, 'moto_unidad_id');
    }

    public function origenSucursal()
    {
        return $this->belongsTo(Sucursal::class, 'origen_sucursal_id');
    }

    public function destinoSucursal()
    {
        return $this->belongsTo(Sucursal::class, 'destino_sucursal_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}
