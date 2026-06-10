<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\ActiveSession;
use App\Models\AuditLog;
use Ramsey\Uuid\Uuid;

class Logout extends Component
{
    public function logout()
    {
        try {
            $user = Auth::user();

            // Registrar evento de seguridad antes de cerrar sesión
            if ($user) {
                $this->registrarEventoSeguridad('Cierre de sesión', [
                    'identificador' => $user->email ?? $user->username,
                    'user_id' => $user->id,
                    'usuario_nombre' => $user->name,
                ], 'logout');

                // Desactivar sesión activa
                ActiveSession::where('user_id', $user->id)
                    ->where('is_current', true)
                    ->update([
                        'is_current' => false,
                        'is_active' => false,
                        'logout_at' => now(),
                    ]);
            }

            // Cerrar sesión
            Auth::logout();

            // Invalidar la sesión y regenerar token
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            // Redirigir al login con mensaje
            return redirect()->route('login')->with('status', 'Sesión cerrada exitosamente.');
        } catch (\Exception $e) {
            \Log::error('Error during logout: ' . $e->getMessage());
            // Si hay error, igual intentar redirigir al login
            return redirect()->route('login');
        }
    }

    private function registrarEventoSeguridad(string $descripcion, array $datos = [], string $tipo = 'seguridad'): void
    {
        try {
            AuditLog::create([
                'id' => Uuid::uuid4()->toString(),
                'user_id' => $datos['user_id'] ?? null,
                'action' => "seguridad.{$tipo}",
                'auditable_type' => 'EventoSeguridad',
                'auditable_id' => $datos['user_id'] ?? 0,
                'old_values' => [],
                'new_values' => $datos,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'tags' => ['seguridad', $tipo, 'auth'],
                'metadata' => [
                    'descripcion' => $descripcion,
                    'tipo_evento' => $tipo,
                    'fecha_hora' => now()->format('Y-m-d H:i:s'),
                    'ip' => request()->ip(),
                ],
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error registrando evento de seguridad: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.logout');
    }
}
