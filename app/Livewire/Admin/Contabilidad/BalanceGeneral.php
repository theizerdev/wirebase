<?php

namespace App\Livewire\Admin\Contabilidad;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\CuentaContable;
use App\Models\AsientoDetalle;
use Carbon\Carbon;
use Livewire\Attributes\Computed;

class BalanceGeneral extends Component
{
    use HasDynamicLayout;

    public $fecha_corte;
    public $mostrar_cuentas_cero = false;
    public $agrupar_por_categoria = true;
    public $comparativo = false;
    public $fecha_comparativa;
    
    // Church search properties
    public $iglesiaSearch = '';
    public $iglesiasBuscadas = [];
    public $mostrarResultadosIglesia = false;
    public $iglesia_id = '';

    protected $queryString = [
        'mostrar_cuentas_cero' => ['except' => false],
        'agrupar_por_categoria' => ['except' => true],
        'comparativo' => ['except' => false],
        'iglesia_id' => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('access contabilidad');
        $this->fecha_corte = now()->format('Y-m-d');
        $this->fecha_comparativa = now()->subYear()->format('Y-m-d');
    }

    public function updatingIglesiaId($value)
    {
        if ($value === '') {
            $this->reset(['iglesiaSearch', 'iglesiasBuscadas', 'mostrarResultadosIglesia']);
        }
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
        $this->reset(['mostrar_cuentas_cero', 'agrupar_por_categoria', 'comparativo']);
        $this->fecha_corte = now()->format('Y-m-d');
        $this->fecha_comparativa = now()->subYear()->format('Y-m-d');
        $this->limpiarBusquedaIglesia();
    }

    private function getSaldoCuenta($cuenta, $fechaCorte)
    {
        $query = AsientoDetalle::where('cuenta_id', $cuenta->id)
            ->whereHas('asiento', fn($q) => $q->where('estado', 'aprobado')
                ->when($this->iglesia_id, fn($iq) => $iq->where('iglesia_id', $this->iglesia_id))
                ->whereDate('fecha', '<=', $fechaCorte));
        
        $debe = (float) $query->sum('debe');
        $haber = (float) (clone $query)->sum('haber');
        
        return $cuenta->naturaleza === 'deudora' ? ($debe - $haber) : ($haber - $debe);
    }

    private function getCuentasPorTipo($tipo, $fechaCorte)
    {
        $cuentas = CuentaContable::where('empresa_id', auth()->user()->empresa_id)
            ->where('tipo', $tipo)
            ->where('acepta_movimientos', true)
            ->where('activo', true)
            ->orderBy('codigo')
            ->get()
            ->map(function($cuenta) use ($fechaCorte) {
                $saldo = $this->getSaldoCuenta($cuenta, $fechaCorte);
                $saldoComparativo = $this->comparativo ? 
                    $this->getSaldoCuenta($cuenta, $this->fecha_comparativa) : 0;
                
                return (object) [
                    'id' => $cuenta->id,
                    'codigo' => $cuenta->codigo,
                    'nombre' => $cuenta->nombre,
                    'categoria' => $this->getCategoriaActivo($cuenta->codigo),
                    'saldo' => $saldo,
                    'saldo_comparativo' => $saldoComparativo,
                    'variacion' => $saldo - $saldoComparativo,
                    'variacion_porcentaje' => $saldoComparativo != 0 ? 
                        (($saldo - $saldoComparativo) / abs($saldoComparativo)) * 100 : 0,
                ];
            });

        if (!$this->mostrar_cuentas_cero) {
            $cuentas = $cuentas->filter(fn($c) => abs($c->saldo) > 0.01);
        }

        return $cuentas;
    }

    private function getCategoriaActivo($codigo)
    {
        if (str_starts_with($codigo, '1.1')) return 'Activo Corriente';
        if (str_starts_with($codigo, '1.2')) return 'Activo No Corriente';
        if (str_starts_with($codigo, '2.1')) return 'Pasivo Corriente';
        if (str_starts_with($codigo, '2.2')) return 'Pasivo No Corriente';
        return 'Otros';
    }

    #[Computed]
    public function activos()
    {
        return $this->getCuentasPorTipo('activo', $this->fecha_corte);
    }

    #[Computed]
    public function pasivos()
    {
        return $this->getCuentasPorTipo('pasivo', $this->fecha_corte);
    }

    #[Computed]
    public function patrimonio()
    {
        return $this->getCuentasPorTipo('patrimonio', $this->fecha_corte);
    }

    #[Computed]
    public function resultadoEjercicio()
    {
        $ingresos = $this->getCuentasPorTipo('ingreso', $this->fecha_corte)->sum('saldo');
        $egresos = $this->getCuentasPorTipo('egreso', $this->fecha_corte)->sum('saldo');
        $costos = $this->getCuentasPorTipo('costo', $this->fecha_corte)->sum('saldo');
        
        return $ingresos - $egresos - $costos;
    }

    #[Computed]
    public function totales()
    {
        $totalActivos = $this->activos->sum('saldo');
        $totalPasivos = $this->pasivos->sum('saldo');
        $totalPatrimonio = $this->patrimonio->sum('saldo') + $this->resultadoEjercicio;
        
        $totalActivosComparativo = $this->comparativo ? $this->activos->sum('saldo_comparativo') : 0;
        $totalPasivosComparativo = $this->comparativo ? $this->pasivos->sum('saldo_comparativo') : 0;
        $totalPatrimonioComparativo = $this->comparativo ? 
            ($this->patrimonio->sum('saldo_comparativo') + $this->getResultadoComparativo()) : 0;
        
        return [
            'activos' => $totalActivos,
            'pasivos' => $totalPasivos,
            'patrimonio' => $totalPatrimonio,
            'activos_comparativo' => $totalActivosComparativo,
            'pasivos_comparativo' => $totalPasivosComparativo,
            'patrimonio_comparativo' => $totalPatrimonioComparativo,
            'ecuacion_balanceada' => round($totalActivos, 2) === round($totalPasivos + $totalPatrimonio, 2),
            'descuadre' => abs($totalActivos - $totalPasivos - $totalPatrimonio),
        ];
    }

    private function getResultadoComparativo()
    {
        if (!$this->comparativo) return 0;
        
        $ingresos = $this->getCuentasPorTipo('ingreso', $this->fecha_comparativa)->sum('saldo');
        $egresos = $this->getCuentasPorTipo('egreso', $this->fecha_comparativa)->sum('saldo');
        $costos = $this->getCuentasPorTipo('costo', $this->fecha_comparativa)->sum('saldo');
        
        return $ingresos - $egresos - $costos;
    }

    #[Computed]
    public function ratiosFinancieros()
    {
        $totales = $this->totales;
        $activoCorriente = $this->activos->where('categoria', 'Activo Corriente')->sum('saldo');
        $pasivoCorriente = $this->pasivos->where('categoria', 'Pasivo Corriente')->sum('saldo');
        
        return [
            'liquidez_corriente' => $pasivoCorriente > 0 ? $activoCorriente / $pasivoCorriente : 0,
            'endeudamiento' => $totales['activos'] > 0 ? ($totales['pasivos'] / $totales['activos']) * 100 : 0,
            'autonomia_financiera' => $totales['activos'] > 0 ? ($totales['patrimonio'] / $totales['activos']) * 100 : 0,
            'rentabilidad_patrimonio' => $totales['patrimonio'] > 0 ? ($this->resultadoEjercicio / $totales['patrimonio']) * 100 : 0,
        ];
    }

    public function exportarPdf()
    {
        return redirect()->route('admin.contabilidad.balance-general.pdf', [
            'fecha' => $this->fecha_corte,
            'comparativo' => $this->comparativo ? 1 : 0,
            'fecha_comparativa' => $this->fecha_comparativa,
        ]);
    }

    protected function getPageTitle(): string
    {
        return 'Balance General';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.contabilidad.balance-general' => 'Balance General'
        ];
    }

    public function render()
    {
        return view('livewire.admin.contabilidad.balance-general', [
            'activos' => $this->activos,
            'pasivos' => $this->pasivos,
            'patrimonio' => $this->patrimonio,
            'totales' => $this->totales,
            'ratios' => $this->ratiosFinancieros,
            'resultado_ejercicio' => $this->resultadoEjercicio,
        ])->layout($this->getLayout());
    }
}
