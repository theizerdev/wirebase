<?php

namespace App\Livewire\Admin\Sucursales;

use Livewire\Component;
use App\Models\Sucursal;

class Show extends Component
{
    public $sucursal;

    public function mount(Sucursal $sucursal)
    {
        $this->sucursal = $sucursal;
    }

    public function render()
    {
        return view('livewire.admin.sucursales.show')
            ->layout('components.layouts.admin', [
                'title' => 'Detalles de Sucursal'
            ]);
    }
}