<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WhatsAppRateLimiter
{
    private const MAX_MESSAGES_PER_MINUTE = 20;
    private const MAX_MESSAGES_PER_HOUR = 1000;
    
    /**
     * Verificar si se puede enviar mensaje
     */
    public static function canSendMessage($identifier = 'global'): bool
    {
        $minuteKey = "whatsapp_rate_limit_minute_{$identifier}_" . now()->format('Y-m-d_H:i');
        $hourKey = "whatsapp_rate_limit_hour_{$identifier}_" . now()->format('Y-m-d_H');
        
        $minuteCount = Cache::get($minuteKey, 0);
        $hourCount = Cache::get($hourKey, 0);
        
        if ($minuteCount >= self::MAX_MESSAGES_PER_MINUTE) {
            Log::warning('Rate limit exceeded per minute', ['identifier' => $identifier]);
            return false;
        }
        
        if ($hourCount >= self::MAX_MESSAGES_PER_HOUR) {
            Log::warning('Rate limit exceeded per hour', ['identifier' => $identifier]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Registrar envío de mensaje
     */
    public static function recordMessage($identifier = 'global'): void
    {
        $minuteKey = "whatsapp_rate_limit_minute_{$identifier}_" . now()->format('Y-m-d_H:i');
        $hourKey = "whatsapp_rate_limit_hour_{$identifier}_" . now()->format('Y-m-d_H');
        
        Cache::increment($minuteKey, 1);
        Cache::put($minuteKey, Cache::get($minuteKey), 60); // 1 minute TTL
        
        Cache::increment($hourKey, 1);
        Cache::put($hourKey, Cache::get($hourKey), 3600); // 1 hour TTL
    }
    
    /**
     * Obtener tiempo de espera hasta próximo envío
     */
    public static function getWaitTime($identifier = 'global'): int
    {
        $minuteKey = "whatsapp_rate_limit_minute_{$identifier}_" . now()->format('Y-m-d_H:i');
        $minuteCount = Cache::get($minuteKey, 0);
        
        if ($minuteCount >= self::MAX_MESSAGES_PER_MINUTE) {
            return 60 - now()->second; // Seconds until next minute
        }
        
        return 0;
    }
}