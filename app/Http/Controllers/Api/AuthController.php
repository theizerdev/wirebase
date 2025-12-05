<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Verificar un token de acceso
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyToken(Request $request)
    {
        try {
            // Validar que se proporcione un token
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token requerido',
                    'errors' => $validator->errors()
                ], 400);
            }

            $token = $request->input('token');

            // Verificar que el token coincida con el API key del sistema
            $expectedToken = config('app.whatsapp_api_token', env('WHATSAPP_API_TOKEN'));

            if ($token !== $expectedToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido'
                ], 401);
            }

            // Obtener el primer usuario administrador para simular la autenticación
            $user = User::where('email', 'admin@example.com')->first();

            // Si no existe el usuario admin, crear uno por defecto
            if (!$user) {
                // Buscar un username único
                $username = 'admin';
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = 'admin' . $counter;
                    $counter++;
                }

                $user = User::create([
                    'name' => 'Administrador',
                    'email' => 'admin@example.com',
                    'username' => $username,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Token válido',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => 'admin'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar el token',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
