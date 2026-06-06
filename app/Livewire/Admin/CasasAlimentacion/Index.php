<?php

namespace App\Livewire\Admin\CasasAlimentacion;
use App\Traits\HasDynamicLayout;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CasaAlimentacion;
use Illuminate\Support\Facades\Auth;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout;

    public $search = '';
    public $estadoId = '';
    public $estadoCda = '';
    public $zonaBaseMisiones = '';
    public $sortBy = 'codigo';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'estadoId' => ['except' => ''],
        'estadoCda' => ['except' => ''],
        'zonaBaseMisiones' => ['except' => ''],
        'sortBy' => ['except' => 'codigo'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        if (!Auth::user()->can('access casas_alimentacion')) {
            abort(403, 'No tienes permiso para acceder a casas de alimentación.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEstadoId()
    {
        $this->resetPage();
    }

    public function updatingEstadoCda()
    {
        $this->resetPage();
    }

    public function updatingZonaBaseMisiones()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteCasa($casaId)
    {
        if (!Auth::user()->can('delete casas_alimentacion')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No tienes permiso para eliminar casas de alimentación.',
                'duration' => 4000
            ]);
            return;
        }

        $casa = CasaAlimentacion::findOrFail($casaId);

        try {
            $codigoCasa = $casa->codigo;
            $casa->delete();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Casa de Alimentación '{$codigoCasa}' eliminada exitosamente.",
                'duration' => 4000
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar la casa de alimentación: ' . $e->getMessage(),
                'duration' => 5000
            ]);
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'estadoId', 'estadoCda', 'zonaBaseMisiones', 'sortBy', 'sortDirection', 'perPage']);
        $this->sortBy = 'codigo';
        $this->sortDirection = 'asc';
        $this->perPage = 10;
    }

    protected function getExportQuery()
    {
        return $this->getBaseQuery();
    }

    protected function getExportHeaders(): array
    {
        return ['ID', 'Código', 'Fecha', 'Estado', 'Municipio', 'Parroquia', 'Sector', 'Teléfono', 'Estado CDA'];
    }

    protected function formatExportRow($casa): array
    {
        return [
            $casa->id,
            $casa->codigo,
            $casa->fecha ? $casa->fecha->format('d/m/Y') : '',
            $casa->estado?->nombre,
            $casa->municipio?->nombre,
            $casa->parroquia?->nombre,
            $casa->sector,
            $casa->telefono_principal,
            $casa->estado_cda
        ];
    }

    private function getBaseQuery()
    {
        $query = CasaAlimentacion::with(['estado', 'municipio', 'parroquia']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('codigo', 'like', '%' . $this->search . '%')
                  ->orWhere('sector', 'like', '%' . $this->search . '%')
                  ->orWhere('consejo_comunal', 'like', '%' . $this->search . '%')
                  ->orWhere('vocero_alimentacion', 'like', '%' . $this->search . '%')
                  ->orWhere('telefono_principal', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->estadoId !== '') {
            $query->where('estado_id', $this->estadoId);
        }

        if ($this->estadoCda !== '') {
            $query->where('estado_cda', $this->estadoCda);
        }

        if ($this->zonaBaseMisiones !== '') {
            $query->where('zona_base_misiones', $this->zonaBaseMisiones === 'si');
        }

        return $query;
    }

    public function render()
    {
        $query = $this->getBaseQuery();

        // Ordenar
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Obtener casas paginadas
        $casas = $query->paginate($this->perPage);

        // Calcular estadísticas
        $totalCasas = CasaAlimentacion::count();
        $casasOperativas = CasaAlimentacion::where('estado_cda', 'Operativa')->count();
        $casasInoperativas = CasaAlimentacion::where('estado_cda', 'Inoperativa')->count();
        $casasZonaBase = CasaAlimentacion::where('zona_base_misiones', true)->count();

        return $this->renderWithLayout('livewire.admin.casas_alimentacion.index', compact(
            'casas',
            'totalCasas',
            'casasOperativas',
            'casasInoperativas',
            'casasZonaBase'
        ), [
            'title' => 'Casas de Alimentación',
            'description' => 'Gestión de casas de alimentación del sistema',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.casas_alimentacion.index' => 'Casas de Alimentación'
            ]
        ]);
    }
}
