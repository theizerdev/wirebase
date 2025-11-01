<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MensajeDestinatario extends Model
{
    use HasFactory;

    protected $table = 'mensaje_destinatarios';

    protected $fillable = [
        'mensaje_id',
        'user_id',
        'leido',
        'leido_en',
        'archivado',
        'archivado_en',
        'respondido',
        'respondido_en',
    ];

    protected $casts = [
        'leido' => 'boolean',
        'archivado' => 'boolean',
        'respondido' => 'boolean',
        'leido_en' => 'datetime',
        'archivado_en' => 'datetime',
        'respondido_en' => 'datetime',
    ];

    public function mensaje(): BelongsTo
    {
        return $this->belongsTo(Mensaje::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}