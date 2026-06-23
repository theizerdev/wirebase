<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'actividad_id',
        'pastor_id',
        'metodo',
        'fecha_hora'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime'
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

    public function pastor()
    {
        return $this->belongsTo(Pastor::class);
    }
}
