<?php

namespace App\Livewire\Admin\Motos\Unidades;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Moto;
use App\Models\MotoUnidad;
use App\Models\Empresa;
use App\Models\Sucursal;

class Create extends Component
{
    use HasDynamicLayout;

    public Moto $moto;

    // Campos del formulario
    public $vin = '';
    public $numero_motor = '';
    public $numero_chasis = '';
    public $placa = '';
    public $color_especifico = '';
    public $kilometraje = 0;
    public $costo_compra = '';
    public $precio_venta = '';
    public $condicion = 'nuevo';
    public $fecha_ingreso;
    public $notas = '';
    public $empresa_id = '';
    public $sucursal_id = '';

    public $empresas;
    public $sucursales = [];

    protected $rules = [
        'vin' => 'nullable|string|max:50',
        'numero_motor' => 'nullable|string|max:50',
        'numero_chasis' => 'nullable|string|max:50',
        'placa' => 'nullable|string|max:20',
        'color_especifico' => 'required|string|max:50',
        'kilometraje' => 'required|integer|min:0',
        'costo_compra' => 'required|numeric|min:0',
        'precio_venta' => 'required|numeric|min:0',
        'condicion' => 'required|in:nuevo,usado',
        'fecha_ingreso' => 'required|date',
        'notas' => 'nullable|string|max:500',
        'empresa_id' => 'required|exists:empresas,id',
        'sucursal_id' => 'required|exists:sucursales,id',
    ];

    public function mount(Moto $moto)
    {
        $this->moto = $moto;
        $this->empresas = Empresa::forUser()->where('status', true)->get();
        $this->fecha_ingreso = date('Y-m-d');

        // Prellenar con datos base del modelo
        $this->color_especifico = $moto->color_principal;
        $this->precio_venta = $moto->precio_venta_base;
        $this->costo_compra = $moto->costo_referencial;

        // Si el usuario tiene una empresa asignada, seleccionarla por defecto
        if (auth()->user()->empresa_id) {
            $this->empresa_id = auth()->user()->empresa_id;
            $this->updatedEmpresaId($this->empresa_id);
        }

        $this->vin = 'N/A';
        $this->numero_motor = 'N/A';
        $this->numero_chasis = 'N/A';
        $this->placa = 'N/A';

    }

    public function updatedEmpresaId($value)
    {
        $this->sucursales = Sucursal::where('empresa_id', $value)->get();
        $this->sucursal_id = '';
    }

    public function save()
    {
        $this->validate();

        // Normalizar campos de identificación: convertir vacíos o espacios en null
        $vin = $this->normalizeIdentificationField($this->vin);
        $numeroMotor = $this->normalizeIdentificationField($this->numero_motor);
        $numeroChasis = $this->normalizeIdentificationField($this->numero_chasis);
        $placa = $this->normalizeIdentificationField($this->placa);

        MotoUnidad::create([
            'moto_id' => $this->moto->id,
            'vin' => $vin,
            'numero_motor' => $numeroMotor,
            'numero_chasis' => $numeroChasis,
            'placa' => $placa,
            'color_especifico' => $this->color_especifico,
            'kilometraje' => $this->kilometraje,
            'costo_compra' => $this->costo_compra,
            'precio_venta' => $this->precio_venta,
            'estado' => 'disponible', // Por defecto disponible
            'condicion' => $this->condicion,
            'fecha_ingreso' => $this->fecha_ingreso,
            'notas' => $this->notas,
            'empresa_id' => $this->empresa_id,
            'sucursal_id' => $this->sucursal_id,
        ]);

        session()->flash('message', 'Unidad registrada correctamente en inventario.');

        return redirect()->route('admin.motos.unidades.index', $this->moto->id);
    }

    /**
     * Normaliza los campos de identificación
     * Convierte valores vacíos, "-", "N/A", "NA", "n/a" en null
     */
    private function normalizeIdentificationField($value)
    {
        if (empty($value)) {
            return null;
        }

        $trimmed = trim($value);

        // Convertir placeholders comunes a null
        if (in_array(strtoupper($trimmed), ['-', 'N/A', 'NA', 'S/N', 'SN', 'NONE', 'NINGUNO', ''])) {
            return null;
        }

        return $trimmed;
    }

    public function render()
    {
        return view('livewire.admin.motos.unidades.create')->layout($this->getLayout());
    }
}
