<?php

namespace App\Livewire\Admin\Municipios;

use App\Models\Municipio;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Edit extends Component
{
    use HasDynamicLayout;

    public $municipio;
    public $nombre = '';
    public $estado_id = '';

    protected $rules = [
        'nombre' => 'required|string|max:100',
        'estado_id' => 'required|exists:estados,id',
    ];

    public function mount($id)
    {
        $this->municipio = Municipio::findOrFail($id);
        
        if (!Auth::user()->can('edit municipios')) {
            abort(403, 'No tienes permiso para editar municipios.');
        }

        $this->fill([
            'nombre' => $this->municipio->nombre,
            'estado_id' => $this->municipio->estado_id,
        ]);
    }

    public function save()
    {
        $this->validate();

        $this->municipio->update([
            'nombre' => $this->nombre,
            'estado_id' => $this->estado_id,
        ]);

        session()->flash('message', 'Municipio actualizado exitosamente.');
        return redirect()->route('admin.municipios.index');
    }

    public function render()
    {
        return view('livewire.admin.municipios.edit')->layout($this->getLayout());
    }
}