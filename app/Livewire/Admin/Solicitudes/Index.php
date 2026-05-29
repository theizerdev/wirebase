<?php

namespace App\Livewire\Admin\Solicitudes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SolicitudModificacionPastor;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;
    
    public $search = '';
    public $filterEstado = '';
    public $filterFechaInicio = '';
    public $filterFechaFin = '';
    public $perPage = 15;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'filterEstado' => ['except' => ''],
    ];
    
    public function updatingSearch()
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
    
    public function render()
    {
        $user = Auth::user();
        
        $query = SolicitudModificacionPastor::with(['pastor', 'presbitero', 'aprobador']);
        
        // Filtrar según rol del usuario
        if ($user->hasRole('Presbitero')) {
            $query->where('presbitero_user_id', $user->id);
        }
        
        // Filtro de búsqueda
        if ($this->search) {
            $query->whereHas('pastor', function($q) {
                $q->where('nombres', 'like', '%' . $this->search . '%')
                  ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                  ->orWhere('documento', 'like', '%' . $this->search . '%');
            });
        }
        
        // Filtro por estado
        if ($this->filterEstado) {
            $query->where('estado', $this->filterEstado);
        }
        
        // Filtro por fecha
        if ($this->filterFechaInicio) {
            $query->whereDate('created_at', '>=', $this->filterFechaInicio);
        }
        if ($this->filterFechaFin) {
            $query->whereDate('created_at', '<=', $this->filterFechaFin);
        }
        
        // Ordenamiento
        $solicitudes = $query->orderBy($this->sortBy, $this->sortDirection)->paginate($this->perPage);
        
        return view('livewire.admin.solicitudes.index', [
            'solicitudes' => $solicitudes
        ])->layout($this->getLayout(), ['title' => 'Lista de Solicitudes']);
    }
}
