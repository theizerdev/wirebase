<?php

namespace App\Livewire\Admin\ConceptosPago;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ConceptoPago;
use App\Traits\HasDynamicLayout;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, HasDynamicLayout, Exportable;

    public $search = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $totalConceptos = 0;
    public $conceptosActivos = 0;
    public $conceptosInactivos = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
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

    private function getBaseQuery()
    {
        $query = ConceptoPago::query();
        if (auth()->check() && !auth()->user()->hasRole('Super Administrador')) {
            $query = ConceptoPago::withoutGlobalScope('multitenancy')
                ->where(function ($q) {
                    if (auth()->user()->empresa_id) {
                        $q->where('empresa_id', auth()->user()->empresa_id);
                    }
                    if (auth()->user()->sucursal_id) {
                        $q->where('sucursal_id', auth()->user()->sucursal_id);
                    }
                });
        }
        return $query
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status !== '', function ($query) {
                $query->where('activo', $this->status);
            });
    }

    public function delete(ConceptoPago $concepto)
    {
        // Verificar permiso para eliminar conceptos de pago
        if (!auth()->user()->can('delete conceptos_pago')) {
            session()->flash('error', 'No tienes permiso para eliminar conceptos de pago.');
            return;
        }

        try {
            $concepto->delete();
            session()->flash('message', 'Concepto de pago eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el concepto de pago: ' . $e->getMessage());
        }

        $this->resetPage();
    }

    public function toggleStatus(ConceptoPago $concepto)
    {
        // Verificar permiso para editar conceptos de pago
        if (!auth()->user()->can('edit conceptos_pago')) {
            session()->flash('error', 'No tienes permiso para editar conceptos de pago.');
            return;
        }

        try {
            $concepto->activo = !$concepto->activo;
            $concepto->save();
            session()->flash('message', 'Estado del concepto de pago actualizado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el estado del concepto de pago: ' . $e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function render()
    {
        $conceptos = $this->getBaseQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $base = $this->getBaseQuery();
        $this->totalConceptos = (clone $base)->count();
        $this->conceptosActivos = (clone $base)->where('activo', 1)->count();
        $this->conceptosInactivos = (clone $base)->where('activo', 0)->count();

        return $this->renderWithLayout('livewire.admin.conceptos-pago.index', compact('conceptos'), [
            'title' => 'Lista de Conceptos de Pago',
            'description' => 'Gestión de conceptos de pago',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.conceptos-pago.index' => 'Conceptos de Pago'
            ]
        ]);
    }

    protected function getExportQuery()
    {
        return $this->getBaseQuery()->orderBy($this->sortBy, $this->sortDirection);
    }

    protected function getExportHeaders(): array
    {
        return ['ID', 'Nombre', 'Descripción', 'Activo', 'Empresa', 'Sucursal', 'Fecha'];
    }

    protected function formatExportRow($row): array
    {
        return [
            $row->id,
            $row->nombre,
            $row->descripcion,
            $row->activo ? 'Sí' : 'No',
            $row->empresa_id,
            $row->sucursal_id,
            optional($row->created_at)->format('d/m/Y H:i')
        ];
    }
}
