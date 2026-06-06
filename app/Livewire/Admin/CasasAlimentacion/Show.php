<?php

namespace App\Livewire\Admin\CasasAlimentacion;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\CasaAlimentacion;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    use HasDynamicLayout;

    public CasaAlimentacion $casa;

    public function mount(CasaAlimentacion $casa)
    {
        if (!Auth::user()->can('access casas_alimentacion')) {
            abort(403, 'No tienes permiso para ver detalles de casas de alimentación.');
        }

        $this->casa = $casa->load(['estado', 'municipio', 'parroquia']);
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.casas_alimentacion.show', [], [
            'title' => 'Detalles de Casa de Alimentación',
            'description' => 'Información detallada de la casa de alimentación',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.casas_alimentacion.index' => 'Casas de Alimentación',
                'admin.casas_alimentacion.show' => 'Detalles'
            ]
        ]);
    }
}
