<?php

namespace App\Livewire\Admin\Motos;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Moto;
use App\Models\MotoUnidad;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    use HasDynamicLayout;

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

    // Campos para registrar primera unidad
    public $registrar_unidad = false;
    public $sucursal_id = '';
    public $vin = '';
    public $numero_motor = '';
    public $numero_chasis = '';
    public $placa = '';
    public $kilometraje = 0;
    public $condicion = 'nuevo';
    public $fecha_ingreso;

    public $empresas;
    public $sucursales = [];

    protected function rules() 
    {
        $rules = [
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

        if ($this->registrar_unidad) {
            $rules['sucursal_id'] = 'required|exists:sucursales,id';
            $rules['vin'] = 'required|string|max:50|unique:moto_unidades,vin';
            $rules['numero_motor'] = 'required|string|max:50';
            $rules['numero_chasis'] = 'required|string|max:50';
            $rules['condicion'] = 'required|in:nuevo,usado';
            $rules['fecha_ingreso'] = 'required|date';
            $rules['kilometraje'] = 'required|integer|min:0';
        }

        return $rules;
    }

    public function mount()
    {
        $this->empresas = Empresa::forUser()->where('status', true)->get();
        $this->fecha_ingreso = date('Y-m-d');
        $this->anio = date('Y');
    }

    public function updatedEmpresaId($value)
    {
        if ($value) {
            $this->sucursales = Sucursal::where('empresa_id', $value)->get();
        } else {
            $this->sucursales = [];
        }
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $moto = Moto::create([
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

            if ($this->registrar_unidad) {
                MotoUnidad::create([
                    'moto_id' => $moto->id,
                    'vin' => $this->vin,
                    'numero_motor' => $this->numero_motor,
                    'numero_chasis' => $this->numero_chasis,
                    'placa' => $this->placa ?: null,
                    'color_especifico' => $this->color_principal, // Hereda color
                    'kilometraje' => $this->kilometraje,
                    'costo_compra' => $this->costo_referencial ?: 0,
                    'precio_venta' => $this->precio_venta_base,
                    'estado' => 'disponible',
                    'condicion' => $this->condicion,
                    'fecha_ingreso' => $this->fecha_ingreso,
                    'empresa_id' => $this->empresa_id,
                    'sucursal_id' => $this->sucursal_id,
                ]);
            }
        });

        session()->flash('message', 'Modelo de motocicleta creado correctamente.');

        return redirect()->route('admin.motos.index');
    }

    public function render()
    {
        return view('livewire.admin.motos.create')->layout($this->getLayout());
    }
}
