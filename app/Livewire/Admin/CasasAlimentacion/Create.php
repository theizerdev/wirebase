<?php

namespace App\Livewire\Admin\CasasAlimentacion;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\CasaAlimentacion;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    use HasDynamicLayout;

    // Wizard step tracking
    public $currentStep = 1;
    public $totalSteps = 2;

    // Step 1: Información General
    public $fecha;
    public $codigo;
    public $estado_id;
    public $municipio_id;
    public $parroquia_id;
    public $sector;
    public $calle_avenida;
    public $numero_vivienda;
    public $punto_referencia;
    public $longitud;
    public $latitud;
    public $zona_base_misiones = false;
    public $distancia_a_base_misiones;
    public $consejo_comunal;
    public $vocero_alimentacion;
    public $telefono_principal;
    public $telefono_secundario;
    public $estado_cda = 'Operativa';
    public $motivo_inoperatividad;

    // Step 2: Operatividad
    public $posee_sello = false;
    public $posee_listado_beneficiarios = false;
    public $num_beneficiarios_registrados;
    public $num_promedio_diario_beneficiarios;
    public $posee_identificacion_fundaproal = false;
    public $identificacion_visible = false;
    public $posee_cartelera_informativa = false;
    public $posee_certificado_manipulacion_alimentos = false;
    public $posee_certificado_salud = false;
    public $posee_gorros_delantales = false;
    public $recibe_suministro_gas = false;
    public $cantidad_reguladores_kg;
    public $bombonas_propias_cantidad = 0;
    public $bombonas_prestadas_cantidad = 0;
    public $manipulacion_alimentos_adecuada = false;
    public $dias_preparacion_semana;
    public $itinerantes_femeninos = 0;
    public $itinerantes_masculinos = 0;
    public $itinerantes_menores_masculinos = 0;
    public $itinerantes_menores_femeninos = 0;
    public $condicion_cocina;
    public $tipo_cocina;
    public $dominio_cocina;
    public $condicion_nevera;
    public $dominio_congelador;
    public $condicion_congelador;
    public $estatus_utensilios;
    public $posee_meson = false;
    public $posee_fregadero = false;
    public $posee_tanque_agua = false;
    public $posee_estante_almacenamiento = false;
    public $observaciones_operatividad;

    public $estados = [];
    public $municipios = [];
    public $parroquias = [];
    public $address = '';

    protected function getStep1Rules()
    {
        return [
            'codigo' => 'required|string|max:100|unique:casas_alimentacion,codigo',
            'fecha' => 'nullable|date',
            'estado_id' => 'nullable|exists:estados,id',
            'municipio_id' => 'nullable|exists:municipios,id',
            'parroquia_id' => 'nullable|exists:parroquias,id',
            'sector' => 'nullable|string|max:191',
            'calle_avenida' => 'nullable|string|max:191',
            'numero_vivienda' => 'nullable|string|max:50',
            'punto_referencia' => 'nullable|string|max:191',
            'longitud' => 'nullable|numeric|between:-90,90',
            'latitud' => 'nullable|numeric|between:-180,180',
            'zona_base_misiones' => 'boolean',
            'distancia_a_base_misiones' => 'nullable|numeric',
            'consejo_comunal' => 'nullable|string|max:191',
            'vocero_alimentacion' => 'nullable|string|max:191',
            'telefono_principal' => 'nullable|string|max:50',
            'telefono_secundario' => 'nullable|string|max:50',
            'estado_cda' => 'required|in:Operativa,Inoperativa,Inactiva',
            'motivo_inoperatividad' => 'nullable|string',
        ];
    }

    protected function getStep2Rules()
    {
        return [
            'posee_sello' => 'boolean',
            'posee_listado_beneficiarios' => 'boolean',
            'num_beneficiarios_registrados' => 'nullable|integer|min:0',
            'num_promedio_diario_beneficiarios' => 'nullable|integer|min:0',
            'posee_identificacion_fundaproal' => 'boolean',
            'identificacion_visible' => 'nullable|boolean',
            'posee_cartelera_informativa' => 'boolean',
            'posee_certificado_manipulacion_alimentos' => 'boolean',
            'posee_certificado_salud' => 'boolean',
            'posee_gorros_delantales' => 'boolean',
            'recibe_suministro_gas' => 'boolean',
            'cantidad_reguladores_kg' => 'nullable|integer|min:0',
            'bombonas_propias_cantidad' => 'nullable|integer|min:0',
            'bombonas_prestadas_cantidad' => 'nullable|integer|min:0',
            'manipulacion_alimentos_adecuada' => 'boolean',
            'dias_preparacion_semana' => 'nullable|integer|min:0|max:7',
            'itinerantes_femeninos' => 'nullable|integer|min:0',
            'itinerantes_masculinos' => 'nullable|integer|min:0',
            'itinerantes_menores_masculinos' => 'nullable|integer|min:0',
            'itinerantes_menores_femeninos' => 'nullable|integer|min:0',
            'condicion_cocina' => 'nullable|in:Operativa,Inoperativa,No posee',
            'tipo_cocina' => 'nullable|in:Industrial,Domestica,Fogon/Reverbero',
            'dominio_cocina' => 'nullable|in:Propia,Fundaproal,Prestada',
            'condicion_nevera' => 'nullable|in:Operativa,Inoperativa,No posee',
            'dominio_congelador' => 'nullable|in:Propia,Fundaproal,Prestada',
            'condicion_congelador' => 'nullable|in:Operativa,Inoperativa,No posee',
            'estatus_utensilios' => 'nullable|in:Buenos,Regular,Malos',
            'posee_meson' => 'boolean',
            'posee_fregadero' => 'boolean',
            'posee_tanque_agua' => 'boolean',
            'posee_estante_almacenamiento' => 'boolean',
            'observaciones_operatividad' => 'nullable|string',
        ];
    }

    #[On('location-updated')]
    public function updateLocation($latitude, $longitude, $address)
    {
        $this->latitud = $latitude;
        $this->longitud = $longitude;
        $this->address = $address;
        $this->punto_referencia = $address;
    }

    public function nextStep()
    {
        // Validate current step before proceeding
        if ($this->currentStep === 1) {
            $this->validate($this->getStep1Rules());
        } elseif ($this->currentStep === 2) {
            $this->validate($this->getStep2Rules());
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep($step)
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            // Only allow going to previous steps or validate current step
            if ($step < $this->currentStep) {
                $this->currentStep = $step;
            } elseif ($step === $this->currentStep + 1) {
                $this->nextStep();
            }
        }
    }

    public function mount()
    {
        if (!Auth::user()->can('create casas_alimentacion')) {
            abort(403, 'No tienes permiso para crear casas de alimentación.');
        }

        $this->estados = Estado::orderBy('nombre')->get();
        $this->latitud = -12.0464; // Coordenadas por defecto
        $this->longitud = -77.0428;
    }

    public function updatedEstadoId($value)
    {
        $this->municipio_id = '';
        $this->parroquia_id = '';
        $this->municipios = $value ? Municipio::where('estado_id', $value)->orderBy('nombre')->get() : [];
        $this->parroquias = [];
    }

    public function updatedMunicipioId($value)
    {
        $this->parroquia_id = '';
        $this->parroquias = $value ? Parroquia::where('municipio_id', $value)->orderBy('nombre')->get() : [];
    }

    public function save()
    {
        // Validate all steps before saving
        $this->validate($this->getStep1Rules());
        $this->validate($this->getStep2Rules());

        CasaAlimentacion::create([
            // Step 1: Información General
            'fecha' => $this->fecha ?: null,
            'codigo' => $this->codigo,
            'estado_id' => $this->estado_id ?: null,
            'municipio_id' => $this->municipio_id ?: null,
            'parroquia_id' => $this->parroquia_id ?: null,
            'sector' => $this->sector,
            'calle_avenida' => $this->calle_avenida,
            'numero_vivienda' => $this->numero_vivienda,
            'punto_referencia' => $this->address ?: $this->punto_referencia,
            'longitud' => $this->longitud,
            'latitud' => $this->latitud,
            'zona_base_misiones' => $this->zona_base_misiones,
            'distancia_a_base_misiones' => $this->distancia_a_base_misiones,
            'consejo_comunal' => $this->consejo_comunal,
            'vocero_alimentacion' => $this->vocero_alimentacion,
            'telefono_principal' => $this->telefono_principal,
            'telefono_secundario' => $this->telefono_secundario,
            'estado_cda' => $this->estado_cda,
            'motivo_inoperatividad' => $this->motivo_inoperatividad,
            // Step 2: Operatividad
            'posee_sello' => $this->posee_sello,
            'posee_listado_beneficiarios' => $this->posee_listado_beneficiarios,
            'num_beneficiarios_registrados' => $this->num_beneficiarios_registrados,
            'num_promedio_diario_beneficiarios' => $this->num_promedio_diario_beneficiarios,
            'posee_identificacion_fundaproal' => $this->posee_identificacion_fundaproal,
            'identificacion_visible' => $this->identificacion_visible,
            'posee_cartelera_informativa' => $this->posee_cartelera_informativa,
            'posee_certificado_manipulacion_alimentos' => $this->posee_certificado_manipulacion_alimentos,
            'posee_certificado_salud' => $this->posee_certificado_salud,
            'posee_gorros_delantales' => $this->posee_gorros_delantales,
            'recibe_suministro_gas' => $this->recibe_suministro_gas,
            'cantidad_reguladores_kg' => $this->cantidad_reguladores_kg,
            'bombonas_propias_cantidad' => $this->bombonas_propias_cantidad,
            'bombonas_prestadas_cantidad' => $this->bombonas_prestadas_cantidad,
            'manipulacion_alimentos_adecuada' => $this->manipulacion_alimentos_adecuada,
            'dias_preparacion_semana' => $this->dias_preparacion_semana,
            'itinerantes_femeninos' => $this->itinerantes_femeninos,
            'itinerantes_masculinos' => $this->itinerantes_masculinos,
            'itinerantes_menores_masculinos' => $this->itinerantes_menores_masculinos,
            'itinerantes_menores_femeninos' => $this->itinerantes_menores_femeninos,
            'condicion_cocina' => $this->condicion_cocina,
            'tipo_cocina' => $this->tipo_cocina,
            'dominio_cocina' => $this->dominio_cocina,
            'condicion_nevera' => $this->condicion_nevera,
            'dominio_congelador' => $this->dominio_congelador,
            'condicion_congelador' => $this->condicion_congelador,
            'estatus_utensilios' => $this->estatus_utensilios,
            'posee_meson' => $this->posee_meson,
            'posee_fregadero' => $this->posee_fregadero,
            'posee_tanque_agua' => $this->posee_tanque_agua,
            'posee_estante_almacenamiento' => $this->posee_estante_almacenamiento,
            'observaciones_operatividad' => $this->observaciones_operatividad,
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Casa de Alimentación '{$this->codigo}' creada correctamente.",
            'duration' => 4000
        ]);

        return redirect()->route('admin.casas_alimentacion.index');
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.casas_alimentacion.create', [], [
            'title' => 'Crear Casa de Alimentación',
            'description' => 'Nueva casa de alimentación del sistema'
        ]);
    }
}
