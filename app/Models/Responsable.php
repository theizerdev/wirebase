<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Responsable extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'nombre_completo',
        'cedula',
        'estado_id',
        'municipio_id',
        'parroquia_id',
        'telefono',
        'direccion',
        'punto_referencia',
        'fecha_levantamiento',
        'codigo_casa_alimentacion',
        'casa_alimentacion_id',
        'empresa_id',
        'sucursal_id',
    ];

    protected $casts = [
        'fecha_levantamiento' => 'date',
        'estado_id' => 'integer',
        'municipio_id' => 'integer',
        'parroquia_id' => 'integer',
        'casa_alimentacion_id' => 'integer',
        'empresa_id' => 'integer',
        'sucursal_id' => 'integer',
    ];

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

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function casaAlimentacion()
    {
        return $this->belongsTo(CasaAlimentacion::class, 'casa_alimentacion_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre_completo', 'cedula', 'estado_id', 'municipio_id', 'parroquia_id', 'telefono', 'direccion', 'punto_referencia', 'fecha_levantamiento', 'codigo_casa_alimentacion', 'casa_alimentacion_id', 'empresa_id', 'sucursal_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
