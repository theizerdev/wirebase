<?php

namespace App\Livewire\Admin\Municipios;

use App\Models\Municipio;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '';
    public $filterEstado = '';
    public $filterActivo = '';
    public $sortBy = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterEstado' => ['except' => ''],
        'filterActivo' => ['except' => ''],
        'sortBy' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        if (!Auth::user()->can('access municipios')) {
            abort(403, 'No tienes permiso para acceder a municipios.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterEstado()
    {
        $this->resetPage();
    }

    public function updatingFilterActivo()
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

    public function toggleStatus($municipioId)
    {
        if (!Auth::user()->can('edit municipios')) {
            session()->flash('error', 'No tienes permiso para editar municipios.');
            return;
        }

        $municipio = Municipio::findOrFail($municipioId);
        $municipio->activo = !$municipio->activo;
        $municipio->save();

        session()->flash('message', $municipio->activo ? 'Municipio activado exitosamente.' : 'Municipio desactivado exitosamente.');
    }

    public function deleteMunicipio($municipioId)
    {
        if (!Auth::user()->can('delete municipios')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No tienes permiso para eliminar municipios.',
                'duration' => 4000
            ]);
            return;
        }

        $municipio = Municipio::findOrFail($municipioId);

        // Verificar si tiene parroquias asociadas
        if ($municipio->parroquias()->exists()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No se puede eliminar el municipio porque tiene parroquias asociadas.',
                'duration' => 4000
            ]);
            return;
        }

        try {
            $nombreMunicipio = $municipio->nombre;
            $municipio->delete();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Municipio '{$nombreMunicipio}' eliminado exitosamente.",
                'duration' => 4000
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar el municipio: ' . $e->getMessage(),
                'duration' => 5000
            ]);
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterEstado', 'filterActivo', 'sortBy', 'sortDirection', 'perPage']);
        $this->sortBy = 'nombre';
        $this->sortDirection = 'asc';
        $this->perPage = 10;
    }

    private function getBaseQuery()
    {
        $query = Municipio::with(['estado'])->withCount('parroquias');

        // Filtro por búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por estado
        if ($this->filterEstado) {
            $query->where('estado_id', $this->filterEstado);
        }

        // Filtro por estado activo/inactivo
        if ($this->filterActivo !== '') {
            $query->where('activo', $this->filterActivo === '1');
        }

        return $query;
    }

    public function render()
    {
        $query = $this->getBaseQuery();

        // Ordenar
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Obtener municipios paginados
        $municipios = $query->paginate($this->perPage);

        // Calcular estadísticas
        $totalMunicipios = Municipio::count();
        $municipiosActivos = Municipio::where('activo', true)->count();
        $municipiosInactivos = Municipio::where('activo', false)->count();
        $municipiosConParroquias = Municipio::has('parroquias')->count();

        return view('livewire.admin.municipios.index', compact(
            'municipios',
            'totalMunicipios',
            'municipiosActivos',
            'municipiosInactivos',
            'municipiosConParroquias'
        ))->layout($this->getLayout());
    }
}