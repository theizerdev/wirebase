<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BibliotecaArchivo extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'biblioteca_archivos';

    protected $fillable = [
        'titulo',
        'descripcion',
        'nombre_archivo',
        'ruta_archivo',
        'tamaño',
        'tipo_mime',
        'categoria_id',
        'usuario_subida_id',
        'descargas',
        'visibilidad',
        'empresa_id',
        'sucursal_id',
        'etiquetas',
    ];

    protected $casts = [
        'tamaño' => 'integer',
        'descargas' => 'integer',
        'visibilidad' => 'string',
        'etiquetas' => 'array',
    ];

    const VISIBILIDAD_PUBLICO = 'publico';
    const VISIBILIDAD_PRIVADO = 'privado';
    const VISIBILIDAD_RESTRINGIDO = 'restringido';

    public static function getVisibilidades(): array
    {
        return [
            self::VISIBILIDAD_PUBLICO => 'Público',
            self::VISIBILIDAD_PRIVADO => 'Privado',
            self::VISIBILIDAD_RESTRINGIDO => 'Restringido',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(BibliotecaCategoria::class, 'categoria_id');
    }

    public function usuarioSubida(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_subida_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuariosAutorizados(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'biblioteca_archivo_usuario', 'archivo_id', 'user_id')
            ->withTimestamps();
    }

    public function descargasRegistro()
    {
        return $this->hasMany(BibliotecaDescarga::class, 'archivo_id');
    }

    public function incrementarDescargas(): void
    {
        $this->increment('descargas');
    }

    public function getTamañoFormateadoAttribute(): string
    {
        $bytes = $this->tamaño;
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function scopeForUser($query, $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('empresa_id', $user->empresa_id)
              ->where('sucursal_id', $user->sucursal_id);
        });
    }

    public function scopeVisiblePara($query, $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('visibilidad', self::VISIBILIDAD_PUBLICO)
              ->orWhere('usuario_subida_id', $user->id)
              ->orWhere(function ($q2) use ($user) {
                  $q2->where('visibilidad', self::VISIBILIDAD_RESTRINGIDO)
                     ->whereHas('usuariosAutorizados', function ($q3) use ($user) {
                         $q3->where('user_id', $user->id);
                     });
              });
        });
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopeBusqueda($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('titulo', 'like', "%{$termino}%")
              ->orWhere('descripcion', 'like', "%{$termino}%")
              ->orWhere('etiquetas', 'like', "%{$termino}%")
              ->orWhereHas('categoria', function ($q2) use ($termino) {
                  $q2->where('nombre', 'like', "%{$termino}%");
              });
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Archivo de biblioteca {$eventName}")
            ->useLogName('biblioteca');
    }
}