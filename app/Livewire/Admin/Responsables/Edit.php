<?php

namespace App\Livewire\Admin\Responsables;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Responsable;
use App\Models\CasaAlimentacion;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\Empresa;
use App\Models\Sucursal;

class Edit extends Component
{
    use HasDynamicLayout;

    public $responsable;
    public $nombre_completo = '';
    public $cedula = '';
    public $estado_id = '';
    public $municipio_id = '';
    public $parroquia_id = '';
    public $telefono = '';
    public $direccion = '';
    public $punto_referencia = '';
    public $fecha_levantamiento = '';
    public $codigo_casa_alimentacion = '';
    public $empresa_id = '';
    public $sucursal_id = '';

    public $estados = [];
    public $municipios = [];
    public $parroquias = [];
    public $empresas = [];
    public $sucursales = [];
    public $casas = [];

    protected $rules = [
        'nombre_completo' => 'required|string|max:255',
        'cedula' => 'required|string|max:20',
        'estado_id' => 'nullable|exists:estados,id',
        'municipio_id' => 'nullable|exists:municipios,id',
        'parroquia_id' => 'nullable|exists:parroquias,id',
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:500',
        'punto_referencia' => 'nullable|string|max:500',
        'fecha_levantamiento' => 'nullable|date',
        'codigo_casa_alimentacion' => 'nullable|string|max:100',
        'empresa_id' => 'nullable|exists:empresas,id',
        'sucursal_id' => 'nullable|exists:sucursales,id',
    ];

     private function cargarCasas(): void
    {
        $query = CasaAlimentacion::query();

        if (!empty($this->estado_id)) {
            $query->where('estado_id', $this->estado_id);
        }

        if (!empty($this->municipio_id)) {
            $query->where('municipio_id', $this->municipio_id);
        }

        if (!empty($this->parroquia_id)) {
            $query->where('parroquia_id', $this->parroquia_id);
        }

        $this->casas = $query
            ->orderBy('codigo')
            ->get(['id', 'codigo']);
    }


    public function mount(Responsable $responsable)
    {
        $this->cargarCasas();

        $this->responsable = $responsable;
        $this->nombre_completo = $responsable->nombre_completo;
        $this->cedula = $responsable->cedula;
        $this->estado_id = $responsable->estado_id;
        $this->municipio_id = $responsable->municipio_id;
        $this->parroquia_id = $responsable->parroquia_id;
        $this->telefono = $responsable->telefono;
        $this->direccion = $responsable->direccion;
        $this->punto_referencia = $responsable->punto_referencia;
        $this->fecha_levantamiento = $responsable->fecha_levantamiento;
        $this->codigo_casa_alimentacion = $responsable->codigo_casa_alimentacion;
        $this->empresa_id = $responsable->empresa_id;
        $this->sucursal_id = $responsable->sucursal_id;
        $this->codigo_casa_alimentacion = $responsable->codigo_casa_alimentacion;



        $this->estados = Estado::all();

        // Cargar dependencias según la ubicación seleccionada
        if ($this->estado_id) {
            $this->municipios = Municipio::where('estado_id', $this->estado_id)->get();
        }

        if ($this->municipio_id) {
            $this->parroquias = Parroquia::where('municipio_id', $this->municipio_id)->get();
        }

        if ($this->parroquia_id) {
            $this->empresas = Empresa::whereHas('sucursales', function($query) {
                $query->where('parroquia_id', $this->parroquia_id);
            })->get();
        }

        if ($this->empresa_id && $this->parroquia_id) {
            $this->sucursales = Sucursal::where('empresa_id', $this->empresa_id)
                ->where('parroquia_id', $this->parroquia_id)
                ->get();
        }
    }

    public function updatedEstadoId($value)
    {
        $this->municipio_id = '';
        $this->parroquia_id = '';
        $this->empresa_id = '';
        $this->sucursal_id = '';

        if ($value) {
            $this->municipios = Municipio::where('estado_id', $value)->get();
        } else {
            $this->municipios = [];
        }

        $this->parroquias = [];
        $this->empresas = [];
        $this->sucursales = [];
    }

    public function updatedMunicipioId($value)
    {
        $this->parroquia_id = '';
        $this->empresa_id = '';
        $this->sucursal_id = '';

        if ($value) {
            $this->parroquias = Parroquia::where('municipio_id', $value)->get();
        } else {
            $this->parroquias = [];
        }

        $this->empresas = [];
        $this->sucursales = [];
    }

    public function updatedParroquiaId($value)
    {
        $this->empresa_id = '';
        $this->sucursal_id = '';

        if ($value) {
            // Cargar empresas que tienen sucursales en la parroquia seleccionada
            $this->empresas = Empresa::whereHas('sucursales', function($query) use ($value) {
                $query->where('parroquia_id', $value);
            })->get();
        } else {
            $this->empresas = [];
        }

        $this->sucursales = [];
    }

    public function updatedEmpresaId($value)
    {
        $this->sucursal_id = '';

        if ($value && $this->parroquia_id) {
            // Filtrar sucursales según la parroquia y la empresa seleccionada
            $this->sucursales = Sucursal::where('empresa_id', $value)
                ->where('parroquia_id', $this->parroquia_id)
                ->get();
        } else {
            $this->sucursales = [];
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $casaAlimentacionId = null;

            if (!empty($this->codigo_casa_alimentacion)) {
                $casa = CasaAlimentacion::where('codigo', $this->codigo_casa_alimentacion)->first();
                $casaAlimentacionId = $casa?->id;
            }

            $this->responsable->update([
                'nombre_completo' => $this->nombre_completo,
                'cedula' => $this->cedula,
                'estado_id' => $this->estado_id ?: null,
                'municipio_id' => $this->municipio_id ?: null,
                'parroquia_id' => $this->parroquia_id ?: null,
                'telefono' => $this->telefono,
                'direccion' => $this->direccion,
                'punto_referencia' => $this->punto_referencia,
                'fecha_levantamiento' => $this->fecha_levantamiento,
                'codigo_casa_alimentacion' => $this->codigo_casa_alimentacion,
                'casa_alimentacion_id' => $casaAlimentacionId,
                'empresa_id' => auth()->user()->empresa_id ?: null,
                'sucursal_id' => auth()->user()->sucursal_id ?: null,
            ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Responsable '{$this->nombre_completo}' actualizado exitosamente.",
            'duration' => 4000
        ]);

        return redirect()->route('admin.responsables.index');
        } catch (\Throwable $th) {
            //throw $th;

        $this->dispatch('notify', [
            'type' => 'error',
            'message' => "Error al actualizar el responsable '{$this->nombre_completo}'.",
            'duration' => 4000
        ]);
        }
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.responsables.edit', [
            'estados' => $this->estados,
            'municipios' => $this->municipios,
            'parroquias' => $this->parroquias,
            'empresas' => $this->empresas,
            'sucursales' => $this->sucursales,
            'casas' => $this->casas,
        ], [
            'title' => 'Editar Responsable',
            'description' => 'Modificar responsable'
        ]);
    }
}
