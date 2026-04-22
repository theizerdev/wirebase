<?php

namespace App\Livewire\Admin\Clientes;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cliente;
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
    public $showDeleted = false; // Variable para controlar qué pestaña mostrar

    protected $queryString = [
        'search' => ['except' => ''],
        'activo' => ['except' => ''],
        'empresa_id' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
        'showDeleted' => ['except' => false]
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'activo', 'empresa_id', 'sortBy', 'sortDirection', 'perPage', 'showDeleted']);
    }

    public function delete($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();
        session()->flash('message', 'Cliente eliminado correctamente.');
    }

    public function restore($id)
    {
        $cliente = Cliente::withTrashed()->findOrFail($id);
        $cliente->restore();
        session()->flash('message', 'Cliente restaurado correctamente.');
    }
    
    public function toggleStatus($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->activo = !$cliente->activo;
        $cliente->save();
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

    public function setShowDeleted($show)
    {
        $this->showDeleted = $show;
        // Resetear la página cuando cambiamos de pestaña
        $this->resetPage();
    }

    public function getExportQuery()
    {
        $query = $this->buildBaseQuery();

        return $query->orderBy($this->sortBy, $this->sortDirection);
    }

    public function getExportHeaders(): array
    {
        return [
            'ID',
            'Nombres',
            'Apellidos',
            'Tipo Doc',
            'Documento',
            'Email',
            'Teléfono',
            'Empresa',
            'Estado',
            'Fecha Registro'
        ];
    }

    public function formatExportRow($row): array
    {
        return [
            $row->id,
            $row->nombre,
            $row->apellido,
            $row->tipo_documento,
            $row->documento,
            $row->email,
            $row->telefono,
            $row->empresa->razon_social ?? 'N/A',
            $row->activo ? 'Activo' : 'Inactivo',
            $row->created_at->format('d/m/Y H:i')
        ];
    }

    private function buildBaseQuery()
    {
        if ($this->showDeleted) {
            $query = Cliente::onlyTrashed(); // Solo clientes eliminados
        } else {
            $query = Cliente::query(); // Clientes regulares
            
            if ($this->activo !== '') {
                $query->where('activo', $this->activo == '1');
            }

            if ($this->empresa_id) {
                $query->where('empresa_id', $this->empresa_id);
            }
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('apellido', 'like', '%' . $this->search . '%')
                  ->orWhere('documento', 'like', '%' . $this->search . '%')
                  ->orWhere('telefono', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return $query;
    }

    public function render()
    {
        $query = $this->buildBaseQuery();

        $clientes = $query
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $empresas = Empresa::all();
        
        // Estadísticas para clientes normales
        $totalClientes = Cliente::count();
        $clientesActivos = Cliente::where('activo', true)->count();
        $clientesInactivos = Cliente::where('activo', false)->count();
        
        // Estadística específica para clientes eliminados
        $clientesEliminados = Cliente::onlyTrashed()->count();

        return view('livewire.admin.clientes.index', [
            'clientes' => $clientes,
            'empresas' => $empresas,
            'totalClientes' => $totalClientes,
            'clientesActivos' => $clientesActivos,
            'clientesInactivos' => $clientesInactivos,
            'clientesEliminados' => $clientesEliminados
        ])->layout($this->getLayout());
    }
}