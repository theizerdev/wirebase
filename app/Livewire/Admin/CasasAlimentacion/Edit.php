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

class Edit extends Component
{
    use HasDynamicLayout;

    // Wizard step tracking
    public $currentStep = 1;
    public $totalSteps = 5;

    public $casa;
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
    public $zona_base_misiones;
    public $distancia_a_base_misiones;
    public $consejo_comunal;
    public $vocero_alimentacion;
    public $telefono_principal;
    public $telefono_secundario;
    public $estado_cda;
    public $motivo_inoperatividad;

    // Campos de operatividad
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

    // Step 3: Infraestructura del área de la Cocina
    public $condicion_fachada;
    public $condicion_area_cocina;
    public $condicion_despensa;
    public $condicion_cableado_electrico;
    public $condicion_aguas_servidas;
    public $condicion_agua_potable;
    public $condicion_techo;
    public $condicion_piso;
    public $condicion_paredes;
    public $observaciones_infraestructura_cocina;

    // Step 4: Factibilidad de espacio / Proyecto socio productivo
    public $espacio_casa_alimentacion;
    public $ha_recibido_formacion_proyectos;
    public $posee_proyecto_socio_productivo;
    public $interesado_produccion_primaria;
    public $cuenta_infraestructura_adecuada_proyecto;
    public $metros_cuadrados_proyecto;
    public $observaciones_factibilidad_proyecto;

    // Step 5: Ficha técnica
    public $encuestador_nombre;
    public $encuestador_telefono;
    public $tecnico_nombre;
    public $tecnico_telefono;
    public $transcriptor_nombre;
    public $transcriptor_telefono;
    public $observaciones_adicionales_ficha_tecnica;

    public $estados = [];
    public $municipios = [];
    public $parroquias = [];
    public $address = '';

    protected function getStep1Rules()
    {
        return [
            'codigo' => 'required|string|max:100|unique:casas_alimentacion,codigo,'.$this->casa->id,
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

    protected function getStep3Rules()
    {
        return [
            'condicion_fachada' => 'nullable|in:Pintada,Frisada,Sin Frisar',
            'condicion_area_cocina' => 'nullable|in:Buena,Regular,Mala',
            'condicion_despensa' => 'nullable|in:Buena,Regular,Mala',
            'condicion_cableado_electrico' => 'nullable|in:Buena,Regular,Mala',
            'condicion_aguas_servidas' => 'nullable|in:Buena,Regular,Mala',
            'condicion_agua_potable' => 'nullable|in:Buena,Regular,Mala',
            'condicion_techo' => 'nullable|in:Buena,Regular,Mala',
            'condicion_piso' => 'nullable|in:Buena,Regular,Mala',
            'condicion_paredes' => 'nullable|in:Buena,Regular,Mala',
            'observaciones_infraestructura_cocina' => 'nullable|string',
        ];
    }

    protected function getStep4Rules()
    {
        return [
            'espacio_casa_alimentacion' => 'nullable|in:Propio,De uso comunal',
            'ha_recibido_formacion_proyectos' => 'nullable|in:si,no',
            'posee_proyecto_socio_productivo' => 'nullable|in:si,no',
            'interesado_produccion_primaria' => 'nullable|in:Si,No,si,no',
            'cuenta_infraestructura_adecuada_proyecto' => 'nullable|in:SI,NO,si,no',
            'metros_cuadrados_proyecto' => 'nullable|numeric|min:0',
            'observaciones_factibilidad_proyecto' => 'nullable|string',
        ];
    }

    protected function getStep5Rules()
    {
        return [
            'encuestador_nombre' => 'nullable|string|max:191',
            'encuestador_telefono' => 'nullable|string|max:50',
            'tecnico_nombre' => 'nullable|string|max:191',
            'tecnico_telefono' => 'nullable|string|max:50',
            'transcriptor_nombre' => 'nullable|string|max:191',
            'transcriptor_telefono' => 'nullable|string|max:50',
            'observaciones_adicionales_ficha_tecnica' => 'nullable|string',
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
        // Para EDIT: validar SOLO el paso actual al avanzar
        if ($this->currentStep === 1) {
            $this->validate($this->getStep1Rules());
        } elseif ($this->currentStep === 2) {
            $this->validate($this->getStep2Rules());
        } elseif ($this->currentStep === 3) {
            $this->validate($this->getStep3Rules());
        } elseif ($this->currentStep === 4) {
            $this->validate($this->getStep4Rules());
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

    public function mount(CasaAlimentacion $casa)
    {
        if (!Auth::user()->can('edit casas_alimentacion')) {
            abort(403, 'No tienes permiso para editar casas de alimentación.');
        }

        $this->casa = $casa;
        $this->fillFromModel($casa);

        $this->estados = Estado::orderBy('nombre')->get();

        if ($casa->estado_id) {
            $this->municipios = Municipio::where('estado_id', $casa->estado_id)->orderBy('nombre')->get();
        }

        if ($casa->municipio_id) {
            $this->parroquias = Parroquia::where('municipio_id', $casa->municipio_id)->orderBy('nombre')->get();
        }
    }

    private function fillFromModel(CasaAlimentacion $casa)
    {
        $this->fecha = \Carbon\Carbon::parse($casa->fecha)->format('Y-m-d');
        $this->codigo = $casa->codigo;
        $this->estado_id = $casa->estado_id;
        $this->municipio_id = $casa->municipio_id;
        $this->parroquia_id = $casa->parroquia_id;
        $this->sector = $casa->sector;
        $this->calle_avenida = $casa->calle_avenida;
        $this->numero_vivienda = $casa->numero_vivienda;
        $this->punto_referencia = $casa->punto_referencia;
        $this->longitud = $casa->longitud;
        $this->latitud = $casa->latitud;
        $this->zona_base_misiones = $casa->zona_base_misiones;
        $this->distancia_a_base_misiones = $casa->distancia_a_base_misiones;
        $this->consejo_comunal = $casa->consejo_comunal;
        $this->vocero_alimentacion = $casa->vocero_alimentacion;
        $this->telefono_principal = $casa->telefono_principal;
        $this->telefono_secundario = $casa->telefono_secundario;
        $this->estado_cda = $casa->estado_cda;
        $this->motivo_inoperatividad = $casa->motivo_inoperatividad;

        // Campos de operatividad
        $this->posee_sello = $casa->posee_sello;
        $this->posee_listado_beneficiarios = $casa->posee_listado_beneficiarios;
        $this->num_beneficiarios_registrados = $casa->num_beneficiarios_registrados;
        $this->num_promedio_diario_beneficiarios = $casa->num_promedio_diario_beneficiarios;
        $this->posee_identificacion_fundaproal = $casa->posee_identificacion_fundaproal;
        $this->identificacion_visible = $casa->identificacion_visible;
        $this->posee_cartelera_informativa = $casa->posee_cartelera_informativa;
        $this->posee_certificado_manipulacion_alimentos = $casa->posee_certificado_manipulacion_alimentos;
        $this->posee_certificado_salud = $casa->posee_certificado_salud;
        $this->posee_gorros_delantales = $casa->posee_gorros_delantales;
        $this->recibe_suministro_gas = $casa->recibe_suministro_gas;
        $this->cantidad_reguladores_kg = $casa->cantidad_reguladores_kg;
        $this->bombonas_propias_cantidad = $casa->bombonas_propias_cantidad;
        $this->bombonas_prestadas_cantidad = $casa->bombonas_prestadas_cantidad;
        $this->manipulacion_alimentos_adecuada = $casa->manipulacion_alimentos_adecuada;
        $this->dias_preparacion_semana = $casa->dias_preparacion_semana;
        $this->itinerantes_femeninos = $casa->itinerantes_femeninos;
        $this->itinerantes_masculinos = $casa->itinerantes_masculinos;
        $this->itinerantes_menores_masculinos = $casa->itinerantes_menores_masculinos;
        $this->itinerantes_menores_femeninos = $casa->itinerantes_menores_femeninos;
        $this->condicion_cocina = $casa->condicion_cocina;
        $this->tipo_cocina = $casa->tipo_cocina;
        $this->dominio_cocina = $casa->dominio_cocina;
        $this->condicion_nevera = $casa->condicion_nevera;
        $this->dominio_congelador = $casa->dominio_congelador;
        $this->condicion_congelador = $casa->condicion_congelador;
        $this->estatus_utensilios = $casa->estatus_utensilios;
        $this->posee_meson = $casa->posee_meson;
        $this->posee_fregadero = $casa->posee_fregadero;
        $this->posee_tanque_agua = $casa->posee_tanque_agua;
        $this->posee_estante_almacenamiento = $casa->posee_estante_almacenamiento;
        $this->observaciones_operatividad = $casa->observaciones_operatividad;

        // Step 3: Infraestructura del área de la Cocina
        $this->condicion_fachada = $casa->condicion_fachada;
        $this->condicion_area_cocina = $casa->condicion_area_cocina;
        $this->condicion_despensa = $casa->condicion_despensa;
        $this->condicion_cableado_electrico = $casa->condicion_cableado_electrico;
        $this->condicion_aguas_servidas = $casa->condicion_aguas_servidas;
        $this->condicion_agua_potable = $casa->condicion_agua_potable;
        $this->condicion_techo = $casa->condicion_techo;
        $this->condicion_piso = $casa->condicion_piso;
        $this->condicion_paredes = $casa->condicion_paredes;
        $this->observaciones_infraestructura_cocina = $casa->observaciones_infraestructura_cocina;

        // Step 4: Factibilidad de espacio / Proyecto socio productivo
        $this->espacio_casa_alimentacion = $casa->espacio_casa_alimentacion;
        $this->ha_recibido_formacion_proyectos = $casa->ha_recibido_formacion_proyectos;
        $this->posee_proyecto_socio_productivo = $casa->posee_proyecto_socio_productivo;
        $this->interesado_produccion_primaria = $casa->interesado_produccion_primaria;
        $this->cuenta_infraestructura_adecuada_proyecto = $casa->cuenta_infraestructura_adecuada_proyecto;
        $this->metros_cuadrados_proyecto = $casa->metros_cuadrados_proyecto;
        $this->observaciones_factibilidad_proyecto = $casa->observaciones_factibilidad_proyecto;

        // Step 5: Ficha técnica
        $this->encuestador_nombre = $casa->encuestador_nombre;
        $this->encuestador_telefono = $casa->encuestador_telefono;
        $this->tecnico_nombre = $casa->tecnico_nombre;
        $this->tecnico_telefono = $casa->tecnico_telefono;
        $this->transcriptor_nombre = $casa->transcriptor_nombre;
        $this->transcriptor_telefono = $casa->transcriptor_telefono;
        $this->observaciones_adicionales_ficha_tecnica = $casa->observaciones_adicionales_ficha_tecnica;

        if ($casa->punto_referencia) {
            $this->address = $casa->punto_referencia;
        }
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
        $this->validate($this->getStep1Rules());
        $this->validate($this->getStep2Rules());
        $this->validate($this->getStep3Rules());
        $this->validate($this->getStep4Rules());
        $this->validate($this->getStep5Rules());

        $this->casa->update([
            // Información General
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
            // Operatividad
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

            // Step 3: Infraestructura del área de la Cocina
            'condicion_fachada' => $this->condicion_fachada,
            'condicion_area_cocina' => $this->condicion_area_cocina,
            'condicion_despensa' => $this->condicion_despensa,
            'condicion_cableado_electrico' => $this->condicion_cableado_electrico,
            'condicion_aguas_servidas' => $this->condicion_aguas_servidas,
            'condicion_agua_potable' => $this->condicion_agua_potable,
            'condicion_techo' => $this->condicion_techo,
            'condicion_piso' => $this->condicion_piso,
            'condicion_paredes' => $this->condicion_paredes,
            'observaciones_infraestructura_cocina' => $this->observaciones_infraestructura_cocina,

            // Step 4: Factibilidad de espacio / Proyecto socio productivo
            'espacio_casa_alimentacion' => $this->espacio_casa_alimentacion,
            'ha_recibido_formacion_proyectos' => $this->ha_recibido_formacion_proyectos,
            'posee_proyecto_socio_productivo' => $this->posee_proyecto_socio_productivo,
            'interesado_produccion_primaria' => $this->interesado_produccion_primaria,
            'cuenta_infraestructura_adecuada_proyecto' => $this->cuenta_infraestructura_adecuada_proyecto,
            'metros_cuadrados_proyecto' => $this->metros_cuadrados_proyecto,
            'observaciones_factibilidad_proyecto' => $this->observaciones_factibilidad_proyecto,

            // Step 5: Ficha técnica
            'encuestador_nombre' => $this->encuestador_nombre,
            'encuestador_telefono' => $this->encuestador_telefono,
            'tecnico_nombre' => $this->tecnico_nombre,
            'tecnico_telefono' => $this->tecnico_telefono,
            'transcriptor_nombre' => $this->transcriptor_nombre,
            'transcriptor_telefono' => $this->transcriptor_telefono,
            'observaciones_adicionales_ficha_tecnica' => $this->observaciones_adicionales_ficha_tecnica,
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Casa de Alimentación '{$this->codigo}' actualizada correctamente.",
            'duration' => 4000
        ]);

        return redirect()->route('admin.casas_alimentacion.index');
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.casas_alimentacion.edit', [], [
            'title' => 'Editar Casa de Alimentación',
            'description' => 'Modificar casa de alimentación del sistema'
        ]);
    }
}
