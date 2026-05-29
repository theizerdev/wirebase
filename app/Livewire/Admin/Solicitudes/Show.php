<?php

namespace App\Livewire\Admin\Solicitudes;

use Livewire\Component;
use App\Models\SolicitudModificacionPastor;
use App\Models\AuditoriaSolicitud;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;
use App\Services\SolicitudWorkflowService;

class Show extends Component
{
    use HasDynamicLayout;
    public $solicitud;
    public $auditoria = [];
    public $comentario = '';
    public $motivoRechazo = '';
    
    public function mount($solicitud)
    {
        $this->solicitud = SolicitudModificacionPastor::with(['pastor', 'presbitero', 'aprobador'])->findOrFail($solicitud);
        $this->cargarAuditoria();
        
        // Registrar que se vio la solicitud
        $this->registrarAccion('vista');
    }
    
    private function cargarAuditoria()
    {
        $this->auditoria = AuditoriaSolicitud::where('solicitud_id', $this->solicitud->id)
            ->orderBy('created_at', 'asc')
            ->get();
    }
    
    private function registrarAccion(string $accion, array $metadata = [])
    {
        $user = Auth::user();
        
        AuditoriaSolicitud::create([
            'solicitud_id' => $this->solicitud->id,
            'accion' => $accion,
            'usuario_id' => $user?->id,
            'usuario_nombre' => $user?->name,
            'usuario_rol' => $user?->getRoleNames()->first(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
        ]);
        
        // Recargar auditoría
        $this->cargarAuditoria();
    }
    
    public function aprobar()
    {
        $this->validate([
            'comentario' => 'nullable|string|max:500'
        ]);
        
        $user = Auth::user();
        
        // Actualizar solicitud
        $this->solicitud->update([
            'estado' => 'aprobado',
            'aprobado_por' => $user->id,
            'aprobado_en' => now(),
            'ip_aprobacion' => request()->ip(),
        ]);
        
        // Registrar en auditoría
        $this->registrarAccion('aprobada', [
            'comentario' => $this->comentario,
            'aprobador_id' => $user->id,
            'aprobador_nombre' => $user->name,
        ]);
        
        session()->flash('message', '✅ Solicitud aprobada exitosamente.');
        
        // Enviar notificación WhatsApp al pastor
        app(SolicitudWorkflowService::class)->notificarPastor($this->solicitud, 'aprobada');
        
        return redirect()->route('admin.solicitudes.index');
    }
    
    public function rechazar()
    {
        $this->validate([
            'motivoRechazo' => 'required|string|min:10|max:500'
        ], [
            'motivoRechazo.required' => 'Debe indicar el motivo del rechazo.',
            'motivoRechazo.min' => 'El motivo debe tener al menos 10 caracteres.',
        ]);
        
        $user = Auth::user();
        
        // Actualizar solicitud
        $this->solicitud->update([
            'estado' => 'rechazado',
            'motivo_rechazo' => $this->motivoRechazo,
        ]);
        
        // Registrar en auditoría
        $this->registrarAccion('rechazada', [
            'motivo' => $this->motivoRechazo,
            'rechazado_por' => $user->id,
            'rechazado_nombre' => $user->name,
        ]);
        
        session()->flash('message', '❌ Solicitud rechazada.');
        
        // Enviar notificación WhatsApp al pastor con motivo
        app(SolicitudWorkflowService::class)->notificarPastor($this->solicitud, 'rechazada');
        
        return redirect()->route('admin.solicitudes.index');
    }
    
    public function render()
    {
        return view('livewire.admin.solicitudes.show')
            ->layout($this->getLayout(), ['title' => 'Detalle de Solicitud #' . $this->solicitud->id]);
    }
}
