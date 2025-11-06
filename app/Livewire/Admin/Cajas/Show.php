<?php

namespace App\Livewire\Admin\Cajas;

use App\Models\Caja;
use App\Models\ExchangeRate;
use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use App\Traits\HasDualCurrency;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Show extends Component
{
    use HasDynamicLayout, HasRegionalFormatting, HasDualCurrency;


    public Caja $caja;
    public $observaciones_cierre = '';
    public $showCerrarModal = false;

    protected $rules = [
        'observaciones_cierre' => 'nullable|string|max:500',
    ];

    public function mount(Caja $caja)
    {
        $this->caja = $caja->load(['usuario', 'sucursal', 'pagos.detalles.conceptoPago', 'pagos.matricula.student']);
    }

    public function abrirModalCerrar()
    {
        if ($this->caja->estado === 'cerrada') {
            session()->flash('error', 'La caja ya está cerrada.');
            return;
        }

        $this->caja->calcularTotales();
        $this->showCerrarModal = true;
    }

    public function cerrarCaja()
    {
        $this->validate();

        if ($this->caja->cerrar($this->observaciones_cierre)) {
            $this->showCerrarModal = false;
            session()->flash('message', 'Caja cerrada exitosamente.');
            $this->caja->refresh();
        } else {
            session()->flash('error', 'No se pudo cerrar la caja.');
        }
    }

    public function getResumenPorMetodoProperty()
    {
        return $this->caja->pagos()
            ->where('estado', 'aprobado')
            ->selectRaw('metodo_pago, COUNT(*) as cantidad, SUM(total) as total')
            ->groupBy('metodo_pago')
            ->get();
    }

    public function getResumenPorConceptoProperty()
    {
        return $this->caja->pagos()
            ->where('estado', 'aprobado')
            ->with(['detalles.conceptoPago'])
            ->get()
            ->flatMap(function ($pago) {
                return $pago->detalles->map(function ($detalle) {
                    return [
                        'concepto' => $detalle->conceptoPago->nombre ?? 'Sin concepto',
                        'cantidad' => $detalle->cantidad,
                        'precio' => $detalle->precio_unitario,
                        'subtotal' => $detalle->subtotal,
                    ];
                });
            })
            ->groupBy('concepto')
            ->map(function ($items, $concepto) {
                return [
                    'concepto' => $concepto,
                    'cantidad' => $items->sum('cantidad'),
                    'total' => $items->sum('subtotal'),
                ];
            });
    }

    public function exportarExcel()
    {
        return redirect()->route('admin.cajas.export', $this->caja->id);
    }

    public function render()
    {
        return view('livewire.admin.cajas.show')->layout($this->getLayout());
    }
}
