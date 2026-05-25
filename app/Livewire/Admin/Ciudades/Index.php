<?php

namespace App\Livewire\Admin\Ciudades;

use App\Models\Ciudad;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '';
    public $filterActivo = '';
    public $sortBy = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
      
        'sortBy' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        if (!Auth::user()->can('access ciudades')) {
            abort(403, 'No tienes permiso para acceder a ciudades.');
        }
    }

    public function updatingSearch()
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

    

    public function deleteCiudad($ciudadId)
    {
        if (!Auth::user()->can('delete ciudades')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No tienes permiso para eliminar ciudades.',
                'duration' => 4000
            ]);
            return;
        }

        $ciudad = Ciudad::findOrFail($ciudadId);

        // En la nueva estructura, las ciudades no tienen municipios asociados directamente
        // La relación es Estado -> Municipio -> Parroquia
        // Las ciudades son entidades separadas sin relación directa con municipios
        
        try {
            $nombreCiudad = $ciudad->nombre;
            $ciudad->delete();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Ciudad '{$nombreCiudad}' eliminada exitosamente.",
                'duration' => 4000
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar la ciudad: ' . $e->getMessage(),
                'duration' => 5000
            ]);
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterActivo', 'sortBy', 'sortDirection', 'perPage']);
        $this->sortBy = 'nombre';
        $this->sortDirection = 'asc';
        $this->perPage = 10;
    }

    private function getBaseQuery()
    {
        $query = Ciudad::with(['estado']); // Remover withCount('municipios')

        // Filtro por búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo', 'like', '%' . $this->search . '%');
            });
        }

    

        return $query;
    }

    public function render()
    {
        $query = $this->getBaseQuery();

        // Ordenar
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Obtener ciudades paginadas
        $ciudades = $query->paginate($this->perPage);

        // Calcular estadísticas (actualizar para reflejar la nueva estructura)
        $totalCiudades = Ciudad::count();
        $ciudadesActivas = Ciudad::count();
        $ciudadesInactivas = 0;
        // Remover referencia a ciudades con municipios
        $totalEstados = \App\Models\Estado::count();
        $totalMunicipios = \App\Models\Municipio::count();

        return view('livewire.admin.ciudades.index', compact(
            'ciudades',
            'totalCiudades',
            'ciudadesActivas',
            'ciudadesInactivas',
            'totalEstados',
            'totalMunicipios'
        ))->layout($this->getLayout());
    }
}