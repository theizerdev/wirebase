<?php

namespace App\Livewire\Admin\Ciudades;

use App\Models\Ciudad;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Edit extends Component
{
    use HasDynamicLayout;

    public $ciudad;
    public $nombre = '';

    public $estado_id = '';


    protected $rules = [
        'nombre' => 'required|string|max:100',
        'estado_id' => 'required|exists:estados,id',

    ];

    public function mount($id)
    {
        if (!Auth::user()->can('edit ciudades')) {
            abort(403, 'No tienes permiso para editar ciudades.');
        }

        $this->ciudad = Ciudad::findOrFail($id);
        $this->nombre = $this->ciudad->nombre;
        $this->estado_id = $this->ciudad->estado_id;

    }

    public function update()
    {
        $this->validate();

        $this->ciudad->update([
            'nombre' => $this->nombre,

            'estado_id' => $this->estado_id,

        ]);

        session()->flash('message', 'Ciudad actualizada exitosamente.');
        return redirect()->route('admin.ciudades.index');
    }

    public function render()
    {
        return view('livewire.admin.ciudades.edit')->layout($this->getLayout());
    }
}
