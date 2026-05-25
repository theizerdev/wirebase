<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Parroquia extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'nombre',
        'municipio_id',
        'latitud',
        'longitud',
    ];

    protected $casts = [
        //
    ];

    /**
     * Relación con municipio
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
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
            ->logOnly(['nombre', 'municipio_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}