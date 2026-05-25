<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Ciudad extends Model
{
    use HasFactory, LogsActivity;


    protected $table = 'ciudades';

    protected $fillable = [
        'nombre',
        'estado_id',
        'latitud',
        'longitud',
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    /**
     * Relación con estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    /**
     * Relación con empresas
     */
    public function empresas()
    {
        return $this->hasMany(Empresa::class);
    }

    /**
     * Relación con sucursales
     */
    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }

    /**
     * Relación con usuarios
     */
    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'estado_id', 'latitud', 'longitud'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}