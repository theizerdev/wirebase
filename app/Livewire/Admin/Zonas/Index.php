<?php

namespace App\Livewire\Admin\Zonas;

use App\Models\Zona;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\HasDynamicLayout;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;
    
    public $search = '';
    public $filterSucursal = '';
    public $filterDistrito = '';
    public $filterActivo = '';
    public $perPage = 10;
    public $sortBy = 'nombre';
    public $sortDirection = 'asc';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'filterSucursal' => ['except' => ''],
        'filterDistrito' => ['except' => ''],
        'filterActivo' => ['except' => ''],
        'sortBy' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
    ];
    
    /**
     * Ordenar por columna.
     */
    public function sortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }
    
    /**
     * Limpiar filtros.
     */
    public function clearFilters()
    {
        $this->reset(['search', 'filterSucursal', 'filterDistrito', 'filterActivo']);
    }
    
    /**
     * Eliminar una zona.
     */
    public function deleteZona($zonaId)
    {
        $zona = Zona::findOrFail($zonaId);
        
        // Verificar que la zona no tenga usuarios asignados
        if ($zona->users()->count() > 0) {
            session()->flash('error', 'No se puede eliminar la zona porque tiene usuarios asignados.');
            return;
        }
        
        $zona->delete();
        
        session()->flash('message', 'Zona eliminada correctamente.');
    }
    
    /**
     * Cambiar el estado de una zona.
     */
    public function toggleActivo($zonaId)
    {
        $zona = Zona::findOrFail($zonaId);
        $zona->activo = !$zona->activo;
        $zona->save();
        
        session()->flash('message', 'Estado de la zona actualizado correctamente.');
    }
    
    /**
     * Obtener estadísticas.
     */
    public function getTotalZonasProperty()
    {
        return Zona::count();
    }
    
    public function getZonasActivasProperty()
    {
        return Zona::where('activo', true)->count();
    }
    
    public function getZonasInactivasProperty()
    {
        return Zona::where('activo', false)->count();
    }
    
    public function getZonasConUsuariosProperty()
    {
        return Zona::has('users')->count();
    }
    
    /**
     * Obtener las zonas filtradas.
     */
    public function getZonasProperty()
    {
        $query = Zona::query()
            ->with(['empresa', 'sucursal'])
            ->withCount('users');
        
        // Filtro por búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo', 'like', '%' . $this->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('distrito', 'like', '%' . $this->search . '%');
            });
        }
        
        // Filtro por sucursal
        if ($this->filterSucursal) {
            $query->where('sucursal_id', $this->filterSucursal);
        }
        
        // Filtro por distrito
        if ($this->filterDistrito) {
            $query->where('distrito', 'like', '%' . $this->filterDistrito . '%');
        }
        
        // Filtro por estado
        if ($this->filterActivo !== '') {
            $query->where('activo', $this->filterActivo === '1');
        }
        
        // Ordenamiento
        $query->orderBy($this->sortBy, $this->sortDirection);
        
        return $query->paginate($this->perPage);
    }
    
    /**
     * Renderizar el componente.
     */
    public function render()
    {
        return view('livewire.admin.zonas.index', [
            'zonas' => $this->zonas,
            'totalZonas' => $this->totalZonas,
            'zonasActivas' => $this->zonasActivas,
            'zonasInactivas' => $this->zonasInactivas,
            'zonasConUsuarios' => $this->zonasConUsuarios,
        ])->layout($this->getLayout());
    }
}
