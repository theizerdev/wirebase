<?php

namespace App\Livewire\Admin\Responsables;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Responsable;

class Show extends Component
{
    use HasDynamicLayout;

    public $responsable;

    public function mount(Responsable $responsable)
    {
        $this->responsable = $responsable->load(['estado', 'municipio', 'parroquia', 'empresa', 'sucursal']);
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.responsables.show', [
            'responsable' => $this->responsable
        ], [
            'title' => 'Detalle del Responsable',
            'description' => 'Información detallada del responsable'
        ]);
    }
}