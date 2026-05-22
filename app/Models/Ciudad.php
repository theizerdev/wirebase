<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSpanishActivityLog;
use App\Models\Estado;

class Ciudad extends Model
{
    use HasFactory, LogsActivity, HasSpanishActivityLog;

    protected $table = 'ciudades';

    protected $fillable = [
        'nombre',
        'codigo',
        'estado_id',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    // Remover la relación con municipios ya que ahora municipios se relaciona directamente con estado
    // public function municipios()
    // {
    //     return $this->hasMany(Municipio::class);
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'codigo', 'estado_id', 'activo'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => static::getSpanishDescription($eventName));
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorEstado($query, $estadoId)
    {
        return $query->where('estado_id', $estadoId);
    }

    public function scopeBuscar($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nombre', 'like', '%' . $search . '%')
              ->orWhere('codigo', 'like', '%' . $search . '%');
        });
    }
}