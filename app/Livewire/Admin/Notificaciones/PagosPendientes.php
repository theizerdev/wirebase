<?php

namespace App\Livewire\Admin\Notificaciones;

use App\Traits\HasDynamicLayout;
use Livewire\Component;

class PagosPendientes extends Component
{
    use HasDynamicLayout;


    public function render()
    {
        return view('livewire.admin.notificaciones.pagos-pendientes')->layout($this->getLayout());
    }
}




