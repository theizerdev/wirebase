<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BibliotecaDescarga extends Model
{
    use HasFactory;

    protected $table = 'biblioteca_descargas';
    public $timestamps = false;

    protected $fillable = [
        'archivo_id',
        'usuario_id',
        'ip_address',
        'user_agent',
        'descargado_en',
    ];

    protected $casts = [
        'descargado_en' => 'datetime',
    ];

    public function archivo(): BelongsTo
    {
        return $this->belongsTo(BibliotecaArchivo::class, 'archivo_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function scopeForUser($query, $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->whereHas('archivo', function ($q2) use ($user) {
                $q2->where('empresa_id', $user->empresa_id)
                   ->where('sucursal_id', $user->sucursal_id);
            });
        });
    }
}