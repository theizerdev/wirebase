<?php

namespace App\Livewire\Admin\Clientes;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Pago;
use Livewire\WithPagination;

class Show extends Component
{
    use HasDynamicLayout, WithPagination;

    public Cliente $cliente;
    public $activeTab = 'general';
    public $searchPagos = '';
    public $searchContratos = '';

    protected $queryString = [
        'activeTab' => ['except' => 'general'],
        'searchPagos' => ['except' => ''],
        'searchContratos' => ['except' => '']
    ];

    public function mount(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
    }

    public function getContratosProperty()
    {
        return Contrato::where('cliente_id', $this->cliente->id)
            ->when($this->searchContratos, function($q) {
                $q->where('numero_contrato', 'like', '%' . $this->searchContratos . '%')
                  ->orWhereHas('unidad.moto', function($q2) {
                      $q2->where('modelo', 'like', '%' . $this->searchContratos . '%');
                  });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'contratosPage');
    }

    public function getPagosProperty()
    {
        return Pago::where('cliente_id', $this->cliente->id)
            ->when($this->searchPagos, function($q) {
                $q->where('numero_completo', 'like', '%' . $this->searchPagos . '%')
                  ->orWhere('referencia', 'like', '%' . $this->searchPagos . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'pagosPage');
    }

    public function getObligacionesPendientesProperty()
    {
        // Obtener todas las cuotas pendientes de todos los contratos activos
        return \App\Models\PlanPago::whereIn('contrato_id', $this->cliente->contratos->pluck('id'))
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();
    }

    public function getStatsProperty()
    {
        return [
            'total_pagado' => Pago::where('cliente_id', $this->cliente->id)->where('estado', 'aprobado')->sum('total'),
            'contratos_activos' => $this->cliente->contratos()->where('estado', 'activo')->count(),
            'deuda_total' => $this->obligacionesPendientes->sum('saldo_pendiente'),
        ];
    }

    public function exportar($formato)
    {
        // TODO: Implementar lógica de exportación real
        $this->dispatch('swal:success', [
            'title' => 'Exportando...',
            'text' => "Generando reporte en formato " . strtoupper($formato)
        ]);
    }

    public function enviarRecordatorio()
    {
        if (!$this->cliente->telefono) {
            $this->dispatch('swal:error', [
                'title' => 'Error',
                'text' => 'El cliente no tiene un número de teléfono registrado.'
            ]);
            return;
        }

        $siguienteCuota = $this->obligacionesPendientes->first();

        if (!$siguienteCuota) {
            $this->dispatch('swal:info', [
                'title' => 'Información',
                'text' => 'El cliente no tiene cuotas pendientes por pagar.'
            ]);
            return;
        }

        try {
            $numero = $this->formatPhoneNumber($this->cliente->telefono);
            $mensaje = $this->generarMensajeRecordatorio($siguienteCuota);
            
            $whatsappService = new \App\Services\WhatsAppService(auth()->user()->empresa_id);
            $response = $whatsappService->sendMessage($numero, $mensaje);

            if ($response && ($response['success'] ?? false)) {
                $this->dispatch('swal:success', [
                    'title' => 'Enviado',
                    'text' => 'Recordatorio de pago enviado al cliente correctamente.'
                ]);
            } else {
                $errorMsg = $response['error'] ?? 'Error desconocido en la API';
                throw new \Exception($errorMsg);
            }
        } catch (\Exception $e) {
            \Log::error('Error enviando recordatorio WhatsApp: ' . $e->getMessage());
            $this->dispatch('swal:error', [
                'title' => 'Error al enviar',
                'text' => 'No se pudo enviar el recordatorio: ' . $e->getMessage()
            ]);
        }
    }

    private function generarMensajeRecordatorio($cuota)
    {
        $fechaVence = $cuota->fecha_vencimiento->format('d/m/Y');
        $monto = number_format($cuota->saldo_pendiente, 2);
        
        $mensaje = "👋 Hola *{$this->cliente->nombre}*,\n\n";
        $mensaje .= "Le recordamos que su próxima cuota de *Inversiones Danger 3000 C.A* vence el día *{$fechaVence}*.\n\n";
        $mensaje .= "📄 *Detalle:*\n";
        $mensaje .= "• Contrato: #{$cuota->contrato_id}\n";
        $mensaje .= "• Cuota: #{$cuota->numero_cuota}\n";
        $mensaje .= "• Monto a Pagar: *$" . $monto . "*\n\n";
        
        if ($cuota->fecha_vencimiento < now()) {
            $diasVencido = $cuota->fecha_vencimiento->diffInDays(now());
            $mensaje .= "⚠️ *Nota:* Esta cuota tiene {$diasVencido} días de retraso.\n\n";
        }
        
        $mensaje .= "Evite recargos por mora realizando su pago a tiempo.\n\n";
        $mensaje .= "Saludos,\n*Equipo Inversiones Danger 3000 C.A*";
        
        return $mensaje;
    }

    private function formatPhoneNumber($number)
    {
        try {
            $service = \App\Services\WhatsAppService::forCompany(auth()->user()->empresa_id);
            return $service->formatPhone($number);
        } catch (\Throwable $e) {
            $cleaned = preg_replace('/[^0-9]/', '', $number);
            if (strlen($cleaned) === 10) {
                return '58' . ltrim($cleaned, '0');
            }
            return ltrim($cleaned, '+');
        }
    }

    public function render()
    {
        return view('livewire.admin.clientes.show')->layout($this->getLayout());
    }
}
