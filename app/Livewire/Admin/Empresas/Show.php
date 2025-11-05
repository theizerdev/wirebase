<?php

namespace App\Livewire\Admin\Empresas;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Empresa;

class Show extends Component
{
    use HasDynamicLayout;


    public $empresa;

    public function mount(Empresa $empresa)
    {
        $this->empresa = $empresa;
    }

    public function render()
    {
        return view('livewire.admin.empresas.show')->layout($this->getLayout());
    }
}



