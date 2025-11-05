<?php

namespace App\Livewire\Admin\Programas;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Programa;

class Show extends Component
{
    use HasDynamicLayout;


    public $programa;

    public function mount(Programa $programa)
    {
        // Verificar permiso para ver programas
        if (!auth()->user()->can('view programas')) {
            session()->flash('error', 'No tienes permiso para ver programas.');
            return redirect()->route('admin.programas.index');
        }

        $this->programa = $programa;
    }

    public function render()
    {
        return view('livewire.admin.programas.show')->layout($this->getLayout());
    }
}



