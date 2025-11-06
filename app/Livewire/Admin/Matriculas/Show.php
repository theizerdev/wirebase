<?php

namespace App\Livewire\Admin\Matriculas;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use App\Models\Matricula;

class Show extends Component
{
    use HasDynamicLayout;
    use HasRegionalFormatting;


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
