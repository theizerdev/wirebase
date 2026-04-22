<?php

namespace App\Livewire\Admin\Sorteo;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sorteo;
use App\Models\Contrato;
use App\Models\SorteoContratoGanador;
use App\Models\Empresa;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '';
    public $empresa_id = '';
    public $sortBy = 'fecha_sorteo';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'empresa_id' => ['except' => ''],
        'sortBy' => ['except' => 'fecha_sorteo'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'empresa_id', 'sortBy', 'sortDirection', 'perPage']);
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

    public function render()
    {
        $query = Sorteo::query()->with(['ganador.contrato.cliente', 'ejecutadoPor', 'empresa']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('numero_contrato_ganador', 'like', '%' . $this->search . '%')
                  ->orWhere('nombre', 'like', '%' . $this->search . '%')
                  ->orWhereHas('ganador.contrato.cliente', function ($q) {
                      $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('apellido', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->empresa_id) {
            $query->where('empresa_id', $this->empresa_id);
        }

        $sorteos = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $empresas = Empresa::all();

        // Stats
        $totalSorteos = Sorteo::count();
        $totalGanadores = SorteoContratoGanador::count();
        $contratosElegibles = Contrato::whereIn('estado', ['activo', 'mora'])
            ->whereNotIn('id', SorteoContratoGanador::pluck('contrato_id')->toArray())
            ->count();
        $ultimoSorteo = Sorteo::orderByDesc('fecha_sorteo')->first();

        return view('livewire.admin.sorteo.index', [
            'sorteos' => $sorteos,
            'empresas' => $empresas,
            'totalSorteos' => $totalSorteos,
            'totalGanadores' => $totalGanadores,
            'contratosElegibles' => $contratosElegibles,
            'ultimoSorteo' => $ultimoSorteo,
        ])->layout($this->getLayout());
    }
}
