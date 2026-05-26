<?php

namespace App\Livewire\Admin\Pastores;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Pastor;
use App\Services\PastorAuthorizationService;
use Illuminate\Support\Facades\Auth;

class SolicitarModificacionTelefono extends Component
{
    use HasDynamicLayout;

    public Pastor $pastor;
    public $telefonoNuevo = '';
    public $showModal = false;
    public $solicitudCreada = false;
    public $error = '';
    public $success = '';

    protected $rules = [
        'telefonoNuevo' => 'required|string|min:11|max:20',
    ];

    protected $messages = [
        'telefonoNuevo.required' => 'El número de teléfono es obligatorio.',
        'telefonoNuevo.min' => 'El número de teléfono debe tener al menos 11 dígitos.',
        'telefonoNuevo.max' => 'El número de teléfono no puede exceder 20 dígitos.',
    ];

    public function mount(Pastor $pastor)
    {
        $this->pastor = $pastor;
    }

    /**
     * Abrir el modal
     */
    public function abrirModal()
    {
        $this->reset(['telefonoNuevo', 'error', 'success', 'solicitudCreada']);
        $this->showModal = true;
    }

    /**
     * Cerrar el modal
     */
    public function cerrarModal()
    {
        $this->showModal = false;
        $this->reset(['telefonoNuevo', 'error', 'success', 'solicitudCreada']);
    }

    /**
     * Enviar solicitud de modificación
     */
    public function enviarSolicitud()
    {
        $this->validate();

        try {
            // Verificar que el pastor tenga zona
            if (empty($this->pastor->zona)) {
                $this->error = 'Este pastor no tiene una zona asignada. Contacte al administrador.';
                return;
            }

            // Verificar que no haya una solicitud pendiente
            $solicitudPendiente = $this->pastor->solicitudesModificacion()
                ->pendientes()
                ->noExpiradas()
                ->first();

            if ($solicitudPendiente) {
                $this->error = 'Ya existe una solicitud pendiente para este pastor. Espere la aprobación del Presbítero.';
                return;
            }

            // Crear la solicitud usando el servicio
            $service = app(PastorAuthorizationService::class);
            $solicitud = $service->crearSolicitud($this->pastor, $this->telefonoNuevo);

            $this->solicitudCreada = true;
            $this->success = '✅ Solicitud enviada exitosamente. El Presbítero de su zona ha sido notificado vía WhatsApp.';
            $this->telefonoNuevo = '';

            // Cerrar modal después de 3 segundos
            $this->dispatch('close-modal-after-delay', delay: 3000);

        } catch (\Exception $e) {
            $this->error = '❌ Error: ' . $e->getMessage();
            
            \Illuminate\Support\Facades\Log::error('Error creando solicitud de modificación', [
                'pastor_id' => $this->pastor->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.pastores.solicitar-modificacion-telefono');
    }
}
