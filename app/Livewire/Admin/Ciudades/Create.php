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
        if (!Auth::user()->can('create ciudades')) {
            abort(403, 'No tienes permiso para crear ciudades.');
        }
    }

    public function save()
    {
        $this->validate();

        Ciudad::create([
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'estado_id' => $this->estado_id,
            'activo' => $this->activo
        ]);

        session()->flash('message', 'Ciudad creada exitosamente.');
        return redirect()->route('admin.ciudades.index');
    }

    public function render()
    {
        return view('livewire.admin.ciudades.create')->layout($this->getLayout());
    }
}
