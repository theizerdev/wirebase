<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class ConceptoNomina extends Model
{
    use HasFactory, Multitenantable;

    protected $table = 'conceptos_nomina';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'tipo',
        'porcentaje',
        'monto_fijo',
        'activo'
    ];
}
