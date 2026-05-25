<?php

namespace App\Livewire\Admin\Iglesias\Finanzas;

use Livewire\Component;
use App\Models\TransaccionFinanciera;
use App\Models\Iglesia;
use App\Traits\HasDynamicLayout;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use HasDynamicLayout;
    public $iglesiaId;
    public $search = '';
    public $tipoFiltro = '';
    public $fechaInicio = '';
    public $fechaFin = '';
    public $metodoPagoFiltro = '';
    public $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'tipoFiltro' => ['except' => ''],
        'fechaInicio' => ['except' => ''],
        'fechaFin' => ['except' => ''],
    ];

    public function mount($iglesiaId = null)
    {
        $this->iglesiaId = $iglesiaId;

        // Establecer fechas por defecto (mes actual)
        if (!$this->fechaInicio) {
            $this->fechaInicio = now()->startOfMonth()->format('Y-m-d');
        }
        if (!$this->fechaFin) {
            $this->fechaFin = now()->endOfMonth()->format('Y-m-d');
        }
    }

    public function render()
    {
        $iglesia = Iglesia::findOrFail($this->iglesiaId);

        $query = TransaccionFinanciera::where('iglesia_id', $this->iglesiaId)
            ->with(['asientoContable'])
            ->orderBy('fecha', 'desc')
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($this->search) {
            $query->where(function($q) {
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('referencia', 'like', '%' . $this->search . '%');
            });
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

        $transacciones = $query->paginate($this->perPage);

        // Calcular totales del período
        $totales = $this->calcularTotales($iglesia->id);

        return view('livewire.admin.iglesias.finanzas.index', compact(
            'iglesia',
            'transacciones',
            'totales'
        ))->layout($this->getLayout());
    }

    private function calcularTotales($iglesiaId)
    {
        $query = TransaccionFinanciera::where('iglesia_id', $iglesiaId);

        if ($this->fechaInicio) {
            $query->whereDate('fecha', '>=', $this->fechaInicio);
        }

        if ($this->fechaFin) {
            $query->whereDate('fecha', '<=', $this->fechaFin);
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

        return [
            'ingresos_ves' => $ingresosVES,
            'egresos_ves' => $egresosVES,
            'balance_ves' => $ingresosVES - $egresosVES,
            'ingresos_usd' => $ingresosUSD,
            'egresos_usd' => $egresosUSD,
            'balance_usd' => $ingresosUSD - $egresosUSD,
        ];
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->tipoFiltro = '';
        $this->metodoPagoFiltro = '';
        $this->fechaInicio = now()->startOfMonth()->format('Y-m-d');
        $this->fechaFin = now()->endOfMonth()->format('Y-m-d');
    }
}
