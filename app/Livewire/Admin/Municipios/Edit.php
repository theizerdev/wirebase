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
    public $codigo = '';
    public $estado_id = '';
    public $activo = true;

    protected $rules = [
        'nombre' => 'required|string|max:100',
        'codigo' => 'nullable|string|max:20',
        'estado_id' => 'required|exists:estados,id',
        'activo' => 'boolean'
    ];

    public function mount($id)
    {
        $this->municipio = Municipio::findOrFail($id);
        
        if (!Auth::user()->can('edit municipios')) {
            abort(403, 'No tienes permiso para editar municipios.');
        }

        $this->fill([
            'nombre' => $this->municipio->nombre,
            'codigo' => $this->municipio->codigo,
            'estado_id' => $this->municipio->estado_id,
            'activo' => $this->municipio->activo
        ]);
    }

    public function update()
    {
        $this->validate();

        $this->municipio->update([
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'estado_id' => $this->estado_id,
            'activo' => $this->activo
        ]);

        session()->flash('message', 'Municipio actualizado exitosamente.');
        return redirect()->route('admin.municipios.index');
    }

    public function render()
    {
        return view('livewire.admin.municipios.edit')->layout($this->getLayout());
    }
}