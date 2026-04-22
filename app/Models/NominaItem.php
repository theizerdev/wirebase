<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NominaItem extends Model
{
    use HasFactory;

    protected $table = 'nomina_items';

    protected $fillable = [
        'nomina_id',
        'empleado_id',
        'concepto_nombre',
        'tipo',
        'cantidad',
        'monto_unitario',
        'subtotal'
    ];
}
