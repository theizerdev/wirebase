<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MensajeArchivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'mensaje_id',
        'nombre_original',
        'nombre_archivo',
        'ruta_archivo',
        'tamaño',
        'tipo_mime',
        'empresa_id',
        'sucursal_id',
    ];

    protected $casts = [
        'tamaño' => 'integer',
    ];

    public function mensaje(): BelongsTo
    {
        return $this->belongsTo(Mensaje::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
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
}