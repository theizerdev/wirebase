<?php

namespace App\Livewire\Admin\Sucursales;

use Livewire\Component;
use App\Models\Sucursal;
use App\Models\Empresa;

class Edit extends Component
{
    public $sucursal;
    public $empresa_id = '';
    public $nombre = '';
    public $telefono = '';
    public $direccion = '';
    public $latitud = '';
    public $longitud = '';
    public $status = true;
    
    public $empresas;

    protected $rules = [
        'empresa_id' => 'required|exists:empresas,id',
        'nombre' => 'required|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string',
        'latitud' => 'nullable|numeric|between:-90,90',
        'longitud' => 'nullable|numeric|between:-180,180',
        'status' => 'boolean',
    ];

    public function mount(Sucursal $sucursal)
    {
        $this->sucursal = $sucursal;
        $this->empresa_id = $sucursal->empresa_id;
        $this->nombre = $sucursal->nombre;
        $this->telefono = $sucursal->telefono;
        $this->direccion = $sucursal->direccion;
        $this->latitud = $sucursal->latitud;
        $this->longitud = $sucursal->longitud;
        $this->status = $sucursal->status;
        
        $this->empresas = Empresa::where('status', true)->get();
    }

    public function save()
    {
        $this->validate();

        $this->sucursal->update([
            'empresa_id' => $this->empresa_id,
            'nombre' => $this->nombre,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'latitud' => $this->latitud ?: null,
            'longitud' => $this->longitud ?: null,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Sucursal actualizada correctamente.');

        return redirect()->route('admin.sucursales.index');
    }

    public function render()
    {
        return view('livewire.admin.sucursales.edit')
            ->layout('components.layouts.admin', [
                'title' => 'Editar Sucursal'
            ]);
    }
}