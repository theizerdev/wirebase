<?php

namespace App\Livewire\Admin\Turnos;

use App\Models\Turno;
use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Show extends Component
{
    use HasDynamicLayout;


    public Turno $turno;

    public function mount(Turno $turno)
    {
        Gate::authorize('show turnos', Turno::class);
        $this->turno = $turno;
    }

    public function render()
    {
        return view('livewire.admin.turnos.show')->layout($this->getLayout());
    }
}




