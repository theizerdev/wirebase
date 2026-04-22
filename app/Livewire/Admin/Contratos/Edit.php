<?php

namespace App\Livewire\Admin\Contratos;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Contrato;

class Edit extends Component
{
    use HasDynamicLayout;

    public $contrato;
    public $observaciones;

    public function mount(Contrato $contrato)
    {
        $this->contrato = $contrato;
        $this->observaciones = $contrato->observaciones;
    }

    public function save()
    {
        $this->validate([
            'observaciones' => 'nullable|string|max:1000'
        ]);

        $this->contrato->update([
            'observaciones' => $this->observaciones
        ]);

        session()->flash('message', 'Contrato actualizado correctamente.');
        return redirect()->route('admin.contratos.show', $this->contrato->id);
    }

    public function render()
    {
        return view('livewire.admin.contratos.edit')->layout($this->getLayout());
    }
}
