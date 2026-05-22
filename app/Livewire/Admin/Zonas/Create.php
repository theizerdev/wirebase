<?php

namespace App\Livewire\Admin\Zonas;

use App\Models\Zona;
use Livewire\Component;

class Create extends Component
{
    public $nombre = '';
    public $codigo = '';
    public $descripcion = '';
    public $distrito = '';
    public $sucursal_id = null;
    public $activo = true;
    
    protected $rules = [
        'nombre' => 'required|string|max:255',
        'codigo' => 'nullable|string|max:100|unique:zonas,codigo',
        'descripcion' => 'nullable|string|max:1000',
        'distrito' => 'nullable|string|max:100',
        'sucursal_id' => 'nullable|exists:sucursales,id',
        'activo' => 'boolean',
    ];
    
    protected $messages = [
        'nombre.required' => 'El nombre de la zona es obligatorio.',
        'codigo.unique' => 'El código ya está en uso.',
        'sucursal_id.exists' => 'La sucursal seleccionada no es válida.',
    ];
    
    /**
     * Guardar la nueva zona.
     */
    public function save()
    {
        $this->validate();
        
        Zona::create([
            'nombre' => $this->nombre,
            'codigo' => $this->codigo ?: null,
            'descripcion' => $this->descripcion,
            'distrito' => $this->distrito ?: null,
            'empresa_id' => auth()->user()->empresa_id,
            'sucursal_id' => $this->sucursal_id,
            'activo' => $this->activo,
        ]);
        
        session()->flash('message', 'Zona creada correctamente.');
        
        return redirect()->route('zonas.index');
    }
    
    /**
     * Renderizar el componente.
     */
    public function render()
    {
        return view('livewire.admin.zonas.create')->layout('layouts.app');
    }
}
