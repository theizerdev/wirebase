<?php

namespace App\Livewire\Admin\Empresas;

use App\Traits\HasDynamicLayout;
use App\Livewire\Traits\HasRegionalConfiguration;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Empresa;
use App\Models\Pais;
use App\Services\RegionalConfigurationService;

class Create extends Component
{
    use HasDynamicLayout, HasRegionalConfiguration;

    public $razon_social = '';
    public $documento = '';
    public $direccion = '';
    public $latitud = -12.0464;
    public $longitud = -77.0428;
    public $address = '';
    public $representante_legal = '';
    public $status = true;
    public $telefono = '';
    public $email = '';
    public $pais_id = '';

    protected $rules = [
        'razon_social' => 'required|string|max:255',
        'documento' => 'required|string|max:50',
        'direccion' => 'nullable|string|max:500',
        'latitud' => 'required|numeric',
        'longitud' => 'required|numeric',
        'representante_legal' => 'nullable|string|max:255',
        'status' => 'boolean',
        'telefono' => 'nullable|string|max:50',
        'email' => 'nullable|email|max:255',
        'pais_id' => 'required|exists:pais,id',
    ];

    #[On('location-updated')]
    public function updateLocation($latitude, $longitude, $address)
    {
        $this->latitud = $latitude;
        $this->longitud = $longitude;
        $this->address = $address;
        $this->direccion = $address;
    }

    public function save()
    {
        $this->validate();

        $empresa = Empresa::create([
            'razon_social' => $this->razon_social,
            'documento' => $this->documento,
            'direccion' => $this->address ?: $this->direccion,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            'representante_legal' => $this->representante_legal,
            'status' => $this->status,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'pais_id' => $this->pais_id,
        ]);

        // Aplicar configuración regional para la nueva empresa
        $this->applyRegionalConfigurationToEmpresa($empresa);

        session()->flash('message', 'Empresa creada correctamente. Configuración regional aplicada.');

        return redirect()->route('admin.empresas.index');
    }

    public function render()
    {
        $paises = \App\Models\Pais::where('activo', true)->orderBy('nombre')->get();

        return $this->renderWithLayout('livewire.admin.empresas.create', [
            'paises' => $paises
        ], [
            'title' => 'Crear Empresa',
            'description' => 'Nueva empresa del sistema'
        ]);
    }
}
