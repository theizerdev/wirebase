<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class Reunion extends Model
{
    use HasFactory, Multitenantable;

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'ubicacion',
        'estado',
        'color',
        'participantes',
        'creado_por',
        'empresa_id',
        'sucursal_id'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'participantes' => 'array'
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public static function getEstados()
    {
        return [
            'programada' => 'Programada',
            'en_curso' => 'En Curso',
            'finalizada' => 'Finalizada',
            'cancelada' => 'Cancelada'
        ];
    }
}