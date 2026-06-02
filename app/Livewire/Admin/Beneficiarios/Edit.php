<?php

namespace App\Livewire\Admin\Beneficiarios;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Beneficiario;
use App\Models\Responsable;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;

class Edit extends Component
{
    use HasDynamicLayout;

    public $beneficiario;
    
    // Wizard steps
    public $currentStep = 1;
    
    // Personal data
    public $nombres = '';
    public $apellidos = '';
    public $cedula = '';
    public $fecha_nacimiento = '';
    public $edad = null;
    public $telefono_principal = '';
    public $telefono_alternativo = '';
    public $estado_civil = 'Soltero(a)';
    public $estudia_actualmente = false;
    public $estudio_actual = '';
    public $nivel_instruccion = '';
    public $ultimo_titulo_obtenido = '';
    public $trabaja_actualmente = false;
    public $lugar_trabajo = '';
    public $ocupacion = '';
    public $ingreso_mensual = '';
    public $responsable_id = '';

    // Location fields
    public $estado_id = '';
    public $municipio_id = '';
    public $parroquia_id = '';

    // New fields for step 3
    public $posee_habilidad_productiva = false;
    public $habilidad_productiva = '';
    public $pertenece_organizacion_social = false;
    public $tipo_organizacion_social = '';
    public $otra_organizacion_social = '';
    public $asignaciones_economicas = []; // This will store selected items
    public $available_asignaciones = [
        'Hogares de la patria',
        'Amor Mayor',
        'José Gregorio Hernández',
        'Parto Humanizado',
        'Pensión por IVSS',
        'Otros'
    ]; // List of available options

    // Step 4: Datos de Salud
    public $tiene_evaluacion_antropometrica = false;
    public $evaluacion_realizada_por = '';
    public $condicion_ingreso = '';
    public $fecha_ingreso = '';
    public $padece_discapacidad_enfermedad = false;
    public $diagnostico = '';
    public $recipe_ayuda_tecnica = '';

    // Step 5: Datos Socio-Familiares
    public $personas_nucleo_familiar = 0;
    public $ninos_niñas = 0;
    public $adolescentes = 0;
    public $mujeres = 0;
    public $hombres = 0;
    public $adultos_mayores = 0;
    public $mujeres_embarazadas = 0;
    public $recipe_socio_familiar = '';
    public $es_mujer_embarazada = 0;
    public $fecha_ultima_menstruacion = '';
    public $edad_gestacion = '';
    public $observaciones = '';

    public $searchAsignacion = ''; // For searching/filtering options

    public $responsables = [];
    public $estados = [];
    public $municipios = [];
    public $parroquias = [];
    
    protected $rules = [
        // Step 1: Basic personal info
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'cedula' => 'required|string|max:20|unique:beneficiarios,cedula,{beneficiario},id',
        'fecha_nacimiento' => 'nullable|date',
        'telefono_principal' => 'nullable|string|max:20',
        'telefono_alternativo' => 'nullable|string|max:20',
        'estado_civil' => 'required|in:Soltero(a),Casado(a),Viudo(a),Divorciado(a)',
        
        // Location fields
        'estado_id' => 'nullable|exists:estados,id',
        'municipio_id' => 'nullable|exists:municipios,id',
        'parroquia_id' => 'nullable|exists:parroquias,id',
        
        // Step 2: Education and study info
        'estudia_actualmente' => 'nullable|boolean',
        'estudio_actual' => 'nullable|string|max:255',
        'nivel_instruccion' => 'required|in:Analfabeto,Básica,Media Diversificada,TSU,Universitario,Maestría,Doctorado',
        'ultimo_titulo_obtenido' => 'nullable|string|max:255',
        
        // Step 3: Work info
        'trabaja_actualmente' => 'nullable|boolean',
        'lugar_trabajo' => 'nullable|string|max:255',
        'ocupacion' => 'nullable|string|max:255',
        'ingreso_mensual' => 'nullable|numeric|min:0',
        
        // Step 3: New fields
        'posee_habilidad_productiva' => 'nullable|boolean',
        'habilidad_productiva' => 'nullable|string|max:255',
        'pertenece_organizacion_social' => 'nullable|boolean',
        'tipo_organizacion_social' => 'nullable|string|max:255',
        'otra_organizacion_social' => 'nullable|string|max:255',
        'asignaciones_economicas' => 'nullable|array',
        
        // Step 4: Datos de Salud
        'tiene_evaluacion_antropometrica' => 'nullable|boolean',
        'evaluacion_realizada_por' => 'nullable|string|max:255',
        'condicion_ingreso' => 'nullable|in:Persona en situación de calle,Mujer embarazada,Desnutrición,Adulto mayor sin recursos,Incapacitado para trabajar,Persona discapacitada,Estudiante sin programa de estudio,Desempleado',
        'fecha_ingreso' => 'nullable|date',
        'padece_discapacidad_enfermedad' => 'nullable|boolean',
        'diagnostico' => 'nullable|string|max:1000',
        'recipe_ayuda_tecnica' => 'nullable|string|max:1000',
        
        // Step 5: Datos Socio-Familiares
        'personas_nucleo_familiar' => 'nullable|integer|min:0',
        'ninos_niñas' => 'nullable|integer|min:0',
        'adolescentes' => 'nullable|integer|min:0',
        'mujeres' => 'nullable|integer|min:0',
        'hombres' => 'nullable|integer|min:0',
        'adultos_mayores' => 'nullable|integer|min:0',
        'mujeres_embarazadas' => 'nullable|integer|min:0',
        'recipe_socio_familiar' => 'nullable|string|max:1000',
        'es_mujer_embarazada' => 'boolean',
        'fecha_ultima_menstruacion' => 'nullable|date',
        'edad_gestacion' => 'nullable|integer|min:0|max:42',
        'observaciones' => 'nullable|string|max:2000',
        
        // Relationship
        'responsable_id' => 'nullable|exists:responsables,id',
    ];

    public function mount(Beneficiario $beneficiario)
    {
        $this->beneficiario = $beneficiario;
        $this->fillFromModel();
        $this->responsables = Responsable::all();
        $this->estados = Estado::all();
        
        // Cargar municipios y parroquias según los valores actuales del beneficiario
        if ($this->estado_id) {
            $this->municipios = Municipio::where('estado_id', $this->estado_id)->get();
        }
        if ($this->municipio_id) {
            $this->parroquias = Parroquia::where('municipio_id', $this->municipio_id)->get();
        }
    }

    private function fillFromModel()
    {
        $this->nombres = $this->beneficiario->nombres;
        $this->apellidos = $this->beneficiario->apellidos;
        $this->cedula = $this->beneficiario->cedula;
        $this->fecha_nacimiento = $this->beneficiario->fecha_nacimiento ? $this->beneficiario->fecha_nacimiento->format('Y-m-d') : '';
        $this->edad = $this->beneficiario->edad;
        $this->telefono_principal = $this->beneficiario->telefono_principal;
        $this->telefono_alternativo = $this->beneficiario->telefono_alternativo;
        $this->estado_civil = $this->beneficiario->estado_civil;
        $this->estudia_actualmente = $this->beneficiario->estudia_actualmente;
        $this->estudio_actual = $this->beneficiario->estudio_actual;
        $this->nivel_instruccion = $this->beneficiario->nivel_instruccion;
        $this->ultimo_titulo_obtenido = $this->beneficiario->ultimo_titulo_obtenido;
        $this->trabaja_actualmente = $this->beneficiario->trabaja_actualmente;
        $this->lugar_trabajo = $this->beneficiario->lugar_trabajo;
        $this->ocupacion = $this->beneficiario->ocupacion;
        $this->ingreso_mensual = $this->beneficiario->ingreso_mensual;
        $this->responsable_id = $this->beneficiario->responsable_id;
        
        // Location fields
        $this->estado_id = $this->beneficiario->estado_id;
        $this->municipio_id = $this->beneficiario->municipio_id;
        $this->parroquia_id = $this->beneficiario->parroquia_id;
        
        // New fields
        $this->posee_habilidad_productiva = $this->beneficiario->posee_habilidad_productiva;
        $this->habilidad_productiva = $this->beneficiario->habilidad_productiva;
        $this->pertenece_organizacion_social = $this->beneficiario->pertenece_organizacion_social;
        $this->tipo_organizacion_social = $this->beneficiario->tipo_organizacion_social;
        $this->otra_organizacion_social = $this->beneficiario->otra_organizacion_social;
        $this->asignaciones_economicas = $this->beneficiario->asignaciones_economicas ?: [];
        // Step 4: Datos de Salud
        $this->tiene_evaluacion_antropometrica = $this->beneficiario->tiene_evaluacion_antropometrica;
        $this->evaluacion_realizada_por = $this->beneficiario->evaluacion_realizada_por;
        $this->condicion_ingreso = $this->beneficiario->condicion_ingreso;
        $this->fecha_ingreso = $this->beneficiario->fecha_ingreso ? $this->beneficiario->fecha_ingreso->format('Y-m-d') : '';
        $this->padece_discapacidad_enfermedad = $this->beneficiario->padece_discapacidad_enfermedad;
        $this->diagnostico = $this->beneficiario->diagnostico;
        $this->recipe_ayuda_tecnica = $this->beneficiario->recipe_ayuda_tecnica;
        // Step 5: Datos Socio-Familiares
        $this->personas_nucleo_familiar = $this->beneficiario->personas_nucleo_familiar;
        $this->ninos_niñas = $this->beneficiario->ninos_niñas;
        $this->adolescentes = $this->beneficiario->adolescentes;
        $this->mujeres = $this->beneficiario->mujeres;
        $this->hombres = $this->beneficiario->hombres;
        $this->adultos_mayores = $this->beneficiario->adultos_mayores;
        $this->mujeres_embarazadas = $this->beneficiario->mujeres_embarazadas;
        $this->recipe_socio_familiar = $this->beneficiario->recipe_socio_familiar;
        $this->es_mujer_embarazada = $this->beneficiario->es_mujer_embarazada;
        $this->fecha_ultima_menstruacion = $this->beneficiario->fecha_ultima_menstruacion ? $this->beneficiario->fecha_ultima_menstruacion->format('Y-m-d') : '';
        $this->edad_gestacion = $this->beneficiario->edad_gestacion;
        $this->observaciones = $this->beneficiario->observaciones;
    }

    public function updatedFechaNacimiento()
    {
        if ($this->fecha_nacimiento) {
            $this->edad = \Carbon\Carbon::parse($this->fecha_nacimiento)->age;
        } else {
            $this->edad = null;
        }
    }

    public function updatedResponsableId()
    {
        if ($this->responsable_id) {
            $responsable = Responsable::find($this->responsable_id);
            if ($responsable) {
                // Copiar los datos de ubicación del responsable
                $this->estado_id = $responsable->estado_id;
                $this->municipio_id = $responsable->municipio_id;
                $this->parroquia_id = $responsable->parroquia_id;
                
                // Cargar municipios y parroquias para cuando se quita el responsable
                if ($this->estado_id) {
                    $this->municipios = Municipio::where('estado_id', $this->estado_id)->get();
                }
                if ($this->municipio_id) {
                    $this->parroquias = Parroquia::where('municipio_id', $this->municipio_id)->get();
                }
            }
        } else {
            // Si se quita el responsable, limpiar los campos de ubicación
            $this->estado_id = '';
            $this->municipio_id = '';
            $this->parroquia_id = '';
        }
    }

    public function updatedEstadoId()
    {
        // Solo permitir cambiar estado si no hay responsable seleccionado
        if (!$this->responsable_id) {
            $this->municipio_id = '';
            $this->parroquia_id = '';
            $this->municipios = collect();
            $this->parroquias = collect();

            if ($this->estado_id) {
                $this->municipios = Municipio::where('estado_id', $this->estado_id)->get();
            }
        }
    }

    public function updatedMunicipioId()
    {
        // Solo permitir cambiar municipio si no hay responsable seleccionado
        if (!$this->responsable_id) {
            $this->parroquia_id = '';
            $this->parroquias = collect();

            if ($this->municipio_id) {
                $this->parroquias = Parroquia::where('municipio_id', $this->municipio_id)->get();
            }
        }
    }

    public function updatedEstudiaActualmente()
    {
        if (!$this->estudia_actualmente) {
            $this->estudio_actual = '';
        }
    }

    public function updatedTrabajaActualmente()
    {
        if (!$this->trabaja_actualmente) {
            $this->lugar_trabajo = '';
            $this->ocupacion = '';
            $this->ingreso_mensual = '';
        }
    }

    // New methods for the additional fields
    public function updatedPoseeHabilidadProductiva()
    {
        if (!$this->posee_habilidad_productiva) {
            $this->habilidad_productiva = '';
        }
    }

    public function updatedPerteneceOrganizacionSocial()
    {
        if (!$this->pertenece_organizacion_social) {
            $this->tipo_organizacion_social = '';
            $this->otra_organizacion_social = '';
        }
    }

    public function updatedTipoOrganizacionSocial()
    {
        if ($this->tipo_organizacion_social !== 'Otros') {
            $this->otra_organizacion_social = '';
        }
    }

    // Methods for Step 4: Datos de Salud
    public function updatedTieneEvaluacionAntropometrica()
    {
        if (!$this->tiene_evaluacion_antropometrica) {
            $this->evaluacion_realizada_por = '';
        }
    }

    public function updatedPadeceDiscapacidadEnfermedad()
    {
        if (!$this->padece_discapacidad_enfermedad) {
            $this->diagnostico = '';
        }
    }

    // Methods for Step 5: Datos Socio-Familiares
    public function updatedPersonasNucleoFamiliar()
    {
        // Ensure total doesn't exceed nucleo familiar
        $this->validateTotalPersonas();
    }

    public function updatedNinosNiñas()
    {
        $this->validateTotalPersonas();
    }

    public function updatedAdolescentes()
    {
        $this->validateTotalPersonas();
    }

    public function updatedMujeres()
    {
        $this->validateTotalPersonas();
    }

    public function updatedHombres()
    {
        $this->validateTotalPersonas();
    }

    public function updatedAdultosMayores()
    {
        $this->validateTotalPersonas();
    }

    public function updatedMujeresEmbarazadas()
    {
        $this->validateTotalPersonas();
        // If there are pregnant women, check if the beneficiary is a pregnant woman
        if ($this->mujeres_embarazadas > 0) {
            $this->es_mujer_embarazada = true;
        }
    }

    public function updatedEsMujerEmbarazada()
    {
        if (!$this->es_mujer_embarazada) {
            $this->fecha_ultima_menstruacion = '';
            $this->edad_gestacion = '';
        }
    }

    private function validateTotalPersonas()
    {
        $total = $this->ninos_niñas + $this->adolescentes + $this->mujeres + $this->hombres + $this->adultos_mayores;
        
        if ($this->personas_nucleo_familiar > 0 && $total > $this->personas_nucleo_familiar) {
            // Reset all counts
            $this->ninos_niñas = 0;
            $this->adolescentes = 0;
            $this->mujeres = 0;
            $this->hombres = 0;
            $this->adultos_mayores = 0;
            $this->mujeres_embarazadas = 0;
            
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'La suma de las personas no puede exceder el total del núcleo familiar',
                'duration' => 5000
            ]);
        }
    }

    // Methods for the cart-like interface
    public function addToCart($asignacion)
    {
        if (!in_array($asignacion, $this->asignaciones_economicas)) {
            $this->asignaciones_economicas[] = $asignacion;
        }
    }

    public function removeFromCart($asignacion)
    {
        $this->asignaciones_economicas = array_filter(
            $this->asignaciones_economicas,
            fn($item) => $item !== $asignacion
        );
    }

    public function nextStep()
    {
        $this->validateStep($this->currentStep);
        if ($this->currentStep < 5) {
            $this->currentStep++;
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    private function validateStep($step)
    {
        $rules = [];
        
        switch ($step) {
            case 1:
                $rules = [
                    'nombres' => 'required|string|max:255',
                    'apellidos' => 'required|string|max:255',
                    'cedula' => 'required|string|max:20|unique:beneficiarios,cedula,' . $this->beneficiario->id,
                    'fecha_nacimiento' => 'nullable|date',
                    'telefono_principal' => 'nullable|string|max:20',
                    'telefono_alternativo' => 'nullable|string|max:20',
                    'estado_civil' => 'required|in:Soltero(a),Casado(a),Viudo(a),Divorciado(a)',
                    // Location fields
                    'estado_id' => 'nullable|exists:estados,id',
                    'municipio_id' => 'nullable|exists:municipios,id',
                    'parroquia_id' => 'nullable|exists:parroquias,id',
                ];
                break;
            case 2:
                $rules = [
                    'estudia_actualmente' => 'boolean',
                    'estudio_actual' => 'nullable|string|max:255',
                    'nivel_instruccion' => 'required|in:Analfabeto,Básica,Media Diversificada,TSU,Universitario,Maestría,Doctorado',
                    'ultimo_titulo_obtenido' => 'nullable|string|max:255',
                ];
                break;
            case 3:
                $rules = [
                    'trabaja_actualmente' => 'boolean',
                    'lugar_trabajo' => 'nullable|string|max:255',
                    'ocupacion' => 'nullable|string|max:255',
                    'ingreso_mensual' => 'nullable|numeric|min:0',
                    // New fields validation
                    'posee_habilidad_productiva' => 'boolean',
                    'habilidad_productiva' => 'nullable|string|max:255',
                    'pertenece_organizacion_social' => 'boolean',
                    'tipo_organizacion_social' => 'nullable|string|max:255',
                    'otra_organizacion_social' => 'nullable|string|max:255',
                    'asignaciones_economicas' => 'nullable|array',
                ];
                break;
            case 4:
                $rules = [
                    'tiene_evaluacion_antropometrica' => 'boolean',
                    'evaluacion_realizada_por' => 'nullable|string|max:255',
                    'condicion_ingreso' => 'nullable|in:Persona en situación de calle,Mujer embarazada,Desnutrición,Adulto mayor sin recursos,Incapacitado para trabajar,Persona discapacitada,Estudiante sin programa de estudio,Desempleado',
                    'fecha_ingreso' => 'nullable|date',
                    'padece_discapacidad_enfermedad' => 'boolean',
                    'diagnostico' => 'nullable|string|max:1000',
                    'recipe_ayuda_tecnica' => 'nullable|string|max:1000',
                ];
                break;
            case 5:
                $rules = [
                    'personas_nucleo_familiar' => 'nullable|integer|min:0',
                    'ninos_niñas' => 'nullable|integer|min:0',
                    'adolescentes' => 'nullable|integer|min:0',
                    'mujeres' => 'nullable|integer|min:0',
                    'hombres' => 'nullable|integer|min:0',
                    'adultos_mayores' => 'nullable|integer|min:0',
                    'mujeres_embarazadas' => 'nullable|integer|min:0',
                    'recipe_socio_familiar' => 'nullable|string|max:1000',
                    'es_mujer_embarazada' => 'boolean',
                    'fecha_ultima_menstruacion' => 'nullable|date',
                    'edad_gestacion' => 'nullable|integer|min:0|max:42',
                    'observaciones' => 'nullable|string|max:2000',
                ];
                break;
        }
        
        $this->validate($rules);
    }

    public function update()
    {
        //$this->validate();
        
        try {

        $responsable = Responsable::find($this->responsable_id);
 
            $this->beneficiario->update([
                'nombres' => $this->nombres,
                'apellidos' => $this->apellidos,
                'cedula' => $this->cedula,
                'fecha_nacimiento' => $this->fecha_nacimiento ?: null,
                'edad' => $this->edad,
                'telefono_principal' => $this->telefono_principal,
                'telefono_alternativo' => $this->telefono_alternativo,
                'estado_civil' => $this->estado_civil,
                'estudia_actualmente' => $this->estudia_actualmente ?: false,
                'estudio_actual' => $this->estudio_actual,
                'nivel_instruccion' => $this->nivel_instruccion,
                'ultimo_titulo_obtenido' => $this->ultimo_titulo_obtenido,
                'trabaja_actualmente' => $this->trabaja_actualmente ?: false,
                'lugar_trabajo' => $this->lugar_trabajo,
                'ocupacion' => $this->ocupacion,
                'ingreso_mensual' => $this->ingreso_mensual,
                'responsable_id' => $this->responsable_id ?: null,
                // Location fields
                'estado_id' => $responsable->estado_id ?: null,
                'municipio_id' => $responsable->municipio_id ?: null,
                'parroquia_id' => $responsable->parroquia_id ?: null,

                // New fields
                'posee_habilidad_productiva' => $this->posee_habilidad_productiva ?: false,
                'habilidad_productiva' => $this->habilidad_productiva,
                'pertenece_organizacion_social' => $this->pertenece_organizacion_social ?: false,
                'tipo_organizacion_social' => $this->tipo_organizacion_social,
                'otra_organizacion_social' => $this->otra_organizacion_social,
                'asignaciones_economicas' => $this->asignaciones_economicas,
                // Step 4: Datos de Salud
                'tiene_evaluacion_antropometrica' => $this->tiene_evaluacion_antropometrica ?: false,
                'evaluacion_realizada_por' => $this->evaluacion_realizada_por,
                'condicion_ingreso' => $this->condicion_ingreso,
                'fecha_ingreso' => $this->fecha_ingreso ?: null,
                'padece_discapacidad_enfermedad' => $this->padece_discapacidad_enfermedad ?: false,
                'diagnostico' => $this->diagnostico,
                'recipe_ayuda_tecnica' => $this->recipe_ayuda_tecnica,
                // Step 5: Datos Socio-Familiares
                'personas_nucleo_familiar' => $this->personas_nucleo_familiar ?: 0,
                'ninos_niñas' => $this->ninos_niñas ?: 0,
                'adolescentes' => $this->adolescentes ?: 0,
                'mujeres' => $this->mujeres ?: 0,
                'hombres' => $this->hombres ?: 0,
                'adultos_mayores' => $this->adultos_mayores ?: 0,
                'mujeres_embarazadas' => $this->mujeres_embarazadas ?: 0,
                'recipe_socio_familiar' => $this->recipe_socio_familiar,
                'es_mujer_embarazada' => $this->es_mujer_embarazada ?: false,
                'fecha_ultima_menstruacion' => $this->fecha_ultima_menstruacion ?: null,
                'edad_gestacion' => $this->edad_gestacion ?: 0,
                'observaciones' => $this->observaciones,
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Beneficiario actualizado exitosamente',
                'duration' => 5000
            ]);

            return redirect()->route('admin.beneficiarios.index');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al actualizar el beneficiario: ' . $e->getMessage(),
                'duration' => 10000
            ]);
        }
    }

    public function render()
    {
        // Filter available options based on search
        $filteredOptions = empty($this->searchAsignacion) 
            ? $this->available_asignaciones
            : array_filter($this->available_asignaciones, function($option) {
                return stripos($option, $this->searchAsignacion) !== false;
            });

        // Get options not yet selected
        $optionsNotInCart = array_values(array_diff($filteredOptions, $this->asignaciones_economicas));

        return $this->renderWithLayout('livewire.admin.beneficiarios.edit', [
            'responsables' => $this->responsables,
            'estados' => $this->estados,
            'municipios' => $this->municipios,
            'parroquias' => $this->parroquias,
            'optionsNotInCart' => $optionsNotInCart,
            'cartItems' => $this->asignaciones_economicas
        ], [
            'title' => 'Editar Beneficiario',
            'description' => 'Formulario para editar un beneficiario'
        ]);
    }
}