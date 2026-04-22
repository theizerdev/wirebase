<?php

namespace App\Livewire\Admin\Motos;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Moto;
use App\Models\Empresa;

class Edit extends Component
{
    use HasDynamicLayout;

    public $moto;
    public $empresa_id = '';
    public $marca = '';
    public $modelo = '';
    public $anio = '';
    public $color_principal = '';
    public $cilindrada = '';
    public $tipo = '';
    public $descripcion = '';
    public $precio_venta_base = '';
    public $costo_referencial = '';
    public $activo = true;

    public $empresas;

    protected $rules = [
        'empresa_id' => 'required|exists:empresas,id',
        'marca' => 'required|string|max:255',
        'modelo' => 'required|string|max:255',
        'anio' => 'required|integer|min:1900|max:2100',
        'precio_venta_base' => 'required|numeric|min:0',
        'costo_referencial' => 'nullable|numeric|min:0',
        'color_principal' => 'nullable|string|max:100',
        'cilindrada' => 'nullable|string|max:50',
        'tipo' => 'nullable|string|max:50',
        'descripcion' => 'nullable|string',
        'activo' => 'boolean',
    ];

    public function mount(Moto $moto)
    {
        $this->moto = $moto;
        $this->empresa_id = $moto->empresa_id;
        $this->marca = $moto->marca;
        $this->modelo = $moto->modelo;
        $this->anio = $moto->anio;
        $this->color_principal = $moto->color_principal;
        $this->cilindrada = $moto->cilindrada;
        $this->tipo = $moto->tipo;
        $this->descripcion = $moto->descripcion;
        $this->precio_venta_base = $moto->precio_venta_base;
        $this->costo_referencial = $moto->costo_referencial;
        $this->activo = $moto->activo;

        $this->empresas = Empresa::forUser()->where('status', true)->get();
    }

    public function save()
    {
        $this->validate();

        $this->moto->update([
            'empresa_id' => $this->empresa_id,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'anio' => $this->anio,
            'precio_venta_base' => $this->precio_venta_base,
            'costo_referencial' => $this->costo_referencial ?: null,
            'color_principal' => $this->color_principal,
            'cilindrada' => $this->cilindrada,
            'tipo' => $this->tipo,
            'descripcion' => $this->descripcion,
            'activo' => $this->activo,
        ]);

        session()->flash('message', 'Modelo de motocicleta actualizado correctamente.');

        return redirect()->route('admin.motos.index');
    }

    public function render()
    {
        return view('livewire.admin.motos.edit')->layout($this->getLayout());
    }
}
