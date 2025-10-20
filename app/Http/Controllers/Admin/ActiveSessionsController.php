<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActiveSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActiveSessionsController extends Controller
{
    /**
     * Display a listing of the active sessions.
     */
    public function index()
    {
        // Obtener todas las sesiones del usuario actual
        $activeSessions = ActiveSession::where('user_id', Auth::id())
            ->orderBy('last_activity', 'desc')
            ->get();

        return view('admin.active-sessions.index', compact('activeSessions'));
    }

    /**
     * Terminate a specific session.
     */
    public function destroy($id)
    {
        $session = ActiveSession::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if ($session) {
            // Si es la sesión actual, no la eliminamos, solo la marcamos como inactiva
            if ($session->is_current) {
                $session->update([
                    'is_current' => false,
                    'is_active' => false,
                    'logout_at' => now(),
                ]);
                
                return redirect()->back()->with('status', 'Sesión actual cerrada.');
            } else {
                // Para otras sesiones, las marcamos como inactivas
                $session->update([
                    'is_active' => false,
                    'logout_at' => now(),
                ]);
                
                return redirect()->back()->with('status', 'Sesión terminada exitosamente.');
            }
        }

        return redirect()->back()->with('error', 'Sesión no encontrada.');
    }
}