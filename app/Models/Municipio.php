<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSpanishActivityLog;

class Municipio extends Model
{
    use HasFactory, LogsActivity, HasSpanishActivityLog;

    protected $table = 'municipios';

    protected $fillable = [
        'nombre',
        'estado_id',  // Cambiado de ciudad_id a estado_id
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    // Mantener la relación con parroquias
    public function parroquias()
    {
        return $this->hasMany(Parroquia::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'estado_id', 'activo'])  // Actualizado para reflejar el cambio
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => static::getSpanishDescription($eventName));
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorEstado($query, $estadoId)  // Actualizado el nombre del scope
    {
        return $query->where('estado_id', $estadoId);  // Actualizado para reflejar el cambio
    }

    public function scopeBuscar($query, $search)
    {
        return $query->where('nombre', 'like', '%' . $search . '%');
    }
}