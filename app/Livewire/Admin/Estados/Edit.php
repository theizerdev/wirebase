<?php

namespace App\Livewire\Admin\Estados;

use App\Models\Estado;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Edit extends Component
{
    use HasDynamicLayout;

    public $estado;
    public $nombre = '';
    public $codigo = '';
    public $pais_id = '';
    public $activo = true;

    protected $rules = [
        'nombre' => 'required|string|max:100',
        'codigo' => 'nullable|string|max:20',
        'pais_id' => 'required|exists:pais,id',
        'activo' => 'boolean'
    ];

    public function mount($id)
    {
        if (!Auth::user()->can('edit estados')) {
            abort(403, 'No tienes permiso para editar estados.');
        }

        $this->estado = Estado::findOrFail($id);
        $this->nombre = $this->estado->nombre;
        $this->codigo = $this->estado->codigo;
        $this->pais_id = $this->estado->pais_id;
        $this->activo = $this->estado->activo;
    }

    public function update()
    {
        $this->validate();

        $this->estado->update([
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'pais_id' => $this->pais_id,
            'activo' => $this->activo
        ]);

        session()->flash('message', 'Estado actualizado exitosamente.');
        return redirect()->route('admin.estados.index');
    }

    public function render()
    {
        return view('livewire.admin.estados.edit')->layout($this->getLayout());
    }
}
