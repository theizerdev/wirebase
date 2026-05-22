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
    public $codigo = '';
    public $estado_id = '';
    public $activo = true;

    protected $rules = [
        'nombre' => 'required|string|max:100',
        'codigo' => 'nullable|string|max:20',
        'estado_id' => 'required|exists:estados,id',
        'activo' => 'boolean'
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
            'codigo' => $this->codigo,
            'estado_id' => $this->estado_id,
            'activo' => $this->activo
        ]);

        session()->flash('message', 'Municipio creado exitosamente.');
        return redirect()->route('admin.municipios.index');
    }

    public function render()
    {
        return view('livewire.admin.municipios.create')->layout($this->getLayout());
    }
}