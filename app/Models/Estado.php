<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Estado extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'nombre',
        'iso_3166_2',
        'latitud',
        'longitud',
    ];

    protected $casts = [
        //
    ];

    /**
     * Relación con municipios
     */
    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }

    /**
     * Relación con ciudades
     */
    public function ciudades()
    {
        return $this->hasMany(Ciudad::class);
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

    /**
     * Relación con iglesias
     */
    public function iglesias()
    {
        return $this->hasMany(Iglesia::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'iso_3166_2'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}