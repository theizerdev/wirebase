<?php

namespace App\Livewire\Admin\Empresas;

use Livewire\Component;
use App\Models\Empresa;

class Create extends Component
{
    public $razon_social = '';
    public $documento = '';
    public $direccion = '';
    public $latitud = '';
    public $longitud = '';
    public $representante_legal = '';
    public $status = true;

    protected $rules = [
        'razon_social' => 'required|string|max:255',
        'documento' => 'required|string|unique:empresas,documento',
        'direccion' => 'nullable|string',
        'latitud' => 'nullable|numeric|between:-90,90',
        'longitud' => 'nullable|numeric|between:-180,180',
        'representante_legal' => 'nullable|string|max:255',
        'status' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        Empresa::create([
            'razon_social' => $this->razon_social,
            'documento' => $this->documento,
            'direccion' => $this->direccion,
            'latitud' => $this->latitud ?: null,
            'longitud' => $this->longitud ?: null,
            'representante_legal' => $this->representante_legal,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Empresa creada correctamente.');

        return redirect()->route('admin.empresas.index');
    }

    public function render()
    {
        return view('livewire.admin.empresas.create')
            ->layout('components.layouts.admin', [
                'title' => 'Crear Empresa'
            ]);
    }
}