<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSpanishActivityLog;

class Estado extends Model
{
    use HasFactory, LogsActivity, HasSpanishActivityLog;

    protected $table = 'estados';

    protected $fillable = [
        'nombre',
        'codigo',
        'pais_id',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }

    public function ciudades()
    {
        return $this->hasMany(Ciudad::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'codigo', 'pais_id', 'activo'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => static::getSpanishDescription($eventName));
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorPais($query, $paisId)
    {
        return $query->where('pais_id', $paisId);
    }

    public function scopeBuscar($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nombre', 'like', '%' . $search . '%')
              ->orWhere('codigo', 'like', '%' . $search . '%');
        });
    }
}
