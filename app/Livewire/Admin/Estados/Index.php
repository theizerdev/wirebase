<?php

namespace App\Livewire\Admin\Estados;

use App\Models\Estado;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '';
    public $filterPais = '';
    public $filterActivo = '';
    public $sortBy = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterPais' => ['except' => ''],
        'filterActivo' => ['except' => ''],
        'sortBy' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        if (!Auth::user()->can('access estados')) {
            abort(403, 'No tienes permiso para acceder a estados.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterPais()
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

    public function toggleStatus($estadoId)
    {
        if (!Auth::user()->can('edit estados')) {
            session()->flash('error', 'No tienes permiso para editar estados.');
            return;
        }

        $estado = Estado::findOrFail($estadoId);
        $estado->activo = !$estado->activo;
        $estado->save();

        session()->flash('message', $estado->activo ? 'Estado activado exitosamente.' : 'Estado desactivado exitosamente.');
    }

    public function deleteEstado($estadoId)
    {
        if (!Auth::user()->can('delete estados')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No tienes permiso para eliminar estados.',
                'duration' => 4000
            ]);
            return;
        }

        $estado = Estado::findOrFail($estadoId);

        // Verificar si tiene ciudades asociadas
        if ($estado->ciudades()->exists()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No se puede eliminar el estado porque tiene ciudades asociadas.',
                'duration' => 4000
            ]);
            return;
        }

        try {
            $nombreEstado = $estado->nombre;
            $estado->delete();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Estado '{$nombreEstado}' eliminado exitosamente.",
                'duration' => 4000
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar el estado: ' . $e->getMessage(),
                'duration' => 5000
            ]);
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterPais', 'filterActivo', 'sortBy', 'sortDirection', 'perPage']);
        $this->sortBy = 'nombre';
        $this->sortDirection = 'asc';
        $this->perPage = 10;
    }

    private function getBaseQuery()
    {
        $query = Estado::with(['pais'])->withCount('ciudades');

        // Filtro por búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por país
        if ($this->filterPais) {
            $query->where('pais_id', $this->filterPais);
        }

        // Filtro por estado
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

        // Obtener estados paginados
        $estados = $query->paginate($this->perPage);

        // Calcular estadísticas
        $totalEstados = Estado::count();
        $estadosActivos = Estado::where('activo', true)->count();
        $estadosInactivos = Estado::where('activo', false)->count();
        $estadosConCiudades = Estado::has('ciudades')->count();

        return view('livewire.admin.estados.index', compact(
            'estados',
            'totalEstados',
            'estadosActivos',
            'estadosInactivos',
            'estadosConCiudades'
        ))->layout($this->getLayout());
    }
}
