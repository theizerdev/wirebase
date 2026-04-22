<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Multitenantable;

class MotoUnidad extends Model
{
    use HasFactory, SoftDeletes, Multitenantable;

    protected $table = 'moto_unidades';

    protected $fillable = [
        'moto_id',
        'vin',
        'numero_motor',
        'numero_chasis',
        'placa',
        'color_especifico',
        'kilometraje',
        'costo_compra',
        'precio_venta',
        'estado',
        'condicion',
        'fecha_ingreso',
        'fecha_venta',
        'notas',
        'empresa_id',
        'sucursal_id'
    ];

    protected $casts = [
        'costo_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'fecha_ingreso' => 'date',
        'fecha_venta' => 'date',
        'kilometraje' => 'integer'
    ];

    // Relaciones
    public function moto()
    {
        return $this->belongsTo(Moto::class);
    }
    
    public function contrato()
    {
        return $this->hasOne(Contrato::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    
    public function movements()
    {
        return $this->hasMany(InventoryMovement::class, 'moto_unidad_id')->orderBy('occurred_at', 'desc');
    }
}
