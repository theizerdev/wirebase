<?php

namespace App\Livewire\Admin\Cajas;

use App\Models\Caja;
use App\Traits\HasDynamicLayout;
use Livewire\Component;

class Create extends Component
{
    use HasDynamicLayout;


    public $monto_inicial = 0;
    public $observaciones_apertura = '';

    protected $rules = [
        'monto_inicial' => 'required|numeric|min:0',
        'observaciones_apertura' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $user = auth()->user();
        $empresaId = $user->empresa_id ?? 1;
        $sucursalId = $user->sucursal_id ?? 1;

        // Verificar si ya existe una caja abierta
        $cajaAbierta = Caja::where('empresa_id', $empresaId)
            ->where('sucursal_id', $sucursalId)
            ->where('estado', 'abierta')
            ->first();

        if ($cajaAbierta) {
            session()->flash('error', 'Ya existe una caja abierta. Debe cerrarla antes de abrir una nueva.');
            return redirect()->route('admin.cajas.index');
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $user = auth()->user();
            $empresaId = $user->empresa_id ?? 1;
            $sucursalId = $user->sucursal_id ?? 1;

            // Verificar si ya existe una caja abierta
            $cajaAbierta = Caja::where('empresa_id', $empresaId)
                ->where('sucursal_id', $sucursalId)
                ->where('estado', 'abierta')
                ->first();

            if ($cajaAbierta) {
                session()->flash('error', 'Ya existe una caja abierta. Debe cerrarla antes de abrir una nueva.');
                return;
            }

            // Verificar si ya existe alguna caja para hoy
            $cajaHoy = Caja::where('empresa_id', $empresaId)
                ->where('sucursal_id', $sucursalId)
                ->whereDate('fecha', today())
                ->exists();

            if ($cajaHoy) {
                // Es un corte de caja
                $caja = Caja::crearCorte(
                    $empresaId,
                    $sucursalId,
                    $this->monto_inicial,
                    $this->observaciones_apertura,
                    auth()->id()
                );
            } else {
                // Es la primera caja del día
                $caja = Caja::crearCajaDiaria(
                    $empresaId,
                    $sucursalId,
                    $this->monto_inicial,
                    $this->observaciones_apertura,
                    auth()->id()
                );
            }

            session()->flash('message', 'Caja abierta exitosamente.');
            return redirect()->route('admin.cajas.show', $caja);
        } catch (\Exception $e) {
              dd($e);
            session()->flash('error', 'Error al abrir la caja: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.cajas.create')->layout($this->getLayout());
    }
}
