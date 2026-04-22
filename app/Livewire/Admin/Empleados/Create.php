<?php

namespace App\Livewire\Admin\Empleados;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Empleado;

class Create extends Component
{
    use HasDynamicLayout;

    public $nombre = '';
    public $apellido = '';
    public $documento = '';
    public $puesto = '';
    public $salario_base = 0;
    public $horas_extra_base = 0;
    public $bono_fijo = 0;
    public $comision_fija = 0;
    public $metodo_pago = 'efectivo';
    public $telefono = '';
    public $email = '';
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'documento' => 'nullable|string|max:50',
            'puesto' => 'nullable|string|max:100',
            'salario_base' => 'required|numeric|min:0',
            'horas_extra_base' => 'nullable|numeric|min:0',
            'bono_fijo' => 'nullable|numeric|min:0',
            'comision_fija' => 'nullable|numeric|min:0',
            'metodo_pago' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'activo' => 'boolean'
        ];
    }

    public function store()
    {
        if (!auth()->user()->can('create empleados')) {
            session()->flash('error', 'No tienes permiso para crear empleados.');
            return;
        }
        $this->validate();
        Empleado::create([
            'empresa_id' => auth()->user()->empresa_id,
            'sucursal_id' => auth()->user()->sucursal_id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'documento' => $this->documento,
            'puesto' => $this->puesto,
            'salario_base' => $this->salario_base,
            'horas_extra_base' => $this->horas_extra_base,
            'bono_fijo' => $this->bono_fijo,
            'comision_fija' => $this->comision_fija,
            'metodo_pago' => $this->metodo_pago,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'activo' => $this->activo
        ]);
        session()->flash('message', 'Empleado creado correctamente.');
        return redirect()->route('admin.empleados.index');
    }

    public function render()
    {
        return view('livewire.admin.empleados.create')->layout($this->getLayout(), [
            'title' => 'Crear Empleado'
        ]);
    }
}
