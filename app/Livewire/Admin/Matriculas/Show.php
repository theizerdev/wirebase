<?php

namespace App\Livewire\Admin\Matriculas;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Matricula;

class Show extends Component
{
    use HasDynamicLayout;


    public $matricula;

    public function mount(Matricula $matricula)
    {
        $this->matricula = $matricula->load(['student', 'programa', 'periodo']);
    }

    public function render()
    {
        return view('livewire.admin.matriculas.show')->layout($this->getLayout());
    }
}



