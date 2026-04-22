<?php

namespace App\Livewire\Admin\Motos;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Moto;
use App\Models\Empresa;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout;

    public $search = '';
    public $activo = '';
    public $empresa_id = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'activo' => ['except' => ''],
        'empresa_id' => ['except' => ''],
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
        $this->reset(['search', 'activo', 'empresa_id', 'sortBy', 'sortDirection', 'perPage']);
    }

    public function delete($id)
    {
        $moto = Moto::findOrFail($id);
        $moto->delete();
        session()->flash('message', 'Motocicleta eliminada correctamente.');
    }

    public function toggleStatus($id)
    {
        $moto = Moto::findOrFail($id);
        $moto->activo = !$moto->activo;
        $moto->save();
        session()->flash('message', 'Estado actualizado correctamente.');
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
        $query = Moto::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('marca', 'like', '%' . $this->search . '%')
                  ->orWhere('modelo', 'like', '%' . $this->search . '%')
                  ->orWhere('anio', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->activo !== '') {
            $query->where('activo', $this->activo == '1');
        }

        if ($this->empresa_id) {
            $query->where('empresa_id', $this->empresa_id);
        }

        return $query->orderBy($this->sortBy, $this->sortDirection);
    }

    public function getExportHeaders(): array
    {
        return [
            'ID',
            'Marca',
            'Modelo',
            'Año',
            'Color',
            'Cilindrada',
            'Tipo',
            'Precio Base',
            'Costo Referencial',
            'Empresa',
            'Estado',
            'Fecha Creación'
        ];
    }

    public function formatExportRow($row): array
    {
        return [
            $row->id,
            $row->marca,
            $row->modelo,
            $row->anio,
            $row->color_principal,
            $row->cilindrada,
            $row->tipo,
            $row->precio_venta_base,
            $row->costo_referencial,
            $row->empresa->razon_social ?? 'N/A',
            $row->activo ? 'Activo' : 'Inactivo',
            $row->created_at->format('d/m/Y H:i')
        ];
    }

    public function render()
    {
        $query = Moto::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('marca', 'like', '%' . $this->search . '%')
                  ->orWhere('modelo', 'like', '%' . $this->search . '%')
                  ->orWhere('anio', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->activo !== '') {
            $query->where('activo', $this->activo == '1');
        }

        if ($this->empresa_id) {
            $query->where('empresa_id', $this->empresa_id);
        }

        $motos = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $empresas = Empresa::all();
        $totalMotos = Moto::count();
        $motosActivas = Moto::where('activo', true)->count();
        $motosInactivas = Moto::where('activo', false)->count();

        return view('livewire.admin.motos.index', [
            'motos' => $motos,
            'empresas' => $empresas,
            'totalMotos' => $totalMotos,
            'motosActivas' => $motosActivas,
            'motosInactivas' => $motosInactivas
        ])->layout($this->getLayout());
    }
}
