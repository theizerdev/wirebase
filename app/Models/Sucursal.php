<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSpanishActivityLog;

class Sucursal extends Model
{
    use HasFactory, LogsActivity, HasSpanishActivityLog;

    protected $table = 'sucursales';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'telefono',
        'direccion',
        'latitud',
        'longitud',
        'status',
        'estado_id',
        'municipio_id',
        'parroquia_id'
    ];

    protected $casts = [
        'status' => 'boolean',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'estado_id' => 'integer',
        'municipio_id' => 'integer',
        'parroquia_id' => 'integer',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class);
    }

    public function scopeForUser($query)
    {
        if (auth()->check() && !auth()->user()->hasRole('Super Administrador')) {
            // Los usuarios ven todas las sucursales de su empresa
            if (auth()->user()->empresa_id) {
                $query->where('empresa_id', auth()->user()->empresa_id);
            }
            // NO filtrar por sucursal_id del usuario - eso limitaría demasiado
            // El sucursal_id del usuario es para saber en qué sucursal trabaja,
            // no para limitar qué sucursales puede ver
        }
        return $query;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'telefono', 'direccion', 'status', 'estado_id', 'municipio_id', 'parroquia_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => static::getSpanishDescription($eventName));
    }
}