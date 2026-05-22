<?php

namespace App\Livewire\Admin\Parroquias;

use App\Models\Parroquia;
use App\Models\Municipio;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Create extends Component
{
    use HasDynamicLayout;

    public $nombre = '';
    public $codigo = '';
    public $municipio_id = '';
    public $activo = true;

    protected $rules = [
        'nombre' => 'required|string|max:100',
        'codigo' => 'nullable|string|max:20',
        'municipio_id' => 'required|exists:municipios,id',
        'activo' => 'boolean'
    ];

    public function mount()
    {
        if (!Auth::user()->can('create parroquias')) {
            abort(403, 'No tienes permiso para crear parroquias.');
        }
    }

    public function save()
    {
        $this->validate();

        Parroquia::create([
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'municipio_id' => $this->municipio_id,
            'activo' => $this->activo
        ]);

        session()->flash('message', 'Parroquia creada exitosamente.');
        return redirect()->route('admin.parroquias.index');
    }

    public function render()
    {
        $municipios = Municipio::with('estado')->where('activo', true)->get();
        return view('livewire.admin.parroquias.create', compact('municipios'))->layout($this->getLayout());
    }
}