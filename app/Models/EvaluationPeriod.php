<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Multitenantable;

class EvaluationPeriod extends Model
{
    use HasFactory, Multitenantable;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'school_period_id',
        'name',
        'number',
        'start_date',
        'end_date',
        'weight',
        'is_active',
        'is_closed',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'is_closed' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function schoolPeriod(): BelongsTo
    {
        return $this->belongsTo(SchoolPeriod::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function gradeSummaries(): HasMany
    {
        return $this->hasMany(GradeSummary::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    public function scopeBySchoolPeriod($query, $schoolPeriodId)
    {
        return $query->where('school_period_id', $schoolPeriodId);
    }
}
