<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Actividad extends Model
{
    use HasFactory, Multitenantable, LogsActivity;

    protected $table = 'actividades';

    protected $fillable = [
        'nombre',
        'tipo_actividad',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'zona',
        'distrito',
        'lugar',
        'nota',
        'coordinador_id',
        'empresa_id',
        'sucursal_id',
        'user_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'zona' => 'integer',
        'distrito' => 'integer',
    ];

    public function coordinador()
    {
        return $this->belongsTo(Pastor::class, 'coordinador_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
