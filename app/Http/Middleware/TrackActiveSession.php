<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrackActiveSession
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
        // Este middleware ha sido deshabilitado y ya no realiza ninguna acción
        return $next($request);
    }
}
