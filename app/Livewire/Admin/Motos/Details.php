<?php

namespace App\Livewire\Admin\Motos;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Moto;
use App\Models\Contrato;
use App\Models\PlanPago;
use App\Models\PagoDetalle;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;

class Details extends Component
{
    use HasDynamicLayout;

    public Moto $moto;
    public $lazyLoaded = false;
    public $contratos = [];
    public $planPagos = [];
    public $pagos = [];
    public $pagosCliente = [];
    public $resumen = [
        'total_pagado' => 0,
        'pendiente' => 0,
        'proximo_pago' => null,
        'estado' => 'pendiente'
    ];

    public function mount(Moto $moto)
    {
        if (!Auth::user()->can('access motos')) {
            abort(403);
        }
        $this->moto = $moto;
    }

    public function loadData()
    {
        if ($this->lazyLoaded) return;
        $unidadIds = $this->moto->unidades()->pluck('id');
        $this->contratos = Contrato::with(['cliente', 'unidad'])
            ->whereIn('moto_unidad_id', $unidadIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
        
        $contratoIds = Contrato::whereIn('moto_unidad_id', $unidadIds)->pluck('id');
        $this->planPagos = PlanPago::with(['contrato', 'empresa'])
            ->whereIn('contrato_id', $contratoIds)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get()
            ->toArray();
        
        $planIds = PlanPago::whereIn('contrato_id', $contratoIds)->pluck('id');
        $detalles = PagoDetalle::with(['pago', 'conceptoPago'])
            ->whereIn('plan_pago_id', $planIds)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $this->pagos = $detalles->map(function($d) {
            return [
                'fecha' => optional($d->pago->fecha)->format('d/m/Y'),
                'monto' => $d->subtotal,
                'estado' => $d->pago->estado,
                'metodo' => $d->pago->metodo_pago,
                'referencia' => $d->pago->referencia,
                'concepto' => $d->conceptoPago->nombre ?? $d->descripcion
            ];
        })->toArray();
        
        // Pagos del cliente (no necesariamente vinculados a cuotas)
        $clienteIds = Contrato::whereIn('id', $contratoIds)->pluck('cliente_id');
        $pagosCliente = Pago::whereIn('cliente_id', $clienteIds)
            ->where('estado', 'aprobado')
            ->orderBy('fecha', 'desc')
            ->limit(50)
            ->get();
        $this->pagosCliente = $pagosCliente->map(function($p) {
            return [
                'fecha' => optional($p->fecha)->format('d/m/Y'),
                'total' => $p->total,
                'metodo' => $p->metodo_pago,
                'referencia' => $p->referencia,
                'documento' => $p->numero_completo ?? ($p->serie . '-' . str_pad($p->numero, 8, '0', STR_PAD_LEFT))
            ];
        })->toArray();
        
        // Resumen financiero
        $this->resumen['total_pagado'] = $pagosCliente->sum('total');
        $this->resumen['pendiente'] = PlanPago::whereIn('id', $planIds)->sum('saldo_pendiente');
        $proximo = PlanPago::whereIn('id', $planIds)->where('estado', 'pendiente')->orderBy('fecha_vencimiento')->first();
        $this->resumen['proximo_pago'] = $proximo ? $proximo->fecha_vencimiento->format('d/m/Y') : null;
        
        // Indicador de estado
        $hayVencidos = PlanPago::whereIn('id', $planIds)->where('estado', 'vencido')->exists();
        if ($hayVencidos) {
            $this->resumen['estado'] = 'mora';
        } else {
            $this->resumen['estado'] = $this->resumen['pendiente'] > 0 ? 'pendiente' : 'al_dia';
        }
        
        $this->lazyLoaded = true;
    }

    public function render()
    {
        return view('livewire.admin.motos.details')->layout($this->getLayout(), [
            'title' => 'Detalles de Moto'
        ]);
    }
}
