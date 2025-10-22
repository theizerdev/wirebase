<?php

namespace App\Livewire\Admin\NivelesEducativos;

use App\Models\NivelEducativo;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Edit extends Component
{
    public NivelEducativo $nivel;
    public $nombre;
    public $costo;
    public $cuotas;

    protected $rules = [
        'nombre' => 'required|string|max:255|unique:niveles_educativos,nombre,',
        'costo' => 'required|numeric|min:0',
        'cuotas' => 'required|integer|min:1'
    ];

    public function mount(NivelEducativo $nivel)
    {
        Gate::authorize('update', $nivel);

        $this->nivel = $nivel;
        $this->nombre = $nivel->nombre;
        $this->costo = $nivel->costo;
        $this->cuotas = $nivel->cuotas;

        $this->rules['nombre'] .= $nivel->id;
    }

    public function save()
    {
        $this->validate();

        $this->nivel->update([
            'nombre' => $this->nombre,
            'costo' => $this->costo,
            'cuotas' => $this->cuotas
        ]);

        session()->flash('success', 'Nivel educativo actualizado exitosamente');
        return redirect()->route('admin.niveles-educativos.index');
    }

    public function render()
    {
        return view('livewire.admin.niveles-educativos.edit')
            ->layout('components.layouts.admin');
    }
}
