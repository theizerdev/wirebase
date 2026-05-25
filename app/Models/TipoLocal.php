<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TipoLocal extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'tipo_locales';

    protected $fillable = [
        'nombre',
        'descripcion',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Relación con iglesias que usan este tipo de local
     */
    public function iglesias()
    {
        return $this->hasMany(Iglesia::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'descripcion', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}