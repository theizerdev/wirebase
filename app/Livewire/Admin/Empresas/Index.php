<?php

namespace App\Livewire\Admin\Empresas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Empresa;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function delete(Empresa $empresa)
    {
        $empresa->delete();
        session()->flash('message', 'Empresa eliminada correctamente.');
    }

    public function toggleStatus(Empresa $empresa)
    {
        $empresa->update([
            'status' => !$empresa->status
        ]);
        
        session()->flash('message', 'Estado de la empresa actualizado correctamente.');
    }

    public function render()
    {
        $query = Empresa::query();

        // Aplicar búsqueda
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('razon_social', 'like', '%' . $this->search . '%')
                  ->orWhere('documento', 'like', '%' . $this->search . '%')
                  ->orWhere('representante_legal', 'like', '%' . $this->search . '%');
            });
        }

        // Aplicar filtro de estado
        if ($this->status !== '') {
            $query->where('status', $this->status === 'active' ? 1 : 0);
        }

        // Aplicar ordenamiento
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Obtener resultados con paginación
        $empresas = $query->paginate($this->perPage);

        // Estadísticas
        $totalEmpresas = Empresa::count();
        $empresasActivas = Empresa::where('status', true)->count();
        $empresasInactivas = Empresa::where('status', false)->count();

        return view('livewire.admin.empresas.index', [
            'empresas' => $empresas,
            'totalEmpresas' => $totalEmpresas,
            'empresasActivas' => $empresasActivas,
            'empresasInactivas' => $empresasInactivas,
        ])
            ->layout('components.layouts.admin', [
                'title' => 'Lista de Empresas'
            ]);
    }
}