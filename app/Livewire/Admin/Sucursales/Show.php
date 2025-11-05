<?php

namespace App\Livewire\Admin\Sucursales;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Sucursal;

class Show extends Component
{
    use HasDynamicLayout;

    public $sucursal;

    public function mount(Sucursal $sucursal)
    {
        $this->sucursal = $sucursal;
    }

    public function render()
    {
        return view('livewire.admin.sucursales.show')->layout($this->getLayout());
    }
}



