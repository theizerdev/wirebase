<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Multitenantable;

class EvaluationType extends Model
{
    use HasFactory, Multitenantable;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'name',
        'code',
        'default_weight',
        'description',
        'is_active',
    ];

    protected $casts = [
        'default_weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
