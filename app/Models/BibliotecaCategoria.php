<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BibliotecaCategoria extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'biblioteca_categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'icono',
        'padre_id',
        'empresa_id',
        'sucursal_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function padre(): BelongsTo
    {
        return $this->belongsTo(BibliotecaCategoria::class, 'padre_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(BibliotecaCategoria::class, 'padre_id');
    }

    public function archivos(): HasMany
    {
        return $this->hasMany(BibliotecaArchivo::class, 'categoria_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeForUser($query, $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('empresa_id', $user->empresa_id)
              ->where('sucursal_id', $user->sucursal_id);
        });
    }

    public function scopeRaices($query)
    {
        return $query->whereNull('padre_id');
    }

    public function getHijosRecursivos()
    {
        $hijos = $this->hijos;
        
        foreach ($this->hijos as $hijo) {
            $hijos = $hijos->merge($hijo->getHijosRecursivos());
        }
        
        return $hijos;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Categoría de biblioteca {$eventName}")
            ->useLogName('biblioteca');
    }
}