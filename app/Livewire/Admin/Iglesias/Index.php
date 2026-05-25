<?php

namespace App\Livewire\Admin\Iglesias;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Iglesia;
use App\Traits\Exportable;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use HasDynamicLayout, WithPagination, Exportable;

    public $search = '';
    public $status = '';
    public $zona = '';
    public $sortBy = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 12;
    public $viewMode = 'grid'; // 'grid' o 'list'

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'zona' => ['except' => ''],
        'sortBy' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 12],
        'viewMode' => ['except' => 'grid']
    ];

    public function mount()
    {
        $this->fill(request()->only(['search', 'status', 'zona', 'page']));
    }

    public function updatingZona()
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
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->zona = '';
        $this->sortBy = 'nombre';
        $this->sortDirection = 'asc';
        $this->perPage = 12;
        $this->resetPage();
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    protected function getExportQuery()
    {
        return $this->getBaseQuery();
    }

    protected function getExportHeaders(): array
    {
        return [
            'ID',
            'Nombre',
            'Dirección',
            'Pastor',
            'Teléfono',
            'Email',
            'Zona',
            'Distrito',
            'Estado',
            'Activa'
        ];
    }

    protected function formatExportRow($iglesia): array
    {
        return [
            $iglesia->id,
            $iglesia->nombre,
            $iglesia->direccion,
            $iglesia->pastor ? $iglesia->pastor->nombres . ' ' . $iglesia->pastor->apellidos : 'Sin asignar',
            $iglesia->telefono,
            $iglesia->email,
            $iglesia->zona,
            $iglesia->distrito,
            $iglesia->estado ? $iglesia->estado->nombre : 'N/A',
            $iglesia->activa ? 'Sí' : 'No'
        ];
    }

    private function getBaseQuery()
    {
        return Iglesia::with(['pastor', 'estado', 'ciudad'])
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('direccion', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== '', function ($query) {
                $query->where('activa', $this->status === 'activa');
            })
            ->when($this->zona !== '', function ($query) {
                $query->where('zona', $this->zona);
            })
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function deleteIglesia(Iglesia $iglesia)
    {
        $iglesia->delete();
        session()->flash('message', 'Extensión eliminada correctamente.');
    }

    public function render()
    {
        try {
            $iglesias = $this->getBaseQuery()->paginate($this->perPage);
        } catch (\Exception $e) {
            Log::error('Error en la consulta de iglesias: ' . $e->getMessage());
            $iglesias = collect([]);
        }
       
        // Obtener las zonas como colección separada
        $zonas = Iglesia::select('zona')->whereNotNull('zona')->distinct()->orderBy('zona')->pluck('zona');
        
        // Calcular estadísticas
        $totalIglesias = Iglesia::count();
        $iglesiasActivas = Iglesia::where('activa', true)->count();
        $iglesiasInactivas = Iglesia::where('activa', false)->count();
        $totalZonas = $zonas->count();

        return $this->renderWithLayout('livewire.admin.iglesias.index', [
            'iglesias' => $iglesias,
            'zonas' => $zonas,
            'totalIglesias' => $totalIglesias,
            'iglesiasActivas' => $iglesiasActivas,
            'iglesiasInactivas' => $iglesiasInactivas,
            'totalZonas' => $totalZonas
        ], [
            'title' => 'Lista de Extensiones',
            'description' => 'Administra las extensiones del sistema'
        ]);
    }
}
