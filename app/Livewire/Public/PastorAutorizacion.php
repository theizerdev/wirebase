<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\SolicitudModificacionPastor;
use App\Services\PastorAuthorizationService;
use Illuminate\Support\Facades\Auth;

class PastorAutorizacion extends Component
{
    public $token;
    public $solicitud;
    public $pastor;
    public $presbitero;
    public $accion = 'ver'; // ver, aprobar, rechazar
    public $motivoRechazo = '';
    public $error = '';
    public $success = '';
    public $autorizado = false;

    protected $rules = [
        'motivoRechazo' => 'nullable|string|max:500',
    ];

    public function mount($token)
    {
        $this->token = $token;

        // Buscar la solicitud
        $service = app(PastorAuthorizationService::class);
        $this->solicitud = $service->obtenerSolicitudPorToken($token);

        if (!$this->solicitud) {
            abort(404, 'Solicitud no encontrada o expirada.');
        }
        //dd($this->solicitud->presbitero_user_id !== \App\Models\User::find($this->solicitud->presbitero_user_id)->id);
        // Verificar que el presbítero sea el correcto
        if ($this->solicitud->presbitero_user_id !== \App\Models\User::find($this->solicitud->presbitero_user_id)->id) {
            abort(403, 'Esta solicitud no corresponde a su cuenta.');
        }

        $this->pastor = $this->solicitud->pastor;
        $this->presbitero = Auth::user();
    }

    /**
     * Aprobar la solicitud
     */
    public function aprobar()
    {
        try {
            $service = app(PastorAuthorizationService::class);
            $service->aprobarSolicitud(
                $this->solicitud,
                 \App\Models\User::find($this->solicitud->presbitero_user_id),
                request()->ip()
            );

            $this->success = '✅ Solicitud aprobada exitosamente. El pastor ha sido notificado.';
            $this->autorizado = true;

            // NOTA: La notificación al pastor ya se envía desde el servicio PastorAuthorizationService

        } catch (\Exception $e) {
            $this->error = '❌ Error: ' . $e->getMessage();
            
            \Illuminate\Support\Facades\Log::error('Error aprobando solicitud', [
                'solicitud_id' => $this->solicitud->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Rechazar la solicitud
     */
    public function rechazar()
    {
        $this->validate();

        try {
            $service = app(PastorAuthorizationService::class);
            $service->rechazarSolicitud(
                $this->solicitud,
                Auth::user(),
                $this->motivoRechazo
            );

            $this->success = 'Solicitud rechazada. El pastor ha sido notificado.';
            $this->accion = 'ver';

        } catch (\Exception $e) {
            $this->error = '❌ Error: ' . $e->getMessage();
        }
    }

    /**
     * Notificar al pastor sobre la aprobación
     */
    private function notificarPastorAprobacion()
    {
        try {
            if (empty($this->pastor->telefono_tlf)) {
                return;
            }

            $mensaje = "✅ *Solicitud Aprobada*\n\n" .
                       "Su solicitud para modificar su número de teléfono ha sido aprobada por el Presbítero {$this->presbitero->name}.\n\n" .
                       "Ahora puede configurar sus preguntas de seguridad para proteger su cuenta.\n\n" .
                       "Gracias por usar el sistema.";

            // Formatear teléfono
            $telefono = preg_replace('/[\s\-\(\)]/', '', $this->pastor->telefono_tlf ?? $this->pastor->telefono_otro );
            if (str_starts_with($telefono, '0')) {
                $telefono = '58' . substr($telefono, 1);
            }
            if (!str_starts_with($telefono, '58')) {
                $telefono = '58' . $telefono;
            }

            // Usar servicio WhatsApp existente
            $whatsappService = app(\App\Services\WhatsAppService::class);
            if (method_exists($whatsappService, 'sendMessage')) {
                $whatsappService->sendMessage($telefono, $mensaje);
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('No se pudo notificar al pastor', [
                'pastor_id' => $this->pastor->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.public.pastor-autorizacion')
            ->layout('components.layouts.auth-basic');
    }
}
