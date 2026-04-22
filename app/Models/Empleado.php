<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class Empleado extends Model
{
    use HasFactory, Multitenantable;

    protected $table = 'empleados';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'nombre',
        'apellido',
        'documento',
        'puesto',
        'salario_base',
        'horas_extra_base',
        'bono_fijo',
        'comision_fija',
        'metodo_pago',
        'telefono',
        'email',
        'activo'
    ];
}
