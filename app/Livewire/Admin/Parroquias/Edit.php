<?php

namespace App\Livewire\Admin\Parroquias;

use App\Models\Parroquia;
use App\Models\Municipio;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDynamicLayout;

class Edit extends Component
{
    use HasDynamicLayout;

    public $parroquia;
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

    public function mount($id)
    {
        $this->parroquia = Parroquia::findOrFail($id);
        
        if (!Auth::user()->can('edit parroquias')) {
            abort(403, 'No tienes permiso para editar parroquias.');
        }

        $this->fill([
            'nombre' => $this->parroquia->nombre,
            'codigo' => $this->parroquia->codigo,
            'municipio_id' => $this->parroquia->municipio_id,
            'activo' => $this->parroquia->activo
        ]);
    }

    public function update()
    {
        $this->validate();

        $this->parroquia->update([
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'municipio_id' => $this->municipio_id,
            'activo' => $this->activo
        ]);

        session()->flash('message', 'Parroquia actualizada exitosamente.');
        return redirect()->route('admin.parroquias.index');
    }

    public function render()
    {
        $municipios = Municipio::with('estado')->where('activo', true)->get();
        return view('livewire.admin.parroquias.edit', compact('municipios'))->layout($this->getLayout());
    }
}