<?php

namespace App\Livewire\Admin\Pagos;
use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pago;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout, HasRegionalFormatting;

    public $search = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function getStatsProperty()
    {
        // Para usuarios no Super Administrador, usar withoutGlobalScope y aplicar manualmente
        if (auth()->check() && !auth()->user()->hasRole('Super Administrador')) {
            $baseQuery = Pago::withoutGlobalScope('multitenancy')
                ->where(function($query) {
                    if (auth()->user()->empresa_id) {
                        $query->where('pagos.empresa_id', auth()->user()->empresa_id);
                    }
                    if (auth()->user()->sucursal_id) {
                        $query->where('pagos.sucursal_id', auth()->user()->sucursal_id);
                    }
                })
                ->whereHas('matricula', function($q) {
                    $q->whereHas('student');
                });
        } else {
            $baseQuery = Pago::whereHas('matricula', function($q) {
                $q->whereHas('student');
            });
        }

        return [
            'total' => (clone $baseQuery)->count(),
            'aprobados' => (clone $baseQuery)->where('estado', 'aprobado')->count(),
            'pendientes' => (clone $baseQuery)->where('estado', 'pendiente')->count(),
            'ingresos_totales' => (clone $baseQuery)->where('estado', 'aprobado')->sum('total') ?: 0
        ];
    }

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

    public function delete(Pago $pago)
    {
        // Verificar permiso para eliminar pagos
        if (!auth()->user()->can('delete pagos')) {
            session()->flash('error', 'No tienes permiso para eliminar pagos.');
            return;
        }

        try {
            $pago->delete();
            session()->flash('message', 'Pago eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el pago: ' . $e->getMessage());
        }

        $this->resetPage();
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

    public function toggleStatus($pagoId)
    {
        if (!auth()->user()->can('edit pagos')) {
            session()->flash('error', 'No tienes permiso para editar pagos.');
            return;
        }

        $pago = Pago::find($pagoId);
        if ($pago) {
            $pago->estado = $pago->estado === 'aprobado' ? 'pendiente' : 'aprobado';
            $pago->save();
        }
    }

    public function getExportQuery()
    {
        return $this->getQuery();
    }

    public function getExportHeaders()
    {
        return [
            'Documento', 'Estudiante', 'DNI', 'Total', 'Fecha', 'Estado', 'Método Pago'
        ];
    }

    public function formatExportRow($pago)
    {
        $studentName = '';
        $studentDocumento = '';

        if ($pago->matricula && $pago->matricula->student) {
            $studentName = ($pago->matricula->student->nombres ?? '') . ' ' . ($pago->matricula->student->apellidos ?? '');
            $studentDocumento = $pago->matricula->student->documento_identidad ?? '';
        }

        return [
            $pago->numero_completo,
            $studentName,
            $studentDocumento,
            $this->format_money($pago->total),
            $this->format_date($pago->fecha),
            ucfirst($pago->estado),
            $pago->metodo_pago ?? ''
        ];
    }

    private function getQuery()
    {
        // Para usuarios no Super Administrador, usar withoutGlobalScope y aplicar manualmente solo a pagos
        if (auth()->check() && !auth()->user()->hasRole('Super Administrador')) {
            return Pago::withoutGlobalScope('multitenancy')
                ->with(['matricula.student', 'detalles.conceptoPago', 'user', 'serieModel'])
                ->where(function($query) {
                    // Aplicar scope manualmente solo a pagos
                    if (auth()->user()->empresa_id) {
                        $query->where('pagos.empresa_id', auth()->user()->empresa_id);
                    }
                    if (auth()->user()->sucursal_id) {
                        $query->where('pagos.sucursal_id', auth()->user()->sucursal_id);
                    }
                })
                ->whereHas('matricula', function($q) {
                    $q->whereHas('student');
                })
                ->when($this->search, function ($query) {
                    $query->where(function($q) {
                        $q->whereHas('matricula.student', function ($subQuery) {
                            $subQuery->where('nombres', 'like', '%' . $this->search . '%')
                                ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                                ->orWhere('documento_identidad', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('detalles.conceptoPago', function($subQuery) {
                            $subQuery->where('nombre', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere('referencia', 'like', '%' . $this->search . '%')
                        ->orWhere('serie', 'like', '%' . $this->search . '%')
                        ->orWhere('numero', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->status !== '', function ($query) {
                    $query->where('estado', $this->status);
                })
                ->orderBy($this->sortBy, $this->sortDirection);
        }

        // Para Super Administrador, usar el scope normal
        return Pago::with(['matricula.student', 'detalles.conceptoPago', 'user', 'serieModel'])
                    ->whereHas('matricula', function($q) {
                        $q->whereHas('student');
                    })
                    ->when($this->search, function ($query) {
                        $query->where(function($q) {
                            $q->whereHas('matricula.student', function ($subQuery) {
                                $subQuery->where('nombres', 'like', '%' . $this->search . '%')
                                    ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                                    ->orWhere('documento_identidad', 'like', '%' . $this->search . '%');
                            })
                            ->orWhereHas('detalles.conceptoPago', function($subQuery) {
                                $subQuery->where('nombre', 'like', '%' . $this->search . '%');
                            })
                            ->orWhere('referencia', 'like', '%' . $this->search . '%')
                            ->orWhere('serie', 'like', '%' . $this->search . '%')
                            ->orWhere('numero', 'like', '%' . $this->search . '%');
                        });
                    })
                    ->when($this->status !== '', function ($query) {
                        $query->where('estado', $this->status);
                    })
                    ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function render()
    {
        $pagos = $this->getQuery()->paginate($this->perPage);

        // Debug temporal para verificar datos
        \Log::info('=== RENDER DE PAGOS COMPONENT ===', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->roles->first()->name ?? 'no role',
            'is_super_admin' => auth()->user()->hasRole('Super Administrador'),
            'empresa_id' => auth()->user()->empresa_id,
            'sucursal_id' => auth()->user()->sucursal_id,
            'pagos_count' => $pagos->count(),
            'pagos_total' => $pagos->total(),
            'per_page' => $this->perPage,
            'search' => $this->search,
            'status' => $this->status,
            'sql' => $this->getQuery()->toSql(),
            'bindings' => $this->getQuery()->getBindings()
        ]);

        return view('livewire.admin.pagos.index', compact('pagos'))
            ->layout($this->getLayout());
    }
}
