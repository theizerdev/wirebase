<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSpanishActivityLog;
use App\Traits\Multitenantable;

class Zona extends Model
{
    use HasFactory, LogsActivity, HasSpanishActivityLog, Multitenantable;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'distrito',
        'empresa_id',
        'sucursal_id',
        'activo',
    ];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Obtener los usuarios asignados a esta zona.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_zona')
            ->withTimestamps();
    }

    /**
     * Obtener la empresa propietaria de la zona.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Obtener la sucursal asociada a la zona.
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    /**
     * Scope para filtrar zonas activas.
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para filtrar por empresa.
     */
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para filtrar por sucursal.
     */
    public function scopePorSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    /**
     * Scope para filtrar por distrito.
     */
    public function scopePorDistrito($query, $distrito)
    {
        return $query->where('distrito', $distrito);
    }

    /**
     * Scope para buscar zonas por nombre o código.
     */
    public function scopeBuscar($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nombre', 'like', '%' . $search . '%')
              ->orWhere('codigo', 'like', '%' . $search . '%')
              ->orWhere('descripcion', 'like', '%' . $search . '%')
              ->orWhere('distrito', 'like', '%' . $search . '%');
        });
    }

    /**
     * Scope para zonas globales (sin sucursal específica).
     */
    public function scopeGlobales($query)
    {
        return $query->whereNull('sucursal_id');
    }

    /**
     * Scope para zonas específicas de sucursal.
     */
    public function scopePorSucursales($query)
    {
        return $query->whereNotNull('sucursal_id');
    }

    /**
     * Scope para ordenar por nombre.
     */
    public function scopeOrdenarPorNombre($query, $direction = 'asc')
    {
        return $query->orderBy('nombre', $direction);
    }

    /**
     * Scope para contar usuarios asignados.
     */
    public function scopeConContadorUsuarios($query)
    {
        return $query->withCount('users');
    }

    /**
     * Opciones de registro de actividad.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'codigo', 'descripcion', 'activo'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => static::getSpanishDescription($eventName));
    }
}
