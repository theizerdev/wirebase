<?php

namespace App\Livewire\Admin\Municipios;

use App\Models\Municipio;
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
        if (!Auth::user()->can('create municipios')) {
            abort(403, 'No tienes permiso para crear municipios.');
        }
    }

    public function save()
    {
        $this->validate();

        Municipio::create([
            'nombre' => $this->nombre,
            'estado_id' => $this->estado_id,
        ]);

        session()->flash('message', 'Municipio creado exitosamente.');
        return redirect()->route('admin.municipios.index');
    }

    public function render()
    {
        return view('livewire.admin.municipios.create')->layout($this->getLayout());
    }
}