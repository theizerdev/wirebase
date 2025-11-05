<?php

namespace App\Livewire\Admin\NivelesEducativos;

use App\Models\NivelEducativo;
use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Edit extends Component
{
    use HasDynamicLayout;


    public NivelEducativo $nivel;
    public $nombre = '';
    public $descripcion = '';
    public $status = true;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'status' => 'boolean'
    ];

    public function mount(NivelEducativo $nivel)
    {
        $this->nivel = $nivel;
        $this->nombre = $nivel->nombre;
        $this->descripcion = $nivel->descripcion;
        $this->status = (bool)$nivel->status;

        $this->rules['nombre'] = 'required|string|max:255|unique:niveles_educativos,nombre,' . $nivel->id;
    }

    public function save()
    {
        $this->validate();

        $this->nivel->update([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'status' => $this->status
        ]);

        session()->flash('message', 'Nivel Educativo actualizado exitosamente.');
        return redirect()->route('admin.niveles-educativos.index');
    }

    public function render()
    {
        return view('livewire.admin.niveles-educativos.edit')->layout($this->getLayout());
    }
}



