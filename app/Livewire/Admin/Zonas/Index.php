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
    
    protected $queryString = [
        'search' => ['except' => ''],
        'filterSucursal' => ['except' => ''],
        'filterDistrito' => ['except' => ''],
        'filterActivo' => ['except' => ''],
    ];
    
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
     * Obtener las zonas filtradas.
     */
    public function getZonasProperty()
    {
        $query = Zona::query()
            ->with(['empresa', 'sucursal'])
            ->orderBy('nombre');
        
        // Filtro por búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo', 'like', '%' . $this->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $this->search . '%');
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
        
        return $query->paginate(15);
    }
    
    /**
     * Renderizar el componente.
     */
    public function render()
    {
        // Calcular estadísticas
        $totalZonas = Zona::count();
        $zonasActivas = Zona::where('activo', true)->count();
        $zonasInactivas = Zona::where('activo', false)->count();
        $zonasConUsuarios = Zona::has('users')->count();

        return view('livewire.admin.zonas.index', [
            'zonas' => $this->zonas,
            'totalZonas' => $totalZonas,
            'zonasActivas' => $zonasActivas,
            'zonasInactivas' => $zonasInactivas,
            'zonasConUsuarios' => $zonasConUsuarios,
        ])->layout($this->getLayout());
    }
}
