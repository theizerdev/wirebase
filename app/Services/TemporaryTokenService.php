<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TemporaryTokenService
{
    /**
     * Generar token temporal para credenciales
     */
    public static function generateCredentialToken($userId, $username, $password): string
    {
        $token = Str::random(32);
        
        Cache::put("credential_token_{$token}", [
            'user_id' => $userId,
            'username' => $username,
            'password' => $password
        ], 300); // 5 minutos
        
        return $token;
    }
    
    /**
     * Obtener credenciales por token
     */
    public static function getCredentials($token): ?array
    {
        $credentials = Cache::get("credential_token_{$token}");
        
        if ($credentials) {
            Cache::forget("credential_token_{$token}"); // Usar solo una vez
        }
        
        return $credentials;
    }
}