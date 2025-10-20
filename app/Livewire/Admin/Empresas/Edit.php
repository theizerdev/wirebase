<?php

namespace App\Livewire\Admin\Empresas;

use Livewire\Component;
use App\Models\Empresa;

class Edit extends Component
{
    public $empresa;
    public $razon_social = '';
    public $documento = '';
    public $direccion = '';
    public $latitud = '';
    public $longitud = '';
    public $representante_legal = '';
    public $status = true;

    protected $rules = [
        'razon_social' => 'required|string|max:255',
        'documento' => 'required|string',
        'direccion' => 'nullable|string',
        'latitud' => 'nullable|numeric|between:-90,90',
        'longitud' => 'nullable|numeric|between:-180,180',
        'representante_legal' => 'nullable|string|max:255',
        'status' => 'boolean',
    ];

    public function mount(Empresa $empresa)
    {
        $this->empresa = $empresa;
        $this->razon_social = $empresa->razon_social;
        $this->documento = $empresa->documento;
        $this->direccion = $empresa->direccion;
        $this->latitud = $empresa->latitud;
        $this->longitud = $empresa->longitud;
        $this->representante_legal = $empresa->representante_legal;
        $this->status = $empresa->status;

        // Actualizar la regla de validación para permitir el documento actual
        $this->rules['documento'] = 'required|string|unique:empresas,documento,' . $empresa->id;
    }

    public function save()
    {
        $this->validate();

        $this->empresa->update([
            'razon_social' => $this->razon_social,
            'documento' => $this->documento,
            'direccion' => $this->direccion,
            'latitud' => $this->latitud ?: null,
            'longitud' => $this->longitud ?: null,
            'representante_legal' => $this->representante_legal,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Empresa actualizada correctamente.');

        return redirect()->route('admin.empresas.index');
    }

    public function render()
    {
        return view('livewire.admin.empresas.edit')
            ->layout('components.layouts.admin', [
                'title' => 'Editar Empresa'
            ]);
    }
}
