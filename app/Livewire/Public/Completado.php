<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Pastor;

class Completado extends Component
{
    public Pastor $pastor;

    public function mount(Pastor $pastor)
    {
        $this->pastor = $pastor;
    }

    public function render()
    {
        return view('livewire.public.completado')
            ->layout('components.layouts.auth-basic', ['title' => 'Registro Exitoso']);
    }
}
