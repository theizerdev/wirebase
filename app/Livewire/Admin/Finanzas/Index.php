<?php

namespace App\Livewire\Admin\Finanzas;

use Livewire\Component;
use App\Models\TransaccionFinanciera;
use App\Models\Iglesia;
use App\Traits\HasDynamicLayout;
use App\Traits\Exportable;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination;
    use HasDynamicLayout;
    use Exportable;

    public string $search = '';
    public string $iglesiaFilter = '';
    public string $tipoFiltro = '';
    public string $fechaInicio = '';
    public string $fechaFin = '';
    public string $metodoPagoFiltro = '';
    public int $perPage = 15;

    public array $stats = [
        'total_transacciones' => 0,
        'total_ingresos_ves' => 0,
        'total_egresos_ves' => 0,
        'balance_ves' => 0,
        'total_ingresos_usd' => 0,
        'total_egresos_usd' => 0,
        'balance_usd' => 0,
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'iglesiaFilter' => ['except' => ''],
        'tipoFiltro' => ['except' => ''],
        'fechaInicio' => ['except' => ''],
        'fechaFin' => ['except' => ''],
    ];

    public function mount()
    {
        // Establecer fechas por defecto (mes actual)
        if (!$this->fechaInicio) {
            $this->fechaInicio = now()->startOfMonth()->format('Y-m-d');
        }
        if (!$this->fechaFin) {
            $this->fechaFin = now()->endOfMonth()->format('Y-m-d');
        }
        
        $this->loadStats();
    }

    protected function loadStats(): void
    {
        $query = TransaccionFinanciera::query();

        // Aplicar filtros a las estadísticas
        if ($this->fechaInicio) {
            $query->whereDate('fecha', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('fecha', '<=', $this->fechaFin);
        }
        if ($this->iglesiaFilter) {
            $query->where('iglesia_id', $this->iglesiaFilter);
        }

        $ingresosVES = (clone $query)->whereIn('tipo', [
            TransaccionFinanciera::TIPO_DIEZMO,
            TransaccionFinanciera::TIPO_OFRENDA,
            TransaccionFinanciera::TIPO_APORTE_ESPECIAL,
            TransaccionFinanciera::TIPO_OTRO_INGRESO,
        ])->sum('monto_bs');

        $egresosVES = (clone $query)->whereIn('tipo', [
            TransaccionFinanciera::TIPO_GASTO_OPERATIVO,
            TransaccionFinanciera::TIPO_GASTO_MINISTERIO,
            TransaccionFinanciera::TIPO_OTRO_EGRESO,
        ])->sum('monto_bs');

        $ingresosUSD = (clone $query)->whereIn('tipo', [
            TransaccionFinanciera::TIPO_DIEZMO,
            TransaccionFinanciera::TIPO_OFRENDA,
            TransaccionFinanciera::TIPO_APORTE_ESPECIAL,
            TransaccionFinanciera::TIPO_OTRO_INGRESO,
        ])->where('moneda', 'USD')->sum('monto');

        $egresosUSD = (clone $query)->whereIn('tipo', [
            TransaccionFinanciera::TIPO_GASTO_OPERATIVO,
            TransaccionFinanciera::TIPO_GASTO_MINISTERIO,
            TransaccionFinanciera::TIPO_OTRO_EGRESO,
        ])->where('moneda', 'USD')->sum('monto');

        $this->stats = [
            'total_transacciones' => (clone $query)->count(),
            'total_ingresos_ves' => $ingresosVES,
            'total_egresos_ves' => $egresosVES,
            'balance_ves' => $ingresosVES - $egresosVES,
            'total_ingresos_usd' => $ingresosUSD,
            'total_egresos_usd' => $egresosUSD,
            'balance_usd' => $ingresosUSD - $egresosUSD,
        ];
    }

    public function updatedIglesiFilter(): void
    {
        $this->resetPage();
        $this->loadStats();
    }

    public function updatedTipoFiltro(): void
    {
        $this->resetPage();
        $this->loadStats();
    }

    public function updatedFechaInicio(): void
    {
        $this->resetPage();
        $this->loadStats();
    }

    public function updatedFechaFin(): void
    {
        $this->resetPage();
        $this->loadStats();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->iglesiaFilter = '';
        $this->tipoFiltro = '';
        $this->metodoPagoFiltro = '';
        $this->fechaInicio = now()->startOfMonth()->format('Y-m-d');
        $this->fechaFin = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
        $this->loadStats();
    }

    public function getTransactionsProperty()
    {
        $query = TransaccionFinanciera::with(['iglesia', 'asientoContable'])
            ->orderBy('fecha', 'desc')
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($this->search) {
            $query->where(function($q) {
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('referencia_bancaria', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->iglesiaFilter) {
            $query->where('iglesia_id', $this->iglesiaFilter);
        }

        if ($this->tipoFiltro) {
            $query->where('tipo', $this->tipoFiltro);
        }

        if ($this->fechaInicio) {
            $query->whereDate('fecha', '>=', $this->fechaInicio);
        }

        if ($this->fechaFin) {
            $query->whereDate('fecha', '<=', $this->fechaFin);
        }

        if ($this->metodoPagoFiltro) {
            $query->where('metodo_pago', $this->metodoPagoFiltro);
        }

        return $query->paginate($this->perPage);
    }

    // Exportable trait methods
    protected function getExportQuery()
    {
        $query = TransaccionFinanciera::with(['iglesia'])->orderBy('fecha', 'desc');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('referencia_bancaria', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->iglesiaFilter) {
            $query->where('iglesia_id', $this->iglesiaFilter);
        }

        if ($this->tipoFiltro) {
            $query->where('tipo', $this->tipoFiltro);
        }

        if ($this->fechaInicio) {
            $query->whereDate('fecha', '>=', $this->fechaInicio);
        }

        if ($this->fechaFin) {
            $query->whereDate('fecha', '<=', $this->fechaFin);
        }

        if ($this->metodoPagoFiltro) {
            $query->where('metodo_pago', $this->metodoPagoFiltro);
        }

        return $query;
    }

    protected function getExportHeaders(): array
    {
        return [
            'ID',
            'Extensión',
            'Fecha',
            'Tipo',
            'Descripción',
            'Monto VES',
            'Monto USD',
            'Tasa BCV',
            'Método de Pago',
            'Referencia',
        ];
    }

    protected function formatExportRow($transaccion): array
    {
        return [
            $transaccion->id,
            $transaccion->iglesia ? $transaccion->iglesia->nombre : '-',
            $transaccion->fecha ? \Carbon\Carbon::parse($transaccion->fecha)->format('d/m/Y') : '-',
            $transaccion->tipo_label ?? $transaccion->tipo,
            $transaccion->descripcion ?? '-',
            number_format($transaccion->monto_bs ?? 0, 2, ',', '.'),
            number_format($transaccion->monto ?? 0, 2, ',', '.'),
            number_format($transaccion->tasa_cambio ?? 0, 4, ',', '.'),
            $transaccion->metodo_pago_label ?? $transaccion->metodo_pago,
            $transaccion->referencia_bancaria ?? '-',
        ];
    }

    public function getExportFileName(): string
    {
        return 'finanzas_' . now()->format('Y-m-d');
    }

    public function render()
    {
        $iglesias = Iglesia::activas()->orderBy('nombre')->get();

        return view('livewire.admin.finanzas.index', [
            'transactions' => $this->transactions,
            'iglesias' => $iglesias,
        ])->layout($this->getLayout());
    }
}
