<?php

namespace App\Livewire\Admin\Iglesias\Finanzas;

use Livewire\Component;
use App\Models\TransaccionFinanciera;
use App\Models\Iglesia;
use App\Services\ContabilidadIglesiaService;
use App\Traits\HasDynamicLayout;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    use HasDynamicLayout;
    public $iglesiaId;
    public $tipo = TransaccionFinanciera::TIPO_DIEZMO;
    public $fecha;
    public $montoVes = 0;
    public $montoUsd = 0;
    public $tasaCambio = 1;
    public $metodoPago = TransaccionFinanciera::METODO_EFECTIVO;
    public $descripcion = '';
    public $referencia = '';
    public $miembroId = null;
    public $ministerioId = null;
    public $categoriaGasto = null;

    protected $rules = [
        'tipo' => 'required|string',
        'fecha' => 'required|date',
        'montoVes' => 'required|numeric|min:0',
        'montoUsd' => 'nullable|numeric|min:0',
        'metodoPago' => 'required|string',
        'descripcion' => 'nullable|string|max:500',
        'referencia' => 'nullable|string|max:100',
    ];

    public function mount($iglesiaId)
    {
        $this->iglesiaId = $iglesiaId;
        $this->fecha = now()->format('Y-m-d');

        // Obtener tasa de cambio actual si existe
        try {
            $tasa = \App\Models\ExchangeRate::whereDate('fecha', '<=', now())
                ->orderBy('fecha', 'desc')
                ->first();

            if ($tasa) {
                $this->tasaCambio = $tasa->tasa;
            }
        } catch (\Exception $e) {
            // Si no hay tasa, usar 1
            $this->tasaCambio = 1;
        }
    }

    public function updatedMontoVes()
    {
        // Calcular USD automáticamente si hay tasa
        if ($this->tasaCambio > 0 && $this->montoVes > 0) {
            $this->montoUsd = round($this->montoVes / $this->tasaCambio, 2);
        }
    }

    public function updatedMontoUsd()
    {
        // Calcular VES automáticamente si hay tasa
        if ($this->tasaCambio > 0 && $this->montoUsd > 0) {
            $this->montoVes = round($this->montoUsd * $this->tasaCambio, 2);
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $service = new ContabilidadIglesiaService();

            $data = [
                'iglesia_id' => $this->iglesiaId,
                'fecha' => $this->fecha,
                'monto' => $this->montoUsd ?? 0,
                'moneda' => 'USD',
                'monto_bs' => $this->montoVes,
                'tasa_cambio' => $this->tasaCambio,
                'metodo_pago' => $this->metodoPago,
                'descripcion' => $this->descripcion,
                'referencia_bancaria' => $this->referencia,
                'user_id' => Auth::id(),
            ];

            // Agregar campos opcionales
            if ($this->miembroId) {
                $data['miembro_id'] = $this->miembroId;
            }

            if ($this->ministerioId) {
                $data['ministerio_id'] = $this->ministerioId;
            }

            // Registrar según el tipo
            $transaccion = match($this->tipo) {
                TransaccionFinanciera::TIPO_DIEZMO => $service->registrarDiezmo($data),
                TransaccionFinanciera::TIPO_OFRENDA => $service->registrarOfrenda($data),
                TransaccionFinanciera::TIPO_APORTE_ESPECIAL => $service->registrarAporteEspecial($data),
                TransaccionFinanciera::TIPO_GASTO_OPERATIVO => $service->registrarGastoOperativo($data),
                TransaccionFinanciera::TIPO_GASTO_MINISTERIO => $service->registrarGastoMinisterio($data),
                default => throw new \Exception('Tipo de transacción no válido'),
            };

            session()->flash('message', 'Transacción registrada exitosamente.');

            return redirect()->route('admin.iglesias.finanzas.index', $this->iglesiaId);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar la transacción: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $iglesia = Iglesia::findOrFail($this->iglesiaId);

        return view('livewire.admin.iglesias.finanzas.create', compact('iglesia'))
            ->layout($this->getLayout());
    }
}
