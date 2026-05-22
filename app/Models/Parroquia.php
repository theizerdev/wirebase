<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSpanishActivityLog;

class Parroquia extends Model
{
    use HasFactory, LogsActivity, HasSpanishActivityLog;

    protected $table = 'parroquias';

    protected $fillable = [
        'nombre',
        'municipio_id',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'municipio_id', 'activo'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => static::getSpanishDescription($eventName));
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorMunicipio($query, $municipioId)
    {
        return $query->where('municipio_id', $municipioId);
    }

    public function scopeBuscar($query, $search)
    {
        return $query->where('nombre', 'like', '%' . $search . '%');
    }
}