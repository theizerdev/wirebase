<?php

namespace App\Livewire\Admin\Contabilidad;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\AsientoContable;
use Carbon\Carbon;

class Asientos extends Component
{
    use HasDynamicLayout, WithPagination;

    public $search = '';
    public $tipo = '';
    public $estado = '';
    public $fecha_desde;
    public $fecha_hasta;
    
    // Búsqueda de iglesias en tiempo real
    public $iglesiaSearch = '';
    public $iglesiasBuscadas = [];
    public $mostrarResultadosIglesia = false;
    public $iglesia_id = '';
    
    public $showDetalles = false;
    public $asientoSeleccionado;
    public $sortField = 'fecha';
    public $sortDirection = 'desc';
    public $perPage = 20;

    protected $queryString = [
        'search' => ['except' => ''],
        'tipo' => ['except' => ''],
        'estado' => ['except' => ''],
        'iglesia_id' => ['except' => ''],
        'sortField' => ['except' => 'fecha'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->authorize('access contabilidad');
        $this->fecha_desde = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_hasta = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTipo()
    {
        $this->resetPage();
    }

    public function updatingEstado()
    {
        $this->resetPage();
    }

    public function updatingIglesiaId()
    {
        $this->resetPage();
    }

    public function updatedIglesiaSearch($value)
    {
        if (strlen($value) >= 2) {
            $this->iglesiasBuscadas = \App\Models\Iglesia::activas()
                ->where('empresa_id', auth()->user()->empresa_id)
                ->where(function($query) use ($value) {
                    $query->where('nombre', 'like', '%' . $value . '%')
                          ->orWhere('direccion', 'like', '%' . $value . '%');
                })
                ->orderBy('nombre')
                ->limit(10)
                ->get();
            $this->mostrarResultadosIglesia = true;
        } else {
            $this->iglesiasBuscadas = [];
            $this->mostrarResultadosIglesia = false;
        }
    }

    public function seleccionarIglesia($iglesiaId)
    {
        $iglesia = \App\Models\Iglesia::find($iglesiaId);
        if ($iglesia) {
            $this->iglesia_id = $iglesiaId;
            $this->iglesiaSearch = $iglesia->nombre;
            $this->mostrarResultadosIglesia = false;
            $this->resetPage();
        }
    }

    public function limpiarBusquedaIglesia()
    {
        $this->iglesia_id = '';
        $this->iglesiaSearch = '';
        $this->iglesiasBuscadas = [];
        $this->mostrarResultadosIglesia = false;
        $this->resetPage();
    }

    #[On('closeIglesiaDropdown')]
    public function closeIglesiaDropdown()
    {
        $this->mostrarResultadosIglesia = false;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'tipo', 'estado', 'iglesia_id', 'iglesiaSearch']);
        $this->iglesiasBuscadas = [];
        $this->mostrarResultadosIglesia = false;
        $this->fecha_desde = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_hasta = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function verDetalles($id)
    {
        $this->authorize('view contabilidad');
        $this->asientoSeleccionado = AsientoContable::with(['detalles.cuenta', 'user'])
            ->findOrFail($id);
        $this->showDetalles = true;
    }

    public function closeDetalles()
    {
        $this->showDetalles = false;
        $this->asientoSeleccionado = null;
    }

    public function anular($id)
    {
        $this->authorize('delete contabilidad');

        try {
            $asiento = AsientoContable::findOrFail($id);
            
            if ($asiento->estado === 'anulado') {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'El asiento ya está anulado',
                ]);
                return;
            }

            $asiento->update(['estado' => 'anulado']);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Asiento {$asiento->numero} anulado exitosamente.",
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al anular el asiento: ' . $e->getMessage(),
            ]);
        }
    }

    protected function getPageTitle(): string
    {
        return 'Asientos Contables';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.contabilidad.asientos' => 'Asientos Contables'
        ];
    }

    public function getAsientosProperty()
    {
        return AsientoContable::with(['user', 'detalles', 'iglesia'])
            ->where('empresa_id', auth()->user()->empresa_id)
            ->when($this->iglesia_id, fn($q) => $q->where('iglesia_id', $this->iglesia_id))
            ->when($this->search, fn($q) => $q->where('numero', 'like', "%{$this->search}%")
                ->orWhere('descripcion', 'like', "%{$this->search}%"))
            ->when($this->tipo, fn($q) => $q->where('tipo', $this->tipo))
            ->when($this->estado, fn($q) => $q->where('estado', $this->estado))
            ->when($this->fecha_desde, fn($q) => $q->whereDate('fecha', '>=', $this->fecha_desde))
            ->when($this->fecha_hasta, fn($q) => $q->whereDate('fecha', '<=', $this->fecha_hasta))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function getStatsProperty()
    {
        $empresaId = auth()->user()->empresa_id;
        $query = AsientoContable::where('empresa_id', $empresaId)
            ->when($this->fecha_desde, fn($q) => $q->whereDate('fecha', '>=', $this->fecha_desde))
            ->when($this->fecha_hasta, fn($q) => $q->whereDate('fecha', '<=', $this->fecha_hasta));

        return [
            'total' => (clone $query)->count(),
            'aprobados' => (clone $query)->where('estado', 'aprobado')->count(),
            'borradores' => (clone $query)->where('estado', 'borrador')->count(),
            'anulados' => (clone $query)->where('estado', 'anulado')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.contabilidad.asientos', [
            'asientos' => $this->asientos,
            'stats' => $this->stats,
        ])->layout($this->getLayout());
    }
}
