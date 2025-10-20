<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActiveSession;

class UpdateActiveSessionOnLogout
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
        // Si es una solicitud de cierre de sesión
        if ($request->isMethod('POST') && $request->is('logout')) {
            if (Auth::check()) {
                $user = Auth::user();
                $sessionId = $request->session()->getId();
                
                // Marcar la sesión actual como no actual y registrar la hora de cierre
                $updated = ActiveSession::where('user_id', $user->id)
                    ->where('session_id', $sessionId)
                    ->update([
                        'is_current' => false,
                        'logout_at' => now(),
                        'is_active' => false,
                    ]);
                
                // Si no se actualizó ninguna sesión, puede ser porque se usó un ID de sesión diferente
                // En ese caso, marcar todas las sesiones del usuario como inactivas
                if ($updated === 0) {
                    ActiveSession::where('user_id', $user->id)
                        ->update([
                            'is_current' => false,
                            'logout_at' => now(),
                            'is_active' => false,
                        ]);
                }
            }
        }

        // Procesar la solicitud
        $response = $next($request);
        
        return $response;
    }
}