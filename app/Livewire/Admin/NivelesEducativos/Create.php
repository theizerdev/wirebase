<?php

namespace App\Livewire\Admin\NivelesEducativos;

use App\Models\NivelEducativo;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Create extends Component
{
    public $nombre;
    public $costo;
    public $cuotas;

    protected $rules = [
        'nombre' => 'required|string|max:255|unique:niveles_educativos',
        'costo' => 'required|numeric|min:0',
        'cuotas' => 'required|integer|min:1'
    ];

    public function mount()
    {
        Gate::authorize('create', NivelEducativo::class);
    }

    public function save()
    {
        $this->validate();

        NivelEducativo::create([
            'nombre' => $this->nombre,
            'costo' => $this->costo,
            'cuotas' => $this->cuotas
        ]);

        session()->flash('success', 'Nivel educativo creado exitosamente');
        return redirect()->route('admin.niveles-educativos.index');
    }

    public function render()
    {
        return view('livewire.admin.niveles-educativos.create')
            ->layout('components.layouts.admin');
    }
}
