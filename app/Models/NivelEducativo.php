<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NivelEducativo extends Model
{
    use SoftDeletes;

    protected $table = 'niveles_educativos';

    protected $fillable = [
        'nombre',
        'costo',
        'cuotas'
    ];

    protected $casts = [
        'costo' => 'decimal:2',
    ];
}
