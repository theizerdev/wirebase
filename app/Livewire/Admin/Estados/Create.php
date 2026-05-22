<?php

namespace App\Livewire\Admin\Estados;

use App\Models\Estado;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Create extends Component
{
    use HasDynamicLayout;

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

    public function mount()
    {
        if (!Auth::user()->can('create estados')) {
            abort(403, 'No tienes permiso para crear estados.');
        }
    }

    public function save()
    {
        $this->validate();

        Estado::create([
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'pais_id' => $this->pais_id,
            'activo' => $this->activo
        ]);

        session()->flash('message', 'Estado creado exitosamente.');
        return redirect()->route('admin.estados.index');
    }

    public function render()
    {
        return view('livewire.admin.estados.create')->layout($this->getLayout());
    }
}
