<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class Nomina extends Model
{
    use HasFactory, Multitenantable;

    protected $table = 'nominas';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'calendario_id',
        'periodo_inicio',
        'periodo_fin',
        'estado',
        'total'
    ];

    public function items()
    {
        return $this->hasMany(NominaItem::class);
    }
}
