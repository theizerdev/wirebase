<?php

namespace App\Livewire\Admin\Empresas;

use Livewire\Component;
use App\Models\Empresa;

class Show extends Component
{
    public $empresa;

    public function mount(Empresa $empresa)
    {
        $this->empresa = $empresa;
    }

    public function render()
    {
        return view('livewire.admin.empresas.show')
            ->layout('components.layouts.admin', [
                'title' => 'Detalles de Empresa'
            ]);
    }
}