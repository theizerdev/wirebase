<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Moto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'marca',
        'modelo',
        'anio',
        'color_principal',
        'cilindrada',
        'tipo',
        'descripcion',
        'precio_venta_base',
        'costo_referencial',
        'imagen_url',
        'activo',
        'empresa_id'
    ];

    protected $casts = [
        'precio_venta_base' => 'decimal:2',
        'costo_referencial' => 'decimal:2',
        'activo' => 'boolean',
        'anio' => 'integer'
    ];

    // Relaciones
    public function unidades()
    {
        return $this->hasMany(MotoUnidad::class);
    }
    
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    
    public function getTituloAttribute()
    {
        return "{$this->marca} {$this->modelo} ({$this->anio})";
    }
}
