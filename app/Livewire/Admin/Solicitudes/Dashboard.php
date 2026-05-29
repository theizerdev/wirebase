<?php

namespace App\Livewire\Admin\Solicitudes;

use Livewire\Component;
use App\Models\SolicitudModificacionPastor;
use App\Models\AuditoriaSolicitud;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Dashboard extends Component
{
    use HasDynamicLayout;
    public $stats = [];
    public $solicitudesRecientes = [];
    
    public function mount()
    {
        $this->cargarEstadisticas();
        $this->cargarSolicitudesRecientes();
    }
    
    private function cargarEstadisticas()
    {
        $user = Auth::user();
        
        // Base query - filtrar según rol del usuario
        $query = SolicitudModificacionPastor::query();
        
        // Si es presbítero, solo ver sus solicitudes asignadas
        if ($user->hasRole('Presbitero')) {
            $query->where('presbitero_user_id', $user->id);
        }
        // TODO: Agregar filtros para otros roles (Supervisor Nacional, Oficina Nacional)
        
        $this->stats = [
            'pendientes' => (clone $query)->where('estado', 'pendiente')->count(),
            'aprobadas' => (clone $query)->where('estado', 'aprobado')->count(),
            'rechazadas' => (clone $query)->where('estado', 'rechazado')->count(),
            'expiradas' => (clone $query)->where('estado', 'expirado')->count(),
            'total' => (clone $query)->count(),
            'tiempo_promedio_respuesta' => $this->calcularTiempoPromedioRespuesta($query),
        ];
    }
    
    private function cargarSolicitudesRecientes()
    {
        $user = Auth::user();
        
        $query = SolicitudModificacionPastor::with(['pastor', 'presbitero'])
            ->orderBy('created_at', 'desc')
            ->limit(10);
        
        // Filtrar según rol
        if ($user->hasRole('Presbitero')) {
            $query->where('presbitero_user_id', $user->id);
        }
        
        $this->solicitudesRecientes = $query->get()->map(function($solicitud) {
            return [
                'id' => $solicitud->id,
                'pastor_nombre' => $solicitud->pastor ? $solicitud->pastor->nombre_completo : 'N/A',
                'pastor_cedula' => $solicitud->pastor ? $solicitud->pastor->documento : 'N/A',
                'presbitero_nombre' => $solicitud->presbitero ? $solicitud->presbitero->name : 'Sin asignar',
                'estado' => $solicitud->estado,
                'fecha_creacion' => $solicitud->created_at->format('d/m/Y H:i'),
                'tiempo_transcurrido' => $solicitud->created_at->diffForHumans(),
            ];
        });
    }
    
    private function calcularTiempoPromedioRespuesta($query)
    {
        // Calcular tiempo promedio entre creación y aprobación
        $solicitudesAprobadas = (clone $query)
            ->where('estado', 'aprobado')
            ->whereNotNull('aprobado_en')
            ->get();
        
        if ($solicitudesAprobadas->isEmpty()) {
            return 'N/A';
        }
        
        $totalMinutos = $solicitudesAprobadas->sum(function($solicitud) {
            return $solicitud->created_at->diffInMinutes($solicitud->aprobado_en);
        });
        
        $promedioMinutos = $totalMinutos / $solicitudesAprobadas->count();
        
        if ($promedioMinutos < 60) {
            return round($promedioMinutos) . ' min';
        } elseif ($promedioMinutos < 1440) {
            return round($promedioMinutos / 60, 1) . ' hrs';
        } else {
            return round($promedioMinutos / 1440, 1) . ' días';
        }
    }
    
    public function render()
    {
        return view('livewire.admin.solicitudes.dashboard')
            ->layout($this->getLayout(), ['title' => 'Dashboard de Solicitudes']);
    }
}
