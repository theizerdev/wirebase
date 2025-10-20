<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use App\Models\ActiveSession;
use Illuminate\Support\Facades\Session;

class TrackUserLogout
{
    protected $request;

    /**
     * Create the event listener.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        // Verificar que el usuario exista (protección contra cierres de sesión no válidos)
        if (!$event->user) {
            return;
        }

        $user = $event->user;

        // Verificar si existe una sesión activa
        if (!Session::isStarted()) {
            return;
        }

        $sessionId = $this->request->session()->getId();

        // Marcar la sesión como no actual y registrar la hora de cierre
        ActiveSession::where('user_id', $user->id)
            ->whereDate('created_at', date('Y-m-d'))
            ->where('is_current', true)
            ->update([
                'is_current' => false,
                'logout_at' => now(),
                'is_active' => false,
            ]);
    }
}
