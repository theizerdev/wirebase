<?php

namespace App\Livewire\Admin\Zonas;

use App\Models\Zona;
use Livewire\Component;

class Edit extends Component
{
    public Zona $zona;
    public $nombre = '';
    public $codigo = '';
    public $descripcion = '';
    public $distrito = '';
    public $sucursal_id = null;
    public $activo = true;
    
    protected $rules = [
        'nombre' => 'required|string|max:255',
        'codigo' => 'nullable|string|max:100|unique:zonas,codigo,{{ $this->zona->id }}',
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
     * Montar el componente con los datos de la zona.
     */
    public function mount(Zona $zona)
    {
        $this->zona = $zona;
        $this->nombre = $zona->nombre;
        $this->codigo = $zona->codigo;
        $this->descripcion = $zona->descripcion;
        $this->distrito = $zona->distrito;
        $this->sucursal_id = $zona->sucursal_id;
        $this->activo = $zona->activo;
    }
    
    /**
     * Actualizar la zona.
     */
    public function update()
    {
        $this->validate();
        
        $this->zona->update([
            'nombre' => $this->nombre,
            'codigo' => $this->codigo ?: null,
            'descripcion' => $this->descripcion,
            'distrito' => $this->distrito ?: null,
            'sucursal_id' => $this->sucursal_id,
            'activo' => $this->activo,
        ]);
        
        session()->flash('message', 'Zona actualizada correctamente.');
        
        return redirect()->route('zonas.index');
    }
    
    /**
     * Renderizar el componente.
     */
    public function render()
    {
        return view('livewire.admin.zonas.edit')->layout('layouts.app');
    }
}
