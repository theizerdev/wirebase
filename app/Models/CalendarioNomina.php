<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class CalendarioNomina extends Model
{
    use HasFactory, Multitenantable;

    protected $table = 'calendarios_nomina';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'frecuencia',
        'periodo_inicio',
        'periodo_fin',
        'estado'
    ];
}
