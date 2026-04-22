<?php

namespace App\Livewire\Admin\Empleados;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Empleado;

class Edit extends Component
{
    use HasDynamicLayout;

    public Empleado $empleado;
    public $nombre;
    public $apellido;
    public $documento;
    public $puesto;
    public $salario_base;
    public $horas_extra_base;
    public $bono_fijo;
    public $comision_fija;
    public $metodo_pago;
    public $telefono;
    public $email;
    public $activo;

    public function mount(Empleado $empleado)
    {
        if (!auth()->user()->can('edit empleados')) {
            session()->flash('error', 'No tienes permiso para editar empleados.');
            return redirect()->route('admin.empleados.index');
        }
        $this->empleado = $empleado;
        $this->nombre = $empleado->nombre;
        $this->apellido = $empleado->apellido;
        $this->documento = $empleado->documento;
        $this->puesto = $empleado->puesto;
        $this->salario_base = $empleado->salario_base;
        $this->horas_extra_base = $empleado->horas_extra_base;
        $this->bono_fijo = $empleado->bono_fijo;
        $this->comision_fija = $empleado->comision_fija;
        $this->metodo_pago = $empleado->metodo_pago;
        $this->telefono = $empleado->telefono;
        $this->email = $empleado->email;
        $this->activo = $empleado->activo;
    }

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

    public function update()
    {
        $this->validate();
        $this->empleado->update([
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
        session()->flash('message', 'Empleado actualizado correctamente.');
        return redirect()->route('admin.empleados.index');
    }

    public function render()
    {
        return view('livewire.admin.empleados.edit')->layout($this->getLayout(), [
            'title' => 'Editar Empleado'
        ]);
    }
}
