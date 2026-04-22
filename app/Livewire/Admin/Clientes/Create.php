<?php

namespace App\Livewire\Admin\Clientes;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Cliente;
use App\Models\Empresa;

class Create extends Component
{
    use HasDynamicLayout;

    public $empresa_id = '';
    public $nombre = '';
    public $apellido = '';
    public $documento = '';
    public $tipo_documento = 'CI';
    public $email = '';
    public $telefono = '';
    public $telefono_alternativo = '';
    public $direccion = '';
    public $ciudad = '';
    public $estado_region = '';
    public $ocupacion = '';
    public $empresa_trabajo = '';
    public $ingreso_mensual_estimado = '';
    public $activo = true;

    public $empresas;

    protected $rules = [
        'empresa_id' => 'required|exists:empresas,id',
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'documento' => 'required|string|max:50|unique:clientes,documento',
        'tipo_documento' => 'required|string|max:20',
        'email' => 'nullable|email|max:255',
        'telefono' => 'required|string|max:20',
        'telefono_alternativo' => 'nullable|string|max:20',
        'direccion' => 'nullable|string',
        'ciudad' => 'nullable|string|max:100',
        'estado_region' => 'nullable|string|max:100',
        'ocupacion' => 'nullable|string|max:255',
        'empresa_trabajo' => 'nullable|string|max:255',
        'ingreso_mensual_estimado' => 'nullable|numeric|min:0',
        'activo' => 'boolean',
    ];

    public function mount()
    {
        $this->empresas = Empresa::forUser()->where('status', true)->get();
    }

    public function save()
    {
        $this->validate();

        Cliente::createWithUser([
            'empresa_id' => $this->empresa_id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'documento' => $this->documento,
            'tipo_documento' => $this->tipo_documento,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'telefono_alternativo' => $this->telefono_alternativo,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'estado_region' => $this->estado_region,
            'ocupacion' => $this->ocupacion,
            'empresa_trabajo' => $this->empresa_trabajo,
            'ingreso_mensual_estimado' => $this->ingreso_mensual_estimado ?: null,
            'activo' => $this->activo,
        ]);

        session()->flash('message', 'Cliente registrado correctamente.');

        return redirect()->route('admin.clientes.index');
    }

    public function render()
    {
        return view('livewire.admin.clientes.create')->layout($this->getLayout());
    }
}
