<?php

namespace App\Livewire\Admin\Sucursales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sucursal;
use App\Models\Empresa;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $empresa_id = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'empresa_id' => ['except' => ''],
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

    public function updatingEmpresaId()
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
        $this->empresa_id = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function delete(Sucursal $sucursal)
    {
        $sucursal->delete();
        session()->flash('message', 'Sucursal eliminada correctamente.');
    }

    public function toggleStatus(Sucursal $sucursal)
    {
        $sucursal->update([
            'status' => !$sucursal->status
        ]);
        
        session()->flash('message', 'Estado de la sucursal actualizado correctamente.');
    }

    public function render()
    {
        $query = Sucursal::with('empresa');

        // Aplicar búsqueda
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('telefono', 'like', '%' . $this->search . '%')
                  ->orWhere('direccion', 'like', '%' . $this->search . '%');
            });
        }

        // Aplicar filtro de estado
        if ($this->status !== '') {
            $query->where('status', $this->status === 'active' ? 1 : 0);
        }

        // Aplicar filtro por empresa
        if ($this->empresa_id !== '') {
            $query->where('empresa_id', $this->empresa_id);
        }

        // Aplicar ordenamiento
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Obtener resultados con paginación
        $sucursales = $query->paginate($this->perPage);

        // Estadísticas
        $totalSucursales = Sucursal::count();
        $sucursalesActivas = Sucursal::where('status', true)->count();
        $sucursalesInactivas = Sucursal::where('status', false)->count();
        
        // Listado de empresas para el filtro
        $empresas = Empresa::where('status', true)->get();

        return view('livewire.admin.sucursales.index', [
            'sucursales' => $sucursales,
            'totalSucursales' => $totalSucursales,
            'sucursalesActivas' => $sucursalesActivas,
            'sucursalesInactivas' => $sucursalesInactivas,
            'empresas' => $empresas
        ])
            ->layout('components.layouts.admin', [
                'title' => 'Lista de Sucursales'
            ]);
    }
}