<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $connection = 'mysql';
    protected $table = 'companies';
    
    protected $fillable = [
        'name',
        'apiKey', 
        'webhookUrl',
        'rateLimitPerMinute',
        'isActive'
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'rateLimitPerMinute' => 'integer'
    ];
}