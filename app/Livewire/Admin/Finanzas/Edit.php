<?php

namespace App\Livewire\Admin\Finanzas;

use Livewire\Component;
use App\Models\TransaccionFinanciera;
use App\Models\Iglesia;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Edit extends Component
{
    use HasDynamicLayout;

    public $transactionId;
    
    // Búsqueda de iglesias
    public $iglesiaSearch = '';
    public $iglesiasBuscadas = [];
    public $mostrarResultadosIglesia = false;
    public $iglesia_id = '';
    
    // Datos del formulario
    public string $tipo = '';
    public string $fecha = '';
    public float $montoVes = 0;
    public ?float $montoUsd = null;
    public ?float $tasaCambio = null;
    public string $metodoPago = '';
    public ?string $descripcion = null;
    public ?string $referencia = null;
    public bool $loadingRate = false;

    protected function rules(): array
    {
        return [
            'iglesia_id' => 'required|exists:iglesias,id',
            'tipo' => 'required|string',
            'fecha' => 'required|date',
            'montoVes' => 'required|numeric|min:0',
            'montoUsd' => 'nullable|numeric|min:0',
            'metodoPago' => 'required|string',
            'descripcion' => 'nullable|string|max:500',
            'referencia' => 'nullable|string|max:100',
        ];
    }

    public function mount($id)
    {
        $this->transactionId = $id;
        $transaccion = TransaccionFinanciera::find($id);
        
        if (!$transaccion) {
            session()->flash('error', 'Transacción no encontrada.');
            return redirect()->route('admin.finanzas.index');
        }

        $this->iglesia_id = $transaccion->iglesia_id;
        $this->iglesiaSearch = $transaccion->iglesia ? $transaccion->iglesia->nombre : '';
        $this->tipo = $transaccion->tipo;
        $this->fecha = $transaccion->fecha->format('Y-m-d');
        $this->montoVes = $transaccion->monto_bs;
        $this->montoUsd = $transaccion->monto;
        $this->tasaCambio = $transaccion->tasa_cambio;
        $this->metodoPago = $transaccion->metodo_pago;
        $this->descripcion = $transaccion->descripcion;
        $this->referencia = $transaccion->referencia_bancaria;
    }

    public function updatedFecha(): void
    {
        // Cuando cambia la fecha, obtener la tasa BCV de ese día
        if ($this->fecha) {
            $this->loadingRate = true;

            try {
                $tasa = \App\Models\ExchangeRate::getRateByDate($this->fecha);
                if ($tasa) {
                    $this->tasaCambio = $tasa;
                    session()->flash('message', "Tasa BCV del {$this->fecha} cargada: Bs " . number_format($tasa, 2, ',', '.'));
                    // Recalcular montos si ya hay valores
                    if ($this->montoVes > 0 || $this->montoUsd > 0) {
                        $this->recalcularMontos();
                    }
                } else {
                    session()->flash('warning', 'No se pudo obtener la tasa BCV para esta fecha. Por favor ingrésela manualmente.');
                }
            } finally {
                $this->loadingRate = false;
            }
        }
    }

    public function updatedMontoVes()
    {
        $this->recalcularMontos();
    }

    public function updatedMontoUsd()
    {
        $this->recalcularMontos();
    }

    public function updatedTasaCambio()
    {
        $this->recalcularMontos();
    }

    protected function recalcularMontos(): void
    {
        if (!$this->tasaCambio || $this->tasaCambio <= 0) {
            return;
        }

        // Si hay monto en VES, calcular USD
        if ($this->montoVes > 0) {
            $this->montoUsd = round($this->montoVes / $this->tasaCambio, 2);
        }
        // Si hay monto en USD, calcular VES
        elseif ($this->montoUsd > 0) {
            $this->montoVes = round($this->montoUsd * $this->tasaCambio, 2);
        }
    }

    public function updatedIglesiaSearch($value)
    {
        if (strlen($value) >= 2) {
            $this->iglesiasBuscadas = Iglesia::activas()
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
        $iglesia = Iglesia::find($iglesiaId);
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

    #[On('closeIglesiaDropdown')]
    public function closeIglesiaDropdown()
    {
        $this->mostrarResultadosIglesia = false;
    }

    public function update()
    {
        $this->validate();

        $transaccion = TransaccionFinanciera::find($this->transactionId);
        
        if (!$transaccion) {
            session()->flash('error', 'Transacción no encontrada.');
            return redirect()->route('admin.finanzas.index');
        }

        // Obtener información de la iglesia
        $iglesia = Iglesia::find($this->iglesia_id);
        
        if (!$iglesia) {
            session()->flash('error', 'Extensión no encontrada.');
            return redirect()->route('admin.finanzas.edit', $this->transactionId);
        }

        $transaccion->update([
            'iglesia_id' => $this->iglesia_id,
            'empresa_id' => $iglesia->empresa_id,
            'sucursal_id' => $iglesia->sucursal_id,
            'tipo' => $this->tipo,
            'fecha' => $this->fecha,
            'monto' => $this->montoUsd ?? 0,
            'moneda' => 'USD',
            'monto_bs' => $this->montoVes,
            'tasa_cambio' => $this->tasaCambio,
            'metodo_pago' => $this->metodoPago,
            'descripcion' => $this->descripcion,
            'referencia_bancaria' => $this->referencia,
        ]);

        session()->flash('message', 'Transacción actualizada exitosamente.');
        return redirect()->route('admin.finanzas.index');
    }

    public function render()
    {
        return view('livewire.admin.finanzas.edit')->layout($this->getLayout());
    }
}
