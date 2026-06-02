<?php

namespace App\Livewire\Admin\Beneficiarios;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Beneficiario;

class Show extends Component
{
    use HasDynamicLayout;

    public $beneficiario;

    public function mount(Beneficiario $beneficiario)
    {
        $this->beneficiario = $beneficiario->load('responsable');
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.beneficiarios.show', [], [
            'title' => 'Detalles del Beneficiario',
            'description' => 'Información detallada del beneficiario'
        ]);
    }
}