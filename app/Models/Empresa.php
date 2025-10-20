<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'razon_social',
        'documento',
        'direccion',
        'latitud',
        'longitud',
        'representante_legal',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }
}