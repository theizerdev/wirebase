<?php

namespace App\Livewire\Admin\Inventario\Unidades;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MotoUnidad;
use App\Models\InventoryMovement;
use App\Traits\Exportable;
use Illuminate\Support\Facades\Auth;

class History extends Component
{
    use WithPagination, HasDynamicLayout, Exportable;

    public MotoUnidad $unidad;
    public $dateFrom;
    public $dateTo;
    public $tipo = '';
    public $responsable_id = '';
    public $viewMode = 'table';
    public $perPage = 10;

    protected $queryString = [
        'viewMode' => ['except' => 'table'],
        'tipo' => ['except' => ''],
        'responsable_id' => ['except' => ''],
    ];

    public function mount(MotoUnidad $unidad)
    {
        if (!Auth::user()->can('access moto unidades')) {
            abort(403);
        }
        $this->unidad = $unidad;
    }

    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }
    public function updatedTipo() { $this->resetPage(); }
    public function updatedResponsableId() { $this->resetPage(); }

    private function getBaseQuery()
    {
        return InventoryMovement::with(['origenSucursal', 'destinoSucursal', 'responsable'])
            ->where('moto_unidad_id', $this->unidad->id)
            ->when($this->dateFrom, fn($q) => $q->where('occurred_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->where('occurred_at', '<=', $this->dateTo))
            ->when($this->tipo, fn($q) => $q->where('tipo', $this->tipo))
            ->when($this->responsable_id, fn($q) => $q->where('responsable_id', $this->responsable_id))
            ->orderBy('occurred_at', 'desc');
    }

    protected function getExportQuery()
    {
        return $this->getBaseQuery();
    }

    protected function getExportHeaders(): array
    {
        return ['Fecha/Hora', 'Tipo', 'Origen', 'Destino', 'Responsable', 'Cantidad', 'Observaciones'];
    }

    protected function formatExportRow($row): array
    {
        return [
            optional($row->occurred_at)->format('d/m/Y H:i'),
            ucfirst($row->tipo),
            $row->origenSucursal->nombre ?? '-',
            $row->destinoSucursal->nombre ?? '-',
            $row->responsable->name ?? '-',
            $row->cantidad,
            $row->observaciones
        ];
    }

    public function render()
    {
        $movimientos = $this->getBaseQuery()->paginate($this->perPage);
        return view('livewire.admin.inventario.unidades.history', compact('movimientos'))->layout($this->getLayout(), [
            'title' => 'Historial de Movimientos'
        ]);
    }
}
