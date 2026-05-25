<?php

namespace App\Livewire\Admin\Contabilidad;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use App\Models\CuentaContable;
use App\Models\AsientoDetalle;
use App\Models\AsientoContable;

class BalanceComprobacion extends Component
{
    use HasDynamicLayout, HasRegionalFormatting;

    public $fecha_desde;
    public $fecha_hasta;
    public $tipo_cuenta = '';
    public $mostrar_saldos_cero = false;
    public $nivel_detalle = 'todos'; // todos, solo_padres, solo_hijas
    public $search = '';
    
    // Properties for church search
    public $iglesiaSearch = '';
    public $iglesiasBuscadas = [];
    public $mostrarResultadosIglesia = false;
    public $iglesia_id = '';

    protected $queryString = [
        'tipo_cuenta' => ['except' => ''],
        'mostrar_saldos_cero' => ['except' => false],
        'nivel_detalle' => ['except' => 'todos'],
        'iglesia_id' => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('access contabilidad');
        $this->fecha_desde = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_hasta = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingIglesiaId()
    {
        // Reset page if pagination is added in the future
    }

    public function updatedIglesiaSearch($value)
    {
        if (strlen($value) >= 2) {
            $this->iglesiasBuscadas = \App\Models\Iglesia::activas()
                ->where('empresa_id', auth()->user()->empresa_id)
                ->where(function($query) use ($value) {
                    $query->where('nombre', 'like', '%' . $value . '%')
                          ->orWhere('direccion', 'like', '%' . $value . '%');
                })
                ->orderBy('nombre')
                ->limit(10)
                ->get();
            $this->mostrarResultadosIglesia = true;
        } else {
            $this->iglesiasBuscadas = [];
            $this->mostrarResultadosIglesia = false;
        }
    }

    public function seleccionarIglesia($iglesiaId)
    {
        $iglesia = \App\Models\Iglesia::find($iglesiaId);
        if ($iglesia) {
            $this->iglesia_id = $iglesiaId;
            $this->iglesiaSearch = $iglesia->nombre;
            $this->mostrarResultadosIglesia = false;
        }
    }

    public function limpiarBusquedaIglesia()
    {
        $this->iglesia_id = '';
        $this->iglesiaSearch = '';
        $this->iglesiasBuscadas = [];
        $this->mostrarResultadosIglesia = false;
    }

    #[\Livewire\Attributes\On('closeIglesiaDropdown')]
    public function closeIglesiaDropdown()
    {
        $this->mostrarResultadosIglesia = false;
    }

    public function resetFilters()
    {
        $this->reset(['tipo_cuenta', 'mostrar_saldos_cero', 'nivel_detalle', 'search', 'iglesia_id', 'iglesiaSearch']);
        $this->fecha_desde = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_hasta = now()->endOfMonth()->format('Y-m-d');
    }

    public function exportarExcel()
    {
        return redirect()->route('admin.contabilidad.balance-comprobacion.excel', [
            'desde' => $this->fecha_desde,
            'hasta' => $this->fecha_hasta,
            'tipo_cuenta' => $this->tipo_cuenta,
            'mostrar_saldos_cero' => $this->mostrar_saldos_cero,
            'iglesia_id' => $this->iglesia_id,
        ]);
    }

    public function exportarPdf()
    {
        return redirect()->route('admin.contabilidad.balance-comprobacion.pdf', [
            'desde' => $this->fecha_desde,
            'hasta' => $this->fecha_hasta,
            'tipo_cuenta' => $this->tipo_cuenta,
            'mostrar_saldos_cero' => $this->mostrar_saldos_cero,
            'iglesia_id' => $this->iglesia_id,
        ]);
    }

    public function getCuentasProperty()
    {
        $query = CuentaContable::where('empresa_id', auth()->user()->empresa_id)
            ->where('acepta_movimientos', true)
            ->where('activo', true)
            ->when($this->tipo_cuenta, fn($q) => $q->where('tipo', $this->tipo_cuenta))
            ->when($this->search, fn($q) => $q->where('codigo', 'like', "%{$this->search}%")
                ->orWhere('nombre', 'like', "%{$this->search}%"))
            ->when($this->nivel_detalle === 'solo_padres', fn($q) => $q->whereNull('cuenta_padre_id'))
            ->when($this->nivel_detalle === 'solo_hijas', fn($q) => $q->whereNotNull('cuenta_padre_id'))
            ->orderBy('codigo');

        return $query->get()
            ->map(function ($cuenta) {
                // Calcular saldo inicial (antes del período)
                $saldoInicialQuery = AsientoDetalle::where('cuenta_id', $cuenta->id)
                    ->whereHas('asiento', fn($q) => $q->where('estado', 'aprobado')
                        ->when($this->iglesia_id, fn($iq) => $iq->where('iglesia_id', $this->iglesia_id))
                        ->whereDate('fecha', '<', $this->fecha_desde));

                $debeInicial = (float) $saldoInicialQuery->sum('debe');
                $haberInicial = (float) (clone $saldoInicialQuery)->sum('haber');
                $saldoInicial = $cuenta->naturaleza === 'deudora' ? ($debeInicial - $haberInicial) : ($haberInicial - $debeInicial);

                // Calcular movimientos del período
                $movimientosQuery = AsientoDetalle::where('cuenta_id', $cuenta->id)
                    ->whereHas('asiento', fn($q) => $q->where('estado', 'aprobado')
                        ->when($this->iglesia_id, fn($iq) => $iq->where('iglesia_id', $this->iglesia_id))
                        ->whereBetween('fecha', [$this->fecha_desde, $this->fecha_hasta]));

                $debePeriodo = (float) $movimientosQuery->sum('debe');
                $haberPeriodo = (float) (clone $movimientosQuery)->sum('haber');

                // Calcular saldo final
                $saldoFinal = $saldoInicial + ($cuenta->naturaleza === 'deudora' ? ($debePeriodo - $haberPeriodo) : ($haberPeriodo - $debePeriodo));

                // Determinar saldos deudor y acreedor según la naturaleza de la cuenta
                if ($cuenta->naturaleza === 'deudora') {
                    // Cuentas deudoras (Activos, Gastos): saldo positivo = deudor
                    $saldoDeudor = $saldoFinal > 0 ? $saldoFinal : 0;
                    $saldoAcreedor = $saldoFinal < 0 ? abs($saldoFinal) : 0;
                } else {
                    // Cuentas acreedoras (Pasivos, Patrimonio, Ingresos): saldo positivo = acreedor
                    $saldoDeudor = $saldoFinal < 0 ? abs($saldoFinal) : 0;
                    $saldoAcreedor = $saldoFinal > 0 ? $saldoFinal : 0;
                }

                return (object) [
                    'id' => $cuenta->id,
                    'codigo' => $cuenta->codigo,
                    'nombre' => $cuenta->nombre,
                    'tipo' => $cuenta->tipo,
                    'naturaleza' => $cuenta->naturaleza,
                    'nivel' => $cuenta->nivel,
                    'saldo_inicial' => $saldoInicial,
                    'debe_periodo' => $debePeriodo,
                    'haber_periodo' => $haberPeriodo,
                    'saldo_final' => $saldoFinal,
                    'saldo_deudor' => $saldoDeudor,
                    'saldo_acreedor' => $saldoAcreedor,
                    'tiene_movimientos' => $debePeriodo > 0 || $haberPeriodo > 0 || abs($saldoInicial) > 0.01,
                ];
            })
            ->filter(function($cuenta) {
                if ($this->mostrar_saldos_cero) {
                    return true;
                }
                return $cuenta->tiene_movimientos;
            })
            ->values();
    }

    public function getTotalesProperty()
    {
        $cuentas = $this->cuentas;
        return (object) [
            'saldo_inicial_deudor' => $cuentas->where('saldo_inicial', '>=', 0)->sum('saldo_inicial'),
            'saldo_inicial_acreedor' => abs($cuentas->where('saldo_inicial', '<', 0)->sum('saldo_inicial')),
            'debe_periodo' => $cuentas->sum('debe_periodo'),
            'haber_periodo' => $cuentas->sum('haber_periodo'),
            'saldo_final_deudor' => $cuentas->sum('saldo_deudor'),
            'saldo_final_acreedor' => $cuentas->sum('saldo_acreedor'),
        ];
    }

    public function getStatsProperty()
    {
        $empresaId = auth()->user()->empresa_id;

        return [
            'total_cuentas' => CuentaContable::where('empresa_id', $empresaId)
                ->where('acepta_movimientos', true)
                ->where('activo', true)
                ->count(),
            'cuentas_con_saldo' => $this->cuentas->where('tiene_movimientos', true)->count(),
            'total_asientos' => AsientoContable::where('empresa_id', $empresaId)
                ->where('estado', 'aprobado')
                ->whereBetween('fecha', [$this->fecha_desde, $this->fecha_hasta])
                ->count(),
            'balance_cuadrado' => abs($this->totales->saldo_final_deudor - $this->totales->saldo_final_acreedor) < 0.01,
        ];
    }

    public function getResumenPorTipoProperty()
    {
        $cuentas = $this->cuentas;

        return $cuentas->groupBy('tipo')->map(function($cuentasTipo, $tipo) {
            return (object) [
                'tipo' => $tipo,
                'cantidad' => $cuentasTipo->count(),
                'saldo_deudor' => $cuentasTipo->sum('saldo_deudor'),
                'saldo_acreedor' => $cuentasTipo->sum('saldo_acreedor'),
                'debe_periodo' => $cuentasTipo->sum('debe_periodo'),
                'haber_periodo' => $cuentasTipo->sum('haber_periodo'),
            ];
        });
    }

    protected function getPageTitle(): string
    {
        return 'Balance de Comprobación';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.contabilidad.balance-comprobacion' => 'Balance de Comprobación'
        ];
    }

    public function render()
    {
        return view('livewire.admin.contabilidad.balance-comprobacion', [
            'cuentas' => $this->cuentas,
            'totales' => $this->totales,
            'stats' => $this->stats,
            'resumenPorTipo' => $this->resumenPorTipo,
            'regionalConfig' => $this->getRegionalConfig(),
        ])->layout($this->getLayout());
    }
}
