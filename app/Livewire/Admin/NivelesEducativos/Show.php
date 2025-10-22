<?php

namespace App\Livewire\Admin\NivelesEducativos;

use App\Models\NivelEducativo;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Show extends Component
{
    public NivelEducativo $nivel;

    public function mount(NivelEducativo $nivel)
    {
        Gate::authorize('view', $nivel);
        $this->nivel = $nivel;
    }

    public function render()
    {
        return view('livewire.admin.niveles-educativos.show', [
            'nivel' => $this->nivel
        ])->layout('components.layouts.admin');
    }
}
