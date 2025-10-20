<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActiveSession;

class HandleLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Procesar la solicitud
        $response = $next($request);
        
        // Verificar si hay un usuario autenticado
        if (Auth::check()) {
            $user = Auth::user();
            $sessionId = $request->session()->getId();
            
            // Verificar si la sesión actual está marcada como current
            $activeSession = ActiveSession::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->where('is_current', true)
                ->first();
                
            // Si no existe una sesión marcada como current, crearla o actualizarla
            if (!$activeSession) {
                // Verificar si existe la sesión sin el filtro de is_current
                $session = ActiveSession::where('user_id', $user->id)
                    ->where('session_id', $sessionId)
                    ->first();
                    
                if ($session) {
                    $session->update(['is_current' => true]);
                } else {
                    // Crear una nueva sesión si no existe
                    ActiveSession::create([
                        'user_id' => $user->id,
                        'session_id' => $sessionId,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'last_activity' => now(),
                        'is_current' => true,
                    ]);
                }
            }
        }

        return $response;
    }
}