<?php

namespace App\Livewire\Admin\Sorteo;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Sorteo;

class Detalle extends Component
{
    use HasDynamicLayout;

    public $sorteo;

    public function mount(Sorteo $sorteo)
    {
        $this->sorteo = $sorteo->load([
            'ganador.contrato.cliente',
            'ganador.contrato.unidad.moto',
            'ganador.contrato.planPagos',
            'ganador.contrato.empresa',
            'ganador.contrato.sucursal',
            'ganador.contrato.vendedor',
            'auditorias',
            'ejecutadoPor',
            'empresa',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.sorteo.detalle')->layout($this->getLayout());
    }
}
