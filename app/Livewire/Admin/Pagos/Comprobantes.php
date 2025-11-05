<?php

namespace App\Livewire\Admin\Pagos;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Comprobante;

class Comprobantes extends Component
{
    use HasDynamicLayout;


    public Comprobante $comprobante;

    public function mount($comprobante)
    {


        $comprobanteId = $comprobante->id;

        $this->comprobante = Comprobante::with([
            'comprobanteable',
            'comprobanteable.user',
            'comprobanteable.matricula.student',
            'comprobanteable.matricula.programa',
            'comprobanteable.conceptoPago'
        ])->findOrFail($comprobanteId);
    }

    public function render()
    {
        return view('livewire.admin.pagos.comprobante')->layout($this->getLayout());
    }
}




