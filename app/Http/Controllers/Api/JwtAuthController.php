<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // No aplicar middleware de auth al constructor
        // El middleware se aplicará solo a métodos específicos
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        // Buscar el usuario primero
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        // Generar token JWT directamente
        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token);
    }

    /**
     * Verificar token JWT
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyToken(Request $request)
    {
        $token = $request->bearerToken() ?: $request->input('token');

        if (!$token) {
            return response()->json([
                'message' => 'Token requerido'
            ], 400);
        }

        try {
            // Intentar decodificar el token
            $payload = JWTAuth::parseToken()->getPayload();
            $userId = $payload->get('sub');

            // Obtener el usuario
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            return response()->json([
                'message' => 'Token válido',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'role' => $user->getRoleNames()->first() ?? 'user'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token inválido',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token inválido',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            JWTAuth::parseToken()->invalidate();
            return response()->json(['message' => 'Successfully logged out']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();
            return $this->respondWithToken($newToken);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al refrescar token',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string  $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
