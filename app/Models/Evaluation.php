<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Multitenantable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Evaluation extends Model
{
    use HasFactory, Multitenantable, LogsActivity;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'subject_id',
        'teacher_id',
        'evaluation_period_id',
        'evaluation_type_id',
        'name',
        'description',
        'evaluation_date',
        'max_score',
        'weight',
        'is_active',
        'is_published',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'max_score' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function evaluationPeriod(): BelongsTo
    {
        return $this->belongsTo(EvaluationPeriod::class);
    }

    public function evaluationType(): BelongsTo
    {
        return $this->belongsTo(EvaluationType::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByPeriod($query, $periodId)
    {
        return $query->where('evaluation_period_id', $periodId);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'evaluation_date', 'max_score', 'weight', 'is_active', 'is_published'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getGradedCountAttribute(): int
    {
        return $this->grades()->where('status', 'graded')->count();
    }

    public function getPendingCountAttribute(): int
    {
        return $this->grades()->where('status', 'pending')->count();
    }

    public function getAverageScoreAttribute(): ?float
    {
        return $this->grades()->where('status', 'graded')->avg('score');
    }
}
