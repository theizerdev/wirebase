<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TokenVerificationController extends Controller
{
    /**
     * Verificar token y devolver información del usuario
     */
    public function verifyToken(Request $request)
    {
        try {
            // Validar el request
            $request->validate([
                'token' => 'required|string'
            ]);

            // Verificar API key del header si está configurada
            if ($this->shouldVerifyApiKey()) {
                $apiKey = $request->header('Authorization');
                $expectedKey = 'Bearer ' . env('LARAVEL_API_KEY');

                if ($apiKey !== $expectedKey) {
                    Log::warning('Intento de verificación con API key inválida', [
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'API key inválida'
                    ], 401);
                }
            }

            // Verificar el token
            $user = $this->getUserFromToken($request->token);

            if (!$user) {
                Log::warning('Token inválido o expirado', [
                    'token' => substr($request->token, 0, 10) . '...',
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido o expirado'
                ], 401);
            }

            // Registrar intento exitoso
            Log::info('Token verificado exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            // Devolver información del usuario
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $this->getUserRole($user),
                    'permissions' => $this->getUserPermissions($user)
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al verificar token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al verificar token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener usuario desde el token
     */
    private function getUserFromToken($token)
    {
        Log::info('Buscando usuario por token', ['token_length' => strlen($token)]);

        // Buscar token personal en la base de datos
        $user = User::where('api_token', $token)->first();
        if ($user) {
            Log::info('Usuario encontrado por api_token', ['user_id' => $user->id, 'user_email' => $user->email]);
            return $user;
        }

        Log::warning('Token no encontrado en base de datos');
        return null;
    }

    /**
     * Obtener permisos del usuario
     */
    private function getUserPermissions($user)
    {
        $permissions = [
            'whatsapp.send' => true,
            'whatsapp.receive' => true,
            'whatsapp.manage' => false
        ];

        // Verificar si el usuario tiene permisos administrativos
        if ($user->hasRole('Administrador') || $user->hasRole('Super Administrador')) {
            $permissions['whatsapp.manage'] = true;
        }

        // Verificar permisos específicos de WhatsApp
        if (method_exists($user, 'hasPermissionTo')) {
            try {
                if ($user->hasPermissionTo('whatsapp.manage')) {
                    $permissions['whatsapp.manage'] = true;
                }
            } catch (\Exception $e) {
                // El permiso no existe, continuar con el valor por defecto
            }
        }

        return $permissions;
    }

    /**
     * Obtener rol del usuario
     */
    private function getUserRole($user)
    {
        if (method_exists($user, 'getRoleNames')) {
            $roles = $user->getRoleNames();
            return $roles->first() ?? 'user';
        }

        return $user->role ?? 'user';
    }

    /**
     * Verificar si se debe verificar la API key
     */
    private function shouldVerifyApiKey()
    {
        return !empty(env('LARAVEL_API_KEY'));
    }
}
