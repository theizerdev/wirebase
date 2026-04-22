<?php

namespace App\Livewire\Admin\Inventario\Unidades;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\MotoUnidad;
use App\Models\Contrato;
use App\Models\PlanPago;
use App\Models\PagoDetalle;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    use HasDynamicLayout;

    public MotoUnidad $unidad;
    public $lazyLoaded = false;
    public $contratos = [];
    public $pagosCuotas = [];
    public $pagosCliente = [];
    public $resumen = [
        'total_pagado' => 0,
        'pendiente' => 0,
        'proximo_pago' => null,
        'estado' => 'pendiente'
    ];

    public function mount(MotoUnidad $unidad)
    {
        if (!Auth::user()->can('access moto unidades')) {
            abort(403);
        }
        $this->unidad = $unidad->load(['moto', 'empresa', 'sucursal']);
    }

    public function loadData()
    {
        if ($this->lazyLoaded) return;
        $this->contratos = Contrato::with(['cliente'])
            ->where('moto_unidad_id', $this->unidad->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
        
        $contratoIds = Contrato::where('moto_unidad_id', $this->unidad->id)->pluck('id');
        $planIds = PlanPago::whereIn('contrato_id', $contratoIds)->pluck('id');
        
        // Pagos vinculados a cuotas
        $detalles = PagoDetalle::with(['pago', 'conceptoPago'])
            ->whereIn('plan_pago_id', $planIds)
            ->orderBy('created_at', 'desc')
            ->get();
        $this->pagosCuotas = $detalles->map(function($d) {
            return [
                'fecha' => optional($d->pago->fecha)->format('d/m/Y'),
                'monto' => $d->subtotal,
                'estado' => $d->pago->estado,
                'metodo' => $d->pago->metodo_pago,
                'referencia' => $d->pago->referencia,
                'concepto' => $d->conceptoPago->nombre ?? $d->descripcion
            ];
        })->toArray();
        
        // Pagos del cliente
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
        
        // Resumen
        $this->resumen['total_pagado'] = $pagosCliente->sum('total');
        $this->resumen['pendiente'] = PlanPago::whereIn('id', $planIds)->sum('saldo_pendiente');
        $proximo = PlanPago::whereIn('id', $planIds)->where('estado', 'pendiente')->orderBy('fecha_vencimiento')->first();
        $this->resumen['proximo_pago'] = $proximo ? $proximo->fecha_vencimiento->format('d/m/Y') : null;
        $hayVencidos = PlanPago::whereIn('id', $planIds)->where('estado', 'vencido')->exists();
        $this->resumen['estado'] = $hayVencidos ? 'mora' : ($this->resumen['pendiente'] > 0 ? 'pendiente' : 'al_dia');
        
        $this->lazyLoaded = true;
    }

    public function render()
    {
        return view('livewire.admin.inventario.unidades.show')->layout($this->getLayout(), [
            'title' => 'Detalle de Unidad'
        ]);
    }
}
