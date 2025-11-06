<?php

namespace App\Livewire\Admin\Paises;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pais;
use App\Traits\HasDynamicLayout;
use App\Traits\Exportable;
use Illuminate\Support\Facades\Auth;

class PaisIndex extends Component
{
    use WithPagination, HasDynamicLayout, Exportable;

    public $search = '';
    public $activo = null;
    public $sortBy = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'activo' => ['except' => null],
        'sortBy' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10]
    ];

    public function updatingActivo()
    {
        $this->resetPage();
    }

    public function updatingSearch()
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

    public function toggleStatus($paisId)
    {
        if (!Auth::user()->can('edit paises')) {
            session()->flash('error', 'No tienes permiso para editar países.');
            return;
        }

        $pais = Pais::findOrFail($paisId);
        $pais->update(['activo' => !$pais->activo]);
        session()->flash('message', $pais->activo ? 'País activado exitosamente.' : 'País desactivado exitosamente.');
    }

    public function deletePais($paisId)
    {
        if (!Auth::user()->can('delete paises')) {
            session()->flash('error', 'No tienes permiso para eliminar países.');
            return;
        }

        $pais = Pais::findOrFail($paisId);

        if ($pais->empresas()->exists()) {
            session()->flash('error', 'No se puede eliminar el país porque tiene empresas asociadas.');
            return;
        }

        try {
            $pais->delete();
            session()->flash('message', 'País eliminado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el país: ' . $e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'activo', 'sortBy', 'sortDirection', 'perPage']);
        $this->sortBy = 'nombre';
        $this->sortDirection = 'asc';
        $this->perPage = 10;
    }

    protected function getExportQuery()
    {
        return $this->getBaseQuery();
    }

    protected function getExportHeaders(): array
    {
        return ['ID', 'Nombre', 'Código ISO2', 'Código ISO3', 'Moneda', 'Continente', 'Activo'];
    }

    protected function formatExportRow($pais): array
    {
        return [
            $pais->id,
            $pais->nombre,
            $pais->codigo_iso2,
            $pais->codigo_iso3,
            $pais->moneda_principal,
            $pais->continente,
            $pais->activo ? 'Activo' : 'Inactivo'
        ];
    }

    private function getBaseQuery()
    {
        $query = Pais::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo_iso2', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo_iso3', 'like', '%' . $this->search . '%')
                  ->orWhere('moneda_principal', 'like', '%' . $this->search . '%')
                  ->orWhere('continente', 'like', '%' . $this->search . '%');
            });
        }

            if ($this->activo !== null) {
                $query->where('activo', (bool)$this->activo);
        }

        return $query;
    }

    public function render()
    {
        $query = $this->getBaseQuery();

        // Ordenar
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Obtener países paginados
        $paises = $query->paginate($this->perPage);

        // Calcular estadísticas
        $totalPaises = Pais::count();
        $paisesActivos = Pais::where('activo', 1)->count();
        $paisesInactivos = Pais::where('activo', 0)->count();

        return $this->renderWithLayout('livewire.admin.paises.pais-index', [
            'paises' => $paises,
            'totalPaises' => $totalPaises,
            'paisesActivos' => $paisesActivos,
            'paisesInactivos' => $paisesInactivos,
        ], [
            'title' => 'Gestión de Países',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.paises.index' => 'Países'
            ]
        ]);
    }
}
