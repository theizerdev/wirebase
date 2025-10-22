<?php

namespace App\Livewire\Admin\Turnos;

use App\Models\Turno;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Show extends Component
{
    public Turno $turno;

    public function mount(Turno $turno)
    {
        Gate::authorize('show turnos', Turno::class);
        $this->turno = $turno;
    }

    public function render()
    {
        return view('livewire.admin.turnos.show')
            ->layout('components.layouts.admin');
    }
}
