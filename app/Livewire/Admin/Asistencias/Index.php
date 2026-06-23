<?php

namespace App\Livewire\Admin\Asistencias;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Asistencia;
use App\Models\Actividad;
use App\Traits\HasDynamicLayout;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    // Filters & Search
    public $search = '';
    public $actividadId = '';
    public $metodo = '';
    public $fechaInicio = '';
    public $fechaFin = '';

    // Pagination & Sorting
    public $perPage = 10;
    public $sortBy = 'fecha_hora';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'actividadId' => ['except' => ''],
        'metodo' => ['except' => ''],
        'fechaInicio' => ['except' => ''],
        'fechaFin' => ['except' => ''],
        'sortBy' => ['except' => 'fecha_hora'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingActividadId() { $this->resetPage(); }
    public function updatingMetodo() { $this->resetPage(); }
    public function updatingFechaInicio() { $this->resetPage(); }
    public function updatingFechaFin() { $this->resetPage(); }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function eliminarAsistencia($id)
    {
        try {
            $asistencia = Asistencia::findOrFail($id);
            $asistencia->delete();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Registro de asistencia eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error eliminando asistencia: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Ocurrió un error al intentar eliminar la asistencia.'
            ]);
        }
    }

    public function exportarReporte()
    {
        $query = Asistencia::with(['pastor', 'actividad']);

        // Apply filters
        if ($this->search) {
            $query->whereHas('pastor', function ($q) {
                $q->where('nombres', 'like', '%' . $this->search . '%')
                  ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                  ->orWhere('documento', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->actividadId) {
            $query->where('actividad_id', $this->actividadId);
        }
        if ($this->metodo) {
            $query->where('metodo', $this->metodo);
        }
        if ($this->fechaInicio) {
            $query->whereDate('fecha_hora', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('fecha_hora', '<=', $this->fechaFin);
        }

        $records = $query->orderBy('fecha_hora', 'desc')->get();

        $filename = "reporte_asistencias_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=" . $filename,
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($records) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for proper UTF-8 decoding in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, ['Pastor', 'Cédula / Identificación', 'Actividad', 'Lugar', 'Método', 'Fecha y Hora']);

            foreach ($records as $rec) {
                fputcsv($file, [
                    $rec->pastor ? ($rec->pastor->nombres . ' ' . $rec->pastor->apellidos) : 'N/A',
                    $rec->pastor ? $rec->pastor->documento : 'N/A',
                    $rec->actividad ? $rec->actividad->nombre : 'N/A',
                    $rec->actividad ? $rec->actividad->lugar : 'N/A',
                    $rec->metodo,
                    $rec->fecha_hora ? $rec->fecha_hora->format('d/m/Y h:i A') : ($rec->created_at ? $rec->created_at->format('d/m/Y h:i A') : 'N/A')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        // 1. Get statistics based on active filters (excluding search for better metrics)
        $statsQuery = Asistencia::query();
        if ($this->actividadId) {
            $statsQuery->where('actividad_id', $this->actividadId);
        }
        if ($this->fechaInicio) {
            $statsQuery->whereDate('fecha_hora', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $statsQuery->whereDate('fecha_hora', '<=', $this->fechaFin);
        }

        $totalGeneral = (clone $statsQuery)->count();
        $totalQR = (clone $statsQuery)->where('metodo', 'QR')->count();
        $totalManual = (clone $statsQuery)->where('metodo', 'Manual')->count();

        // 2. Fetch list for layout table
        $query = Asistencia::with(['pastor', 'actividad']);

        if ($this->search) {
            $query->whereHas('pastor', function ($q) {
                $q->where('nombres', 'like', '%' . $this->search . '%')
                  ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                  ->orWhere('documento', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->actividadId) {
            $query->where('actividad_id', $this->actividadId);
        }
        if ($this->metodo) {
            $query->where('metodo', $this->metodo);
        }
        if ($this->fechaInicio) {
            $query->whereDate('fecha_hora', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('fecha_hora', '<=', $this->fechaFin);
        }

        $asistencias = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $actividades = Actividad::orderBy('fecha_inicio', 'desc')->get(['id', 'nombre']);

        return $this->renderWithLayout('livewire.admin.asistencias.index', 
            compact('asistencias', 'actividades', 'totalGeneral', 'totalQR', 'totalManual'), 
            [
                'title' => 'Asistencias',
                'description' => 'Listado y Reportes de Asistencias',
                'breadcrumb' => [
                    'admin.dashboard' => 'Dashboard',
                    'admin.asistencias.index' => 'Asistencias'
                ]
            ]
        );
    }
}
