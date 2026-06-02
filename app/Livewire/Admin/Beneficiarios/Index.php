<?php

namespace App\Livewire\Admin\Beneficiarios;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Beneficiario;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BeneficiariosExport;

class Index extends Component
{
    use HasDynamicLayout, WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    
    // Campos para filtros avanzados
    public $estado_civil = '';
    public $nivel_instruccion = '';
    public $estudia_actualmente = '';
    public $trabaja_actualmente = '';
    public $posee_habilidad_productiva = '';
    public $pertenece_organizacion_social = '';
    public $edad_min = '';
    public $edad_max = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $asignaciones_economicas = '';
    public $estado_id = '';
    public $municipio_id = '';
    public $parroquia_id = '';


    protected $queryString = [
        'search', 
        'estado_civil', 
        'nivel_instruccion', 
        'estudia_actualmente', 
        'trabaja_actualmente', 
        'posee_habilidad_productiva', 
        'pertenece_organizacion_social', 
        'edad_min', 
        'edad_max', 
        'fecha_inicio', 
        'fecha_fin',
        'asignaciones_economicas',
        'sortBy',
        'sortDirection',
        'perPage'
    ];

    public function mount()
    {
        $this->estados = \App\Models\Estado::orderBy('nombre')->get();
        $this->responsables = \App\Models\Responsable::orderBy('nombre_completo')->get();
        
        // Cargar municipios y parroquias si hay estado seleccionado
        if ($this->estado_id) {
            $this->municipios = \App\Models\Municipio::where('estado_id', $this->estado_id)->orderBy('nombre')->get();
        }
        if ($this->municipio_id) {
            $this->parroquias = \App\Models\Parroquia::where('municipio_id', $this->municipio_id)->orderBy('nombre')->get();
        }
        
        // Verificar si hay filtros activos
        $this->verificarFiltrosActivos();
    }

    public function updatedEstadoId()
    {
        $this->municipio_id = '';
        $this->parroquia_id = '';
        $this->municipios = [];
        $this->parroquias = [];
        
        if ($this->estado_id) {
            $this->municipios = Municipio::where('estado_id', $this->estado_id)->orderBy('nombre')->get();
        }
    }

    public function updatedMunicipioId()
    {
        $this->parroquia_id = '';
        $this->parroquias = [];
        
        if ($this->municipio_id) {
            $this->parroquias = Parroquia::where('municipio_id', $this->municipio_id)->orderBy('nombre')->get();
        }
    }

    public function updated($property)
    {
        // Resetear página cuando cambian los filtros
        if (
            in_array($property, [
                'search', 'estado_id', 'municipio_id', 'parroquia_id',
                'estado_civil', 'nivel_instruccion', 'estudia_actualmente',
                'trabaja_actualmente', 'posee_habilidad_productiva',
                'pertenece_organizacion_social', 'edad_min', 'edad_max',
                'fecha_inicio', 'fecha_fin', 'asignaciones_economicas',
            ], true)
        ) {
            $this->resetPage();
            $this->verificarFiltrosActivos();
        }
    }

    private function verificarFiltrosActivos()
    {
        $this->filtrosActivos = !empty($this->search) ||
                                !empty($this->estado_id) ||
                                !empty($this->municipio_id) ||
                                !empty($this->parroquia_id) ||
                                !empty($this->estado_civil) ||
                                !empty($this->nivel_instruccion) ||
                                !empty($this->estudia_actualmente) ||
                                !empty($this->trabaja_actualmente) ||
                                !empty($this->posee_habilidad_productiva) ||
                                !empty($this->pertenece_organizacion_social) ||
                                !empty($this->edad_min) ||
                                !empty($this->edad_max) ||
                                !empty($this->fecha_inicio) ||
                                !empty($this->fecha_fin) ||
                                !empty($this->asignaciones_economicas);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->estado_id = '';
        $this->municipio_id = '';
        $this->parroquia_id = '';
        $this->estado_civil = '';
        $this->nivel_instruccion = '';
        $this->estudia_actualmente = '';
        $this->trabaja_actualmente = '';
        $this->posee_habilidad_productiva = '';
        $this->pertenece_organizacion_social = '';
        $this->edad_min = '';
        $this->edad_max = '';
        $this->fecha_inicio = '';
        $this->fecha_fin = '';
        $this->asignaciones_economicas = '';
        $this->municipios = [];
        $this->parroquias = [];
        $this->filtrosActivos = false;
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    private function buildBeneficiariosQuery()
    {
        return Beneficiario::with([
                'responsable:id,nombre_completo,estado_id,codigo_casa_alimentacion',
                'responsable.estado:id,nombre',
                'estado:id,nombre',
                'municipio:id,nombre',
                'parroquia:id,nombre',
            ])
            ->when(auth()->check() && auth()->user()->hasRole('Responsable') && auth()->user()->responsable_id, function ($query) {
                $query->where('responsable_id', auth()->user()->responsable_id);
            })
            ->when($this->search, function ($query) {
                $query->where('nombres', 'like', '%' . $this->search . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                      ->orWhere('cedula', 'like', '%' . $this->search . '%')
                      ->orWhereHas('responsable', function ($subQuery) {
                          $subQuery->where('nombre_completo', 'like', '%' . $this->search . '%');
                      });
            })
            ->when($this->estado_civil, function ($query) {
                $query->where('estado_civil', $this->estado_civil);
            })
            ->when($this->nivel_instruccion, function ($query) {
                $query->where('nivel_instruccion', $this->nivel_instruccion);
            })
            ->when($this->estudia_actualmente !== '', function ($query) {
                $query->where('estudia_actualmente', $this->estudia_actualmente);
            })
            ->when($this->trabaja_actualmente !== '', function ($query) {
                $query->where('trabaja_actualmente', $this->trabaja_actualmente);
            })
            ->when($this->posee_habilidad_productiva !== '', function ($query) {
                $query->where('posee_habilidad_productiva', $this->posee_habilidad_productiva);
            })
            ->when($this->pertenece_organizacion_social !== '', function ($query) {
                $query->where('pertenece_organizacion_social', $this->pertenece_organizacion_social);
            })
            ->when($this->edad_min, function ($query) {
                $query->where('edad', '>=', $this->edad_min);
            })
            ->when($this->edad_max, function ($query) {
                $query->where('edad', '<=', $this->edad_max);
            })
            ->when($this->fecha_inicio, function ($query) {
                $query->whereDate('fecha_nacimiento', '>=', $this->fecha_inicio);
            })
            ->when($this->fecha_fin, function ($query) {
                $query->whereDate('fecha_nacimiento', '<=', $this->fecha_fin);
            })
            ->when($this->asignaciones_economicas, function ($query) {
                $query->whereJsonContains('asignaciones_economicas', $this->asignaciones_economicas);
            });
    }

    public function render()
    {
        $query = $this->buildBeneficiariosQuery();

        $totalBeneficiarios = (clone $query)->count();
        $totalConResponsable = (clone $query)->whereNotNull('responsable_id')->count();
        $totalEstudian = (clone $query)->where('estudia_actualmente', true)->count();
        $totalTrabajan = (clone $query)->where('trabaja_actualmente', true)->count();

        $beneficiarios = $query
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return $this->renderWithLayout('livewire.admin.beneficiarios.index', [
            'beneficiarios' => $beneficiarios,
            'totalBeneficiarios' => $totalBeneficiarios,
            'totalConResponsable' => $totalConResponsable,
            'totalEstudian' => $totalEstudian,
            'totalTrabajan' => $totalTrabajan,
        ], [
            'title' => 'Beneficiarios',
            'description' => 'Listado de beneficiarios'
        ]);
    }

    public function delete($id)
    {
        $beneficiario = Beneficiario::findOrFail($id);
        $nombre = $beneficiario->nombres . ' ' . $beneficiario->apellidos;
        $beneficiario->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "El beneficiario {$nombre} ha sido eliminado exitosamente",
            'duration' => 5000
        ]);
    }


    public function export()
    {
        $beneficiarios = $this->buildBeneficiariosQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->get();

        return Excel::download(new BeneficiariosExport($beneficiarios), 'beneficiarios_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
}