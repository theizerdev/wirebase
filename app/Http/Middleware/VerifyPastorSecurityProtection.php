<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Pastor;

class VerifyPastorSecurityProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener el pastor de la ruta
        $pastor = $request->route('pastor');
        
        if (!$pastor || !$pastor instanceof Pastor) {
            return $next($request);
        }

        // Verificar si el pastor tiene protección activada
        if (!$pastor->preguntasSeguridad || !$pastor->preguntasSeguridad->activado) {
            // No tiene protección activada
            // Si hay una solicitud aprobada pendiente, redirigir a configurar seguridad
            $solicitudAprobada = $pastor->solicitudesModificacion()
                ->where('estado', 'aprobado')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($solicitudAprobada) {
                return redirect()->route('pastor.configurar-seguridad', $pastor->id)
                    ->with('warning', 'Debe configurar su seguridad antes de continuar.');
            }
            
            // Si no hay solicitud aprobada, permitir acceso normal
            return $next($request);
        }

        // Verificar si está bloqueado por intentos fallidos
        if ($pastor->preguntasSeguridad->estaBloqueado()) {
            $tiempoRestante = now()->diffInMinutes($pastor->preguntasSeguridad->ultimo_intento->addMinutes(15));
            
            return redirect()->back()
                ->with('error', "Demasiados intentos fallidos. Intente nuevamente en {$tiempoRestante} minutos.");
        }

        // Protección activada y no bloqueado - permitir acceso
        return $next($request);
    }
}
