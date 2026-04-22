<?php

namespace App\Livewire\Admin\Inventario\Unidades;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MotoUnidad;
use App\Models\Moto;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout;

    public $search = '';
    public $estado = '';
    public $moto_id = '';
    public $empresa_id = '';
    public $sucursal_id = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'estado' => ['except' => ''],
        'moto_id' => ['except' => ''],
        'empresa_id' => ['except' => ''],
        'sucursal_id' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'estado', 'moto_id', 'empresa_id', 'sucursal_id', 'sortBy', 'sortDirection', 'perPage']);
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
        $query = MotoUnidad::query()->with(['moto', 'empresa', 'sucursal']);

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

        if ($this->moto_id) {
            $query->where('moto_id', $this->moto_id);
        }

        if ($this->empresa_id) {
            $query->where('empresa_id', $this->empresa_id);
        }

        if ($this->sucursal_id) {
            $query->where('sucursal_id', $this->sucursal_id);
        }

        return $query->orderBy($this->sortBy, $this->sortDirection);
    }

    public function getExportHeaders(): array
    {
        return [
            'ID',
            'Modelo',
            'VIN / Chasis',
            'Motor',
            'Placa',
            'Color',
            'Kilometraje',
            'Precio Venta',
            'Estado',
            'Empresa',
            'Sucursal',
            'Fecha Ingreso'
        ];
    }

    public function formatExportRow($row): array
    {
        return [
            $row->id,
            $row->moto->titulo ?? 'N/A',
            $row->vin,
            $row->numero_motor,
            $row->placa,
            $row->color_especifico,
            $row->kilometraje,
            $row->precio_venta,
            ucfirst($row->estado),
            $row->empresa->razon_social ?? 'N/A',
            $row->sucursal->nombre ?? 'N/A',
            $row->fecha_ingreso ? $row->fecha_ingreso->format('d/m/Y') : ''
        ];
    }

    public function render()
    {
        $query = MotoUnidad::query()->with(['moto', 'empresa', 'sucursal']);

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

        if ($this->moto_id) {
            $query->where('moto_id', $this->moto_id);
        }

        if ($this->empresa_id) {
            $query->where('empresa_id', $this->empresa_id);
        }

        if ($this->sucursal_id) {
            $query->where('sucursal_id', $this->sucursal_id);
        }

        $unidades = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $motos = Moto::all();
        $empresas = Empresa::all();
        $sucursales = $this->empresa_id ? Sucursal::where('empresa_id', $this->empresa_id)->get() : [];

        // Estadísticas Globales
        $totalUnidades = MotoUnidad::count();
        $disponibles = MotoUnidad::where('estado', 'disponible')->count();
        $vendidas = MotoUnidad::where('estado', 'vendido')->count();
        $reservadas = MotoUnidad::where('estado', 'reservado')->count();

        return view('livewire.admin.inventario.unidades.index', [
            'unidades' => $unidades,
            'motos' => $motos,
            'empresas' => $empresas,
            'sucursales' => $sucursales,
            'totalUnidades' => $totalUnidades,
            'disponibles' => $disponibles,
            'vendidas' => $vendidas,
            'reservadas' => $reservadas
        ])->layout($this->getLayout());
    }
}
