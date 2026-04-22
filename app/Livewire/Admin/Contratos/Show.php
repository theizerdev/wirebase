<?php

namespace App\Livewire\Admin\Contratos;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Contrato;
use App\Models\PlanPago;

class Show extends Component
{
    use HasDynamicLayout;

    public $contrato;

    public function mount(Contrato $contrato)
    {
        $this->contrato = $contrato->load(['cliente', 'unidad.moto', 'planPagos', 'empresa', 'sucursal', 'vendedor']);
    }

    public function changeStatus($status)
    {
        // Validaciones simples de cambio de estado
        if ($status === 'activo' && $this->contrato->estado === 'borrador') {
            $this->contrato->update(['estado' => 'activo']);
            session()->flash('message', 'Contrato activado correctamente.');
        } elseif ($status === 'cancelado' && $this->contrato->estado !== 'completado') {
            $this->contrato->update(['estado' => 'cancelado']);
            // Liberar unidad
            $this->contrato->unidad->update(['estado' => 'disponible']);
            session()->flash('message', 'Contrato cancelado y unidad liberada.');
        }
    }

    public function render()
    {
        return view('livewire.admin.contratos.show')->layout($this->getLayout());
    }
}
