<?php

namespace App\Livewire\Admin\Ciudades;

use App\Models\Ciudad;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Create extends Component
{
    use HasDynamicLayout;

    public $nombre = '';
    public $estado_id = '';

    protected $rules = [
        'nombre' => 'required|string|max:100',
        'estado_id' => 'required|exists:estados,id',
    ];

    public function mount()
    {
        if (!Auth::user()->can('create ciudades')) {
            abort(403, 'No tienes permiso para crear ciudades.');
        }
    }

    public function save()
    {
        $this->validate();

        Ciudad::create([
            'nombre' => $this->nombre,
            'estado_id' => $this->estado_id
        ]);

        session()->flash('message', 'Ciudad creada exitosamente.');
        return redirect()->route('admin.ciudades.index');
    }

    public function render()
    {
        return view('livewire.admin.ciudades.create')->layout($this->getLayout());
    }
}
