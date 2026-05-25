<?php

namespace App\Livewire\Admin\Parroquias;

use App\Models\Parroquia;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '';
    public $filterMunicipio = '';
    public $filterActivo = '';
    public $sortBy = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterMunicipio' => ['except' => ''],
        'filterActivo' => ['except' => ''],
        'sortBy' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        if (!Auth::user()->can('access parroquias')) {
            abort(403, 'No tienes permiso para acceder a parroquias.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterMunicipio()
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

    public function toggleStatus($parroquiaId)
    {
        if (!Auth::user()->can('edit parroquias')) {
            session()->flash('error', 'No tienes permiso para editar parroquias.');
            return;
        }

        $parroquia = Parroquia::findOrFail($parroquiaId);
        $parroquia->activo = !$parroquia->activo;
        $parroquia->save();

        session()->flash('message', $parroquia->activo ? 'Parroquia activada exitosamente.' : 'Parroquia desactivada exitosamente.');
    }

    public function deleteParroquia($parroquiaId)
    {
        if (!Auth::user()->can('delete parroquias')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No tienes permiso para eliminar parroquias.',
                'duration' => 4000
            ]);
            return;
        }

        $parroquia = Parroquia::findOrFail($parroquiaId);

        try {
            $nombreParroquia = $parroquia->nombre;
            $parroquia->delete();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Parroquia '{$nombreParroquia}' eliminada exitosamente.",
                'duration' => 4000
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar la parroquia: ' . $e->getMessage(),
                'duration' => 5000
            ]);
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterMunicipio', 'filterActivo', 'sortBy', 'sortDirection', 'perPage']);
        $this->sortBy = 'nombre';
        $this->sortDirection = 'asc';
        $this->perPage = 10;
    }

    private function getBaseQuery()
    {
        $query = Parroquia::with(['municipio.estado']);

        // Filtro por búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por municipio
        if ($this->filterMunicipio) {
            $query->where('municipio_id', $this->filterMunicipio);
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

        // Obtener parroquias paginadas
        $parroquias = $query->paginate($this->perPage);

        // Calcular estadísticas
        $totalParroquias = Parroquia::count();
        $parroquiasActivas = Parroquia::count();
        $parroquiasInactivas = 0;

        return view('livewire.admin.parroquias.index', compact(
            'parroquias',
            'totalParroquias',
            'parroquiasActivas',
            'parroquiasInactivas'
        ))->layout($this->getLayout());
    }
}