<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppTemplate extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_templates';

    protected $fillable = [
        'name',
        'description',
        'content',
        'variables',
        'category',
        'is_active',
        'created_by',
        'usage_count',
        'last_used_at'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'usage_count' => 'integer'
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'template_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    public function getProcessedContent(array $replacements = []): string
    {
        $content = $this->content;
        
        foreach ($replacements as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }
        
        return $content;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function getVariablesListAttribute(): array
    {
        preg_match_all('/\{\{([^}]+)\}\}/', $this->content, $matches);
        return $matches[1] ?? [];
    }
}