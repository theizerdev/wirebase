<?php

namespace App\Livewire\Admin\Sucursales;

use App\Traits\HasDynamicLayout;

use Livewire\Component;
use App\Models\Sucursal;
use App\Models\Empresa;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;

class Edit extends Component
{

    use HasDynamicLayout;
    public $sucursal;
    public $empresa_id = '';
    public $nombre = '';
    public $telefono = '';
    public $direccion = '';
    public $latitud = '';
    public $longitud = '';
    public $status = true;
    public $estado_id = '';
    public $municipio_id = '';
    public $parroquia_id = '';

    public $empresas;
    public $estados;
    public $municipios = [];
    public $parroquias = [];

    protected $rules = [
        'empresa_id' => 'required|exists:empresas,id',
        'nombre' => 'required|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string',
        'latitud' => 'nullable|numeric|between:-90,90',
        'longitud' => 'nullable|numeric|between:-180,180',
        'estado_id' => 'nullable|exists:estados,id',
        'municipio_id' => 'nullable|exists:municipios,id',
        'parroquia_id' => 'nullable|exists:parroquias,id',
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
        $this->estado_id = $sucursal->estado_id;
        $this->municipio_id = $sucursal->municipio_id;
        $this->parroquia_id = $sucursal->parroquia_id;
        $this->status = $sucursal->status;

        $this->empresas = Empresa::where('status', true)->get();
        $this->estados = Estado::all();
        
        // Cargar municipios si hay un estado seleccionado
        if ($this->estado_id) {
            $this->municipios = Municipio::where('estado_id', $this->estado_id)->get();
        }
        
        // Cargar parroquias si hay un municipio seleccionado
        if ($this->municipio_id) {
            $this->parroquias = Parroquia::where('municipio_id', $this->municipio_id)->get();
        }
    }

    public function updatedEstadoId($value)
    {
        $this->municipio_id = '';
        $this->parroquia_id = '';
        
        if ($value) {
            $this->municipios = Municipio::where('estado_id', $value)->get();
        } else {
            $this->municipios = [];
        }
        
        $this->parroquias = [];
    }

    public function updatedMunicipioId($value)
    {
        $this->parroquia_id = '';
        
        if ($value) {
            $this->parroquias = Parroquia::where('municipio_id', $value)->get();
        } else {
            $this->parroquias = [];
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $this->sucursal->update([
                'empresa_id' => $this->empresa_id,
                'nombre' => $this->nombre,
                'telefono' => $this->telefono,
                'direccion' => $this->direccion,
                'latitud' => $this->latitud ?: null,
                'longitud' => $this->longitud ?: null,
                'estado_id' => $this->estado_id ?: null,
                'municipio_id' => $this->municipio_id ?: null,
                'parroquia_id' => $this->parroquia_id ?: null,
                'status' => $this->status,
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Sucursal '{$this->nombre}' actualizada exitosamente.",
                'duration' => 4000
            ]);

            return redirect()->route('admin.sucursales.index');
            
        } catch (\Exception $e) {
          
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al actualizar la sucursal: ' . $e->getMessage(),
                'duration' => 5000
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.sucursales.edit')->layout($this->getLayout());
    }
}