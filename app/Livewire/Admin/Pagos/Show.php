<?php

namespace App\Livewire\Admin\Pagos;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use App\Models\Pago;

class Show extends Component
{
    use HasDynamicLayout, HasRegionalFormatting;


    public $pago;

    public function mount(Pago $pago)
    {
        $this->pago = $pago->load(['matricula.student', 'matricula.programa', 'detalles.conceptoPago', 'serieModel']);
    }

    public function render()
    {
        return view('livewire.admin.pagos.show')->layout($this->getLayout());
    }
}
