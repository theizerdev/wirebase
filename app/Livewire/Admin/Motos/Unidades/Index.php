<?php

namespace App\Livewire\Admin\Motos\Unidades;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Moto;
use App\Models\MotoUnidad;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout;

    public Moto $moto;
    public $search = '';
    public $estado = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'estado' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function mount(Moto $moto)
    {
        $this->moto = $moto;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'estado', 'sortBy', 'sortDirection', 'perPage']);
    }

    public function delete($id)
    {
        $unidad = MotoUnidad::findOrFail($id);
        
        // Validar que no tenga contratos asociados
        if ($unidad->contrato()->exists()) {
            $this->dispatch('error', 'No se puede eliminar una unidad con contrato asociado.');
            return;
        }

        $unidad->delete();
        session()->flash('message', 'Unidad eliminada correctamente.');
    }

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getExportQuery()
    {
        $query = $this->moto->unidades()->with(['sucursal']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('vin', 'like', '%' . $this->search . '%')
                  ->orWhere('numero_motor', 'like', '%' . $this->search . '%')
                  ->orWhere('placa', 'like', '%' . $this->search . '%')
                  ->orWhere('color_especifico', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->estado !== '') {
            $query->where('estado', $this->estado);
        }

        return $query->orderBy($this->sortBy, $this->sortDirection);
    }

    public function getExportHeaders(): array
    {
        return [
            'ID',
            'VIN / Chasis',
            'Motor',
            'Placa',
            'Color',
            'Kilometraje',
            'Precio Venta',
            'Estado',
            'Sucursal',
            'Fecha Ingreso'
        ];
    }

    public function formatExportRow($row): array
    {
        return [
            $row->id,
            $row->vin,
            $row->numero_motor,
            $row->placa,
            $row->color_especifico,
            $row->kilometraje,
            $row->precio_venta,
            ucfirst($row->estado),
            $row->sucursal->nombre ?? 'N/A',
            $row->fecha_ingreso ? $row->fecha_ingreso->format('d/m/Y') : ''
        ];
    }

    public function render()
    {
        $query = $this->moto->unidades()->with(['sucursal']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('vin', 'like', '%' . $this->search . '%')
                  ->orWhere('numero_motor', 'like', '%' . $this->search . '%')
                  ->orWhere('placa', 'like', '%' . $this->search . '%')
                  ->orWhere('color_especifico', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->estado !== '') {
            $query->where('estado', $this->estado);
        }

        $unidades = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        // Estadísticas
        $totalUnidades = $this->moto->unidades()->count();
        $disponibles = $this->moto->unidades()->where('estado', 'disponible')->count();
        $vendidas = $this->moto->unidades()->where('estado', 'vendido')->count();
        $reservadas = $this->moto->unidades()->where('estado', 'reservado')->count();

        return view('livewire.admin.motos.unidades.index', [
            'unidades' => $unidades,
            'totalUnidades' => $totalUnidades,
            'disponibles' => $disponibles,
            'vendidas' => $vendidas,
            'reservadas' => $reservadas
        ])->layout($this->getLayout());
    }
}
