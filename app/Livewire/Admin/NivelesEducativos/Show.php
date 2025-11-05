<?php

namespace App\Livewire\Admin\NivelesEducativos;

use App\Models\NivelEducativo;
use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Show extends Component
{
    use HasDynamicLayout;


    public NivelEducativo $nivel;

    public function mount(NivelEducativo $nivel)
    {
        Gate::authorize('view', $nivel);
        $this->nivel = $nivel;
    }

    public function render()
    {
        return view('livewire.admin.niveles-educativos.show')->layout($this->getLayout());
    }
}




