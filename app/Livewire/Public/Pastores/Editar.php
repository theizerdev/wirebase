<?php

namespace App\Livewire\Public\Pastores;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Pastor;
use App\Models\Ciudad;
use App\Models\Estado;
use App\Models\Parroquia;
use App\Models\Municipio;
use App\Models\Empresa;
use App\Models\Sucursal;

use Illuminate\Support\Facades\Log;

class Editar extends Component
{
    use HasDynamicLayout, WithFileUploads;

    public ?Pastor $pastor = null;
    public $esNuevoRegistro = false;



    // Control de pasos
    public int $currentStep = 1;
    public int $totalSteps = 4;

    // Paso 1: Datos personales
    public $codigo = '';
    public $nombres = '';
    public $apellidos = '';
    public $documento = '';
    public $fe_nacimiento = '';
    public $edad = '';
    public $genero = '';
    public $estado_civil = '';
    public $conyuge_id = '';
    public $conyuge_es_pastor = true;
    public $telefono_hab = '';
    public $telefono_tlf = '';
    public $telefono_otro = '';
    public $email = '';
    public $empresa_id = '';
    public $sucursal_id = '';

    // Datos del cónyuge a registrar
    public $conyuge_nombres = '';
    public $conyuge_apellidos = '';
    public $conyuge_documento = '';
    public $conyuge_genero = '';
    public $showConyugeModal = false;

    // Paso 2: Datos de ubicación
    public $edificio_casa_quinta = '';
    public $piso = '';
    public $apartamento = '';
    public $calle_avenida = '';
    public $urbanizacion = '';
    public $municipio_id = '';
    public $estado_id = '';
    public $ciudad_id = '';
    public $parroquia_id = '';
    public $latitud = '';
    public $longitud = '';

    // Paso 3: Datos académicos
    public $grado_instruccion = '';
    public $titulo_obtenido = '';
    public $estudio_teologico = false;
    public $titulo_teologico = '';
    public $tiempo_de_estudio_teologico = '';
    public $instituto_teologico = '';

    // Paso 4: Datos ministeriales
    public $nivel_ministerial = '';
    public $ano_promocion = '';
    public $tiempo_colaborando = '';
    public $zona = '';
    public $distrito = '';
    public $cargo_nacional = '';
    public $mencion = '';
    public $pertenece_ministerio = false;
    public $batizado_espiritu_santo = false;
    public $foto = '';
    public $foto_temporal = null;
    public $nota = '';
    public $status = true;

    // Listas desplegables
    public $estados = [];
    public $ciudades = [];
    public $parroquias = [];
    public $municipios = [];
    public $pastores = [];
    public $empresas = [];
    public $sucursales = [];
    public $pastorSeleccionado;
    // Propiedades para búsqueda de cónyuge
    public $conyuge_busqueda = '';
    public $resultados_conyuge;
    public $conyuge_encontrado;
    
    // Propiedades para verificación de seguridad
    public $showSecurityModal = false;
    public $securityVerificationType = 'questions'; // 'questions' o 'backup_code'
    public $securityAnswers = [];
    public $backupCodeInput = '';
    public $securityError = '';
    public $securityQuestions = [];

    protected $rules = [
        // Paso 1: Datos personales
        'codigo' => 'required|string|max:50',
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'documento' => 'required|string|max:50|unique:pastores,documento',
        'fe_nacimiento' => 'nullable|date',
        'edad' => 'nullable|integer|min:0',
        'genero' => 'nullable|string|max:50',
        'estado_civil' => 'nullable|string|max:50',
        'conyuge_id' => 'nullable|exists:pastores,id',
        'telefono_hab' => 'nullable|string|max:50',
        'telefono_tlf' => 'nullable|string|max:50',
        'telefono_otro' => 'nullable|string|max:50',
        'email' => 'nullable|email|max:255',
        'empresa_id' => 'nullable|exists:empresas,id',
        'sucursal_id' => 'nullable|exists:sucursales,id',

        // Datos del cónyuge (solo requeridos cuando se registra un nuevo cónyuge)
        'conyuge_nombres' => 'nullable|string|max:255',
        'conyuge_apellidos' => 'nullable|string|max:255',
        'conyuge_documento' => 'nullable|string|max:50|unique:pastores,documento',
        'conyuge_genero' => 'nullable|string|max:50',

        // Paso 2: Datos de ubicación
        'edificio_casa_quinta' => 'nullable|string|max:100',
        'piso' => 'nullable|string|max:10',
        'apartamento' => 'nullable|string|max:10',
        'calle_avenida' => 'nullable|string|max:255',
        'urbanizacion' => 'nullable|string|max:100',
        'municipio_id' => 'nullable|exists:municipios,id',
        'estado_id' => 'nullable|exists:estados,id',
        'ciudad_id' => 'nullable|exists:ciudades,id',
        'parroquia_id' => 'nullable|exists:parroquias,id',
        //'latitud' => 'nullable|numeric|between:-90,90',
        //'longitud' => 'nullable|numeric|between:-180,180',

        // Paso 3: Datos académicos
        'grado_instruccion' => 'nullable|string|max:100',
        'titulo_obtenido' => 'nullable|string|max:255',
        'estudio_teologico' => 'boolean',
        'titulo_teologico' => 'nullable|string|max:255',
        'tiempo_de_estudio_teologico' => 'nullable|string|max:100',
        'instituto_teologico' => 'nullable|string|max:255',

        // Paso 4: Datos ministeriales
        'nivel_ministerial' => 'nullable|string|max:100',
        'ano_promocion' => 'nullable|string|max:4',
        'tiempo_colaborando' => 'nullable|string|max:100',
        'zona' => 'nullable|string|max:100',
        'distrito' => 'nullable|string|max:100',
        'cargo_nacional' => 'nullable|string|max:100',
        'mencion' => 'nullable|string|max:500',
        'pertenece_ministerio' => 'boolean',
        'batizado_espiritu_santo' => 'boolean',
        'foto' => 'nullable|string|max:255',
        'foto_temporal' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'nota' => 'nullable|string',
        'status' => 'boolean',
    ];

    public function mount(Pastor $pastor)
    {
        // En la ruta pública no verificamos permisos de Auth, pero sí verificamos que el pastor esté activo
        if (!$pastor->status) {
            abort(404, 'Pastor no encontrado o inactivo.');
        }

        $this->pastor = $pastor;
        $this->pastorSeleccionado = $pastor;

        // Paso 1: Datos personales
        $this->codigo = $pastor->codigo;
        $this->nombres = $pastor->nombres;
        $this->apellidos = $pastor->apellidos;
        $this->documento = $pastor->documento;

        // Asegurar que la fecha de nacimiento tenga el formato correcto para el input type="date"
        if ($pastor->fe_nacimiento) {
            try {
                $this->fe_nacimiento = \Carbon\Carbon::parse($pastor->fe_nacimiento)->format('Y-m-d');
            } catch (\Exception $e) {
                $this->fe_nacimiento = null;
            }
        } else {
            $this->fe_nacimiento = null;
        }

        $this->edad = $pastor->edad;
        $this->genero = $pastor->genero;
        $this->estado_civil = $pastor->estado_civil;
        $this->conyuge_id = $pastor->conyuge_id;
        $this->conyuge_es_pastor = $pastor->conyuge_id ? true : false;
        $this->telefono_hab = $pastor->telefono_hab;
        $this->telefono_tlf = $pastor->telefono_tlf;
        $this->telefono_otro = $pastor->telefono_otro;
        $this->email = $pastor->email;
        $this->empresa_id = $pastor->empresa_id;
        $this->sucursal_id = $pastor->sucursal_id;

        // Paso 2: Datos de ubicación
        $this->edificio_casa_quinta = $pastor->edificio_casa_quinta;
        $this->piso = $pastor->piso;
        $this->apartamento = $pastor->apartamento;
        $this->calle_avenida = $pastor->calle_avenida;
        $this->urbanizacion = $pastor->urbanizacion;
        $this->municipio_id = $pastor->municipio_id;
        $this->estado_id = $pastor->estado_id;
        $this->ciudad_id = $pastor->ciudad_id;
        $this->parroquia_id = $pastor->parroquia_id;
        //$this->latitud = $pastor->latitud;
        //$this->longitud = $pastor->longitud;

        // Paso 3: Datos académicos
        $this->grado_instruccion = $pastor->grado_instruccion;
        $this->titulo_obtenido = $pastor->titulo_obtenido;
        $this->estudio_teologico = $pastor->estudio_teologico;
        $this->titulo_teologico = $pastor->titulo_teologico;
        $this->tiempo_de_estudio_teologico = $pastor->tiempo_de_estudio_teologico;
        $this->instituto_teologico = $pastor->instituto_teologico;

        // Paso 4: Datos ministeriales
        $this->nivel_ministerial = $pastor->nivel_ministerial;
        $this->ano_promocion = $pastor->ano_promocion;
        $this->tiempo_colaborando = $pastor->tiempo_colaborando;
        $this->zona = $pastor->zona;
        $this->distrito = $pastor->distrito;
        $this->cargo_nacional = $pastor->cargo_nacional;
        $this->mencion = $pastor->mencion;
        $this->pertenece_ministerio = $pastor->pertenece_ministerio;
        $this->batizado_espiritu_santo = $pastor->batizado_espiritu_santo;
        $this->foto = $pastor->foto;
        $this->nota = $pastor->nota;
        $this->status = $pastor->status;
        $this->empresa_id = $pastor->empresa_id;
        $this->sucursal_id = $pastor->sucursal_id;

        // Cargar listas desplegables
        $this->estados = Estado::orderBy('nombre')->get();

        // Cargar pastores disponibles para cónyuge según el género del pastor actual
        // Si el pastor actual es femenino (cónyuge), debe poder seleccionar al esposo (masculino)
        // Si el pastor actual es masculino, debe poder seleccionar a la esposa (femenina)
        if ($pastor->genero == 'Femenino') {
            // La persona actual es la esposa, por lo tanto debe poder seleccionar al esposo (masculino)
            $this->pastores = Pastor::whereNull('conyuge_id')
                ->where('genero', 'Masculino')
                ->where('id', '!=', $pastor->id)  // No se puede seleccionar a sí misma
                ->when($pastor->zona, function ($query, $zona) {
                    return $query->where('zona', $zona);
                })
                ->when($pastor->distrito, function ($query, $distrito) {
                    return $query->where('distrito', $distrito);
                })
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();
        } else {
            // La persona actual es el esposo, por lo tanto debe poder seleccionar a la esposa (femenina)
            $this->pastores = Pastor::whereNull('conyuge_id')
                ->where('genero', 'Femenino')
                ->where('id', '!=', $pastor->id)  // No se puede seleccionar a sí mismo
                ->when($pastor->zona, function ($query, $zona) {
                    return $query->where('zona', $zona);
                })
                ->when($pastor->distrito, function ($query, $distrito) {
                    return $query->where('distrito', $distrito);
                })
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();
        }


        if ($this->estado_id) {
            $this->ciudades = Ciudad::where('estado_id', $this->estado_id)->orderBy('nombre')->get();
            $this->municipios = Municipio::where('estado_id', $this->estado_id)->orderBy('nombre')->get();
        } else {
            $this->ciudades = collect();
            $this->municipios = collect();
        }

        if ($this->municipio_id) {
            $this->parroquias = Parroquia::where('municipio_id', $this->municipio_id)->orderBy('nombre')->get();
        } else {
            $this->parroquias = collect();
        }

        // Cargar empresas y sucursales
        $this->empresas = Empresa::where('status', true)->orderBy('razon_social')->get();
        if ($this->empresa_id) {
            $this->sucursales = Sucursal::where('empresa_id', $this->empresa_id)->where('status', true)->orderBy('nombre')->get();
        } else {
            $this->sucursales = collect();
        }
    }

    public function updatedEstadoId($value)
    {
        if ($value) {
            $this->ciudades = Ciudad::where('estado_id', $value)->orderBy('nombre')->get();
            $this->municipios = Municipio::where('estado_id', $value)->orderBy('nombre')->get();
        } else {
            $this->ciudades = collect();
            $this->municipios = collect();
        }
        $this->ciudad_id = null;
        $this->municipio_id = null;
        $this->parroquia_id = null;
        $this->parroquias = collect();
    }

    public function updatedCiudadId($value)
    {
        // No se cargan parroquias por ciudad_id, solo por municipio_id
        $this->parroquia_id = null;
    }

    public function updatedMunicipioId($value)
    {
        if ($value) {
            $this->parroquias = Parroquia::where('municipio_id', $value)->orderBy('nombre')->get();
        } else {
            $this->parroquias = collect();
        }
        $this->parroquia_id = null;
    }

    public function updatedEmpresaId($value)
    {
        if ($value) {
            $this->sucursales = Sucursal::where('empresa_id', $value)->where('status', true)->orderBy('nombre')->get();
        } else {
            $this->sucursales = collect();
        }
        $this->sucursal_id = null;
    }

    public function updatedFeNacimiento($value)
    {
        if ($value) {
            try {
                $fechaNacimiento = \Carbon\Carbon::parse($value);
                $hoy = \Carbon\Carbon::now();
                $this->edad = round($fechaNacimiento->diffInYears($hoy));
            } catch (\Exception $e) {
                $this->edad = '';
            }
        } else {
            $this->edad = '';
        }
    }

     public function updatedGenero($value)
    {
        // Cargar pastores disponibles para ser cónyuges según el género del pastor actual
        // Si el pastor actual es femenino (cónyuge), debe seleccionar al esposo (masculino)
        // Si el pastor actual es masculino, debe seleccionar a la esposa (femenina)
        if ($value == 'Femenino') {
            // La persona actual es la esposa, por lo tanto debe seleccionar al esposo (masculino)
            $this->pastores = Pastor::whereNull('conyuge_id')
                ->where('genero', 'Masculino')
                ->where('id', '!=', $this->pastor->id)  // No se puede seleccionar a sí misma
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();
        } else {
            // La persona actual es el esposo, por lo tanto debe seleccionar a la esposa (femenina)
            $this->pastores = Pastor::whereNull('conyuge_id')
                ->where('genero', 'Femenino')
                ->where('id', '!=', $this->pastor->id)  // No se puede seleccionar a sí mismo
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();
        }
    }

    public function updatedConyugeId($value)
    {
        // Verificar si el pastor actual ya tiene un cónyuge diferente al seleccionado
        $conyugeActualId = $this->pastor->conyuge_id;

        // Si ya tiene un cónyuge y está intentando seleccionar otro, mostrar error
        if ($conyugeActualId && $value && $conyugeActualId != $value) {
            // Verificar que el cónyuge actual no esté en una relación activa
            $conyugeActual = Pastor::find($conyugeActualId);
            if ($conyugeActual && $conyugeActual->conyuge_id == $this->pastor->id) {
                $this->addError('conyuge_id', 'Este pastor ya está casado. No puede seleccionar otro cónyuge.');
                $this->conyuge_id = $conyugeActualId; // Restablecer al valor anterior
                return;
            }
        }

        // Verificar que el nuevo cónyuge no esté ya casado con alguien más
        if ($value) {
            $nuevoConyuge = Pastor::find($value);
            if ($nuevoConyuge && $nuevoConyuge->conyuge_id && $nuevoConyuge->conyuge_id != $this->pastor->id) {
                $this->addError('conyuge_id', 'El pastor seleccionado ya está casado con otra persona.');
                $this->conyuge_id = $conyugeActualId; // Restablecer al valor anterior
                return;
            }
        }

        // Recargar la lista de pastores disponibles cuando se cambia el cónyuge
        $this->pastores = Pastor::where(function($query) use ($conyugeActualId, $value) {
                $query->whereNull('conyuge_id')
                      ->where('id', '!=', $this->pastor->id);

                // Incluir el cónyuge actual si existe
                if ($conyugeActualId) {
                    $query->orWhere('id', $conyugeActualId);
                }

                // Incluir el nuevo cónyuge seleccionado
                if ($value) {
                    $query->orWhere('id', $value);
                }
            })
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();
    }

    public function updatedEstadoCivil($value)
    {
        // Si el estado civil ya no es Casado, limpiar el cónyuge
        if ($value !== 'Casado') {
            $this->conyuge_id = null;
        }

        // Recargar la lista de pastores disponibles
        $conyugeActualId = $this->pastor->conyuge_id;

    }

    #[On('location-updated')]
    public function updateLocation($latitude, $longitude, $address)
    {
        $this->latitud = $latitude;
        $this->longitud = $longitude;
    }

    public function goToStep($step)
    {
        // Validar paso actual antes de avanzar
        if ($step > $this->currentStep) {
            $this->validateStep($this->currentStep);
        }

        $this->currentStep = $step;
    }

    public function nextStep()
    {
        $this->validateStep($this->currentStep);
        if ($this->currentStep < $this->totalSteps) {
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
        switch ($step) {
            case 1:
                $rules = [
                    'codigo' => 'required|string|max:50|unique:pastores,codigo,' . $this->pastor->id,
                    'nombres' => 'required|string|max:255',
                    'apellidos' => 'required|string|max:255',
                    'documento' => 'required|string|max:50|unique:pastores,documento,' . $this->pastor->id,
                    'fe_nacimiento' => 'nullable|date',
                    'edad' => 'nullable|integer|min:0',
                    'genero' => 'nullable|string|max:50',
                    'estado_civil' => 'nullable|string|max:50',
                    'telefono_hab' => 'nullable|string|max:50',
                    'telefono_tlf' => 'nullable|string|max:50',
                    'email' => 'nullable|email|max:255',
                    'conyuge_id' => 'nullable|exists:pastores,id',
                    //'empresa_id' => 'nullable|exists:empresas,id',
                    //'sucursal_id' => 'nullable|exists:sucursales,id',
                ];

                // Si está casado y no tiene cónyuge seleccionado, validar que se registren los datos
                if ($this->estado_civil === 'Casado' && !$this->conyuge_id) {
                    $rules['conyuge_nombres'] = 'required|string|max:255';
                    $rules['conyuge_apellidos'] = 'required|string|max:255';
                    $rules['conyuge_documento'] = 'required|string|max:50|unique:pastores,documento';
                }

                // Ajustar la validación del documento para excluir el pastor actual
        $rules['documento'] = 'required|string|max:50|unique:pastores,documento,' . $this->pastor->id;

        $this->validate($rules);
                break;
            case 2:
                $this->validate([
                    'estado_id' => 'nullable|exists:estados,id',
                    'ciudad_id' => 'nullable|exists:ciudades,id',
                    'municipio_id' => 'nullable|exists:municipios,id',
                    'parroquia_id' => 'nullable|exists:parroquias,id',
                ]);
                break;
            case 3:
                $this->validate([
                    'grado_instruccion' => 'nullable|string|max:100',
                    'titulo_obtenido' => 'nullable|string|max:255',
                    'estudio_teologico' => 'boolean',
                    'titulo_teologico' => 'nullable|string|max:255',
                    'tiempo_de_estudio_teologico' => 'nullable|string|max:100',
                    'instituto_teologico' => 'nullable|string|max:255',
                ]);
                break;
            case 4:
                $this->validate([
                    'nivel_ministerial' => 'nullable|string|max:100',
                    'ano_promocion' => 'nullable|string|max:4',
                    'tiempo_colaborando' => 'nullable|string|max:100',
                    'zona' => 'nullable|string|max:100',
                    'distrito' => 'nullable|string|max:100',
                    'cargo_nacional' => 'nullable|string|max:100',
                    'pertenece_ministerio' => 'boolean',
                    'batizado_espiritu_santo' => 'boolean',
                    'status' => 'boolean',
                ]);
                break;
        }
    }

    public function save()
    {
        // VERIFICAR SEGURIDAD SI ESTÁ ACTIVADA
        if ($this->pastor->preguntasSeguridad && $this->pastor->preguntasSeguridad->activado) {
            // Verificar si está bloqueado
            if ($this->pastor->preguntasSeguridad->estaBloqueado()) {
                $tiempoRestante = now()->diffInMinutes($this->pastor->preguntasSeguridad->ultimo_intento->addMinutes(15));
                $this->addError('security', "Demasiados intentos fallidos. Intente nuevamente en {$tiempoRestante} minutos.");
                return;
            }
            
            // Mostrar modal de verificación de seguridad
            $this->loadSecurityQuestions();
            $this->showSecurityModal = true;
            return;
        }
        
        // Si no tiene seguridad activada, proceder con el guardado normal
        $this->proceedWithSave();
    }
    
    /**
     * Cargar preguntas de seguridad del pastor
     */
    public function loadSecurityQuestions()
    {
        if ($this->pastor->preguntasSeguridad) {
            $this->securityQuestions = collect($this->pastor->preguntasSeguridad->preguntas)
                ->map(fn($q) => ['pregunta' => $q['pregunta'], 'respuesta' => ''])
                ->toArray();
        }
    }
    
    /**
     * Verificar respuestas de seguridad
     */
    public function verifySecurityAnswers()
    {
        $respuestas = collect($this->securityAnswers)
            ->map(fn($answer, $index) => [
                'pregunta' => $this->securityQuestions[$index]['pregunta'],
                'respuesta' => $answer
            ])
            ->toArray();
        
        if ($this->pastor->preguntasSeguridad->verificarRespuestas($respuestas)) {
            // Respuestas correctas - proceder con el guardado
            $this->showSecurityModal = false;
            $this->securityError = '';
            $this->proceedWithSave();
        } else {
            $this->securityError = 'Respuestas incorrectas. Intente nuevamente.';
            $this->securityAnswers = [];
        }
    }
    
    /**
     * Verificar código de respaldo
     */
    public function verifyBackupCode()
    {
        if ($this->pastor->preguntasSeguridad->verificarBackupCode($this->backupCodeInput)) {
            // Código válido - proceder con el guardado
            $this->showSecurityModal = false;
            $this->securityError = '';
            $this->proceedWithSave();
        } else {
            $this->securityError = 'Código de respaldo inválido.';
            $this->backupCodeInput = '';
        }
    }
    
    /**
     * Proceder con el guardado después de la verificación de seguridad
     */
    private function proceedWithSave()
    {
        // Validar todos los pasos antes de guardar
        $rules = $this->getRules();

        // Si está casado y no tiene cónyuge seleccionado, validar que se registren los datos
        if ($this->estado_civil === 'Casado' && !$this->conyuge_id) {
            $rules['conyuge_nombres'] = 'required|string|max:255';
            $rules['conyuge_apellidos'] = 'required|string|max:255';
            $rules['conyuge_documento'] = 'required|string|max:50|unique:pastores,documento';
        }

        // Ajustar la validación del documento para excluir el pastor actual
        $rules['documento'] = 'required|string|max:50|unique:pastores,documento,' . $this->pastor->id;

        $this->validate($rules);

        // Validación adicional: Verificar que no se esté intentando cambiar un cónyuge existente
        $conyugeActualId = $this->pastor->conyuge_id;
        if ($conyugeActualId && $this->estado_civil === 'Casado' && $this->conyuge_id && $conyugeActualId != $this->conyuge_id) {
            $conyugeActual = Pastor::find($conyugeActualId);
            if ($conyugeActual && $conyugeActual->conyuge_id == $this->pastor->id) {
                $this->addError('conyuge_id', 'No se puede cambiar el cónyuge de un pastor casado. Primero debe desvincular el matrimonio actual.');
                return;
            }
        }

        // Validar que el nuevo cónyuge no esté casado con alguien más
        if ($this->estado_civil === 'Casado' && $this->conyuge_id) {
            $nuevoConyuge = Pastor::find($this->conyuge_id);
            if ($nuevoConyuge && $nuevoConyuge->conyuge_id && $nuevoConyuge->conyuge_id != $this->pastor->id) {
                $this->addError('conyuge_id', 'El pastor seleccionado ya está casado con otra persona.');
                return;
            }
        }

        // Guardar el ID del cónyuge original antes de actualizar
        $conyuge_id_original = $this->pastor->conyuge_id;

        $this->pastor->update([
            // Paso 1: Datos personales
            'codigo' => $this->codigo,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'documento' => $this->documento,
            'fe_nacimiento' => $this->fe_nacimiento,
            'edad' => $this->edad,
            'genero' => $this->genero,
            'estado_civil' => $this->estado_civil,
            'conyuge_id' => $this->estado_civil === 'Casado' ? $this->conyuge_id : null,
            'telefono_hab' => $this->telefono_hab,
            'telefono_tlf' => $this->telefono_tlf,
            'telefono_otro' => $this->telefono_otro,
            'email' => $this->email,

            // Paso 2: Datos de ubicación
            'edificio_casa_quinta' => $this->edificio_casa_quinta,
            'piso' => $this->piso,
            'apartamento' => $this->apartamento,
            'calle_avenida' => $this->calle_avenida,
            'urbanizacion' => $this->urbanizacion,
            'municipio_id' => $this->municipio_id,
            'estado_id' => $this->estado_id,
            'ciudad_id' => $this->ciudad_id,
            'parroquia_id' => $this->parroquia_id,
            //'latitud' => $this->latitud,
            //'longitud' => $this->longitud,

            // Paso 3: Datos académicos
            'grado_instruccion' => $this->grado_instruccion,
            'titulo_obtenido' => $this->titulo_obtenido,
            'estudio_teologico' => $this->estudio_teologico,
            'titulo_teologico' => $this->titulo_teologico,
            'tiempo_de_estudio_teologico' => $this->tiempo_de_estudio_teologico,
            'instituto_teologico' => $this->instituto_teologico,

            // Paso 4: Datos ministeriales
            'nivel_ministerial' => $this->nivel_ministerial,
            'ano_promocion' => $this->ano_promocion,
            'tiempo_colaborando' => $this->tiempo_colaborando,
            'zona' => $this->zona,
            'distrito' => $this->distrito,
            'cargo_nacional' => $this->cargo_nacional,
            'mencion' => $this->mencion,
            'pertenece_ministerio' => $this->pertenece_ministerio,
            'batizado_espiritu_santo' => $this->batizado_espiritu_santo,
            'foto' => $this->foto,
            'nota' => $this->nota,
            'status' => $this->status,
            'empresa_id' => $this->empresa_id,
            'sucursal_id' => $this->sucursal_id,
        ]);

        // Si se ha cambiado el cónyuge, actualizar las relaciones
        if ($conyuge_id_original != ($this->estado_civil === 'Casado' ? $this->conyuge_id : null)) {
            // Desvincular al cónyuge anterior si existía
            if ($conyuge_id_original) {
                $conyuge_anterior = Pastor::find($conyuge_id_original);
                if ($conyuge_anterior) {
                    // Actualizar el estado civil del cónyuge anterior a "Soltero" (o el que corresponda)
                    $conyuge_anterior->update([
                        'conyuge_id' => null,
                        'estado_civil' => 'Soltero' // Asumiendo que al desvincularse vuelve a Soltero
                    ]);
                }
            }

            // Vincular al nuevo cónyuge si se seleccionó uno
            if ($this->estado_civil === 'Casado' && $this->conyuge_id) {
                $nuevo_conyuge = Pastor::find($this->conyuge_id);
                if ($nuevo_conyuge) {
                    // Actualizar ambos pastores para asegurar que estén "Casados"
                    $nuevo_conyuge->update([
                        'conyuge_id' => $this->pastor->id,
                        'estado_civil' => 'Casado'
                    ]);

                    // Asegurar que el pastor actual también esté "Casado"
                    if ($this->pastor->estado_civil !== 'Casado') {
                        $this->pastor->update(['estado_civil' => 'Casado']);
                    }
                }
            }
        }

        // Logica para Esposa / Cónyuge y asignación de extensiones
        try {
            Log::info("Buscando extensiones para el pastor ID: {$this->pastor->id}");

            // Verificamos si es una esposa (cónyuge de alguien) y si ese alguien tiene extensiones
            $iglesia = null;
            
            // Primero verificamos si el pastor actual tiene iglesias como pastor principal
            $iglesiaPropia = $this->pastor->iglesias()->first();
            
            // Si no tiene iglesias propias, verificamos si es cónyuge y obtenemos la iglesia del pastor principal
            if (!$iglesiaPropia && $this->pastor->esConyuge() && $this->pastor->pastorPrincipal) {
                Log::info("El usuario es cónyuge del pastor ID: {$this->pastor->pastorPrincipal->id}. Buscando extensiones del esposo.");
                $iglesiaDelEsposo = $this->pastor->pastorPrincipal->iglesias()->first();
                
                if ($iglesiaDelEsposo) {
                    Log::info("Se encontró la extensión del esposo (Extensión ID: {$iglesiaDelEsposo->id}).");
                    
                    // Permitir la edición de la iglesia del esposo si el cónyuge la va a editar
                    $iglesia = $iglesiaDelEsposo;
                }
            } else {
                // Si tiene iglesias propias, permitir la edición
                $iglesia = $iglesiaPropia;
            }

            if ($iglesia) {
                // Si la extensión encontrada pertenece al esposo, redirigimos a la página de completado
                // porque ya existe una iglesia asociada al matrimonio
                if ($iglesia->pastor_id !== $this->pastor->id) {
                     Log::info("La extensión pertenece al esposo. El matrimonio ya tiene una iglesia registrada. Redirigiendo a completado.");
                     session()->flash('message', '¡Datos del pastor actualizados exitosamente! Su esposo ya ha registrado la iglesia familiar.');
                     return redirect()->route('public.completado', ['pastor' => $this->pastor->id]);
                }

                Log::info("Extensión propia encontrada (ID: {$iglesia->id}). Redirigiendo a edición.");
                session()->flash('message', '¡Datos del pastor actualizados exitosamente! Ahora puede actualizar los datos de su iglesia.');
                return redirect()->route('public.iglesias.editar', ['pastor' => $this->pastor->id, 'iglesia' => $iglesia->id]);
            } else {
                Log::info("No se encontraron iglesias. Redirigiendo a creación de iglesia.");
                session()->flash('message', '¡Datos del pastor actualizados exitosamente! Por favor, registre los datos de su iglesia.');
                return redirect()->route('public.iglesias.crear', ['pastor' => $this->pastor->id]);
            }
        } catch (\Exception $e) {
            Log::error("Error al procesar la redirección a la extensión para el pastor ID: {$this->pastor->id}. Error: " . $e->getMessage());
            session()->flash('message', '¡Datos del pastor actualizados exitosamente! Sin embargo, hubo un problema al procesar la información de su iglesia.');
            return redirect()->route('public.pastores.busqueda');
        }
    }

    public function render()
    {
        return view('livewire.public.pastores.editar', [
            'estados' => $this->estados,
            'ciudades' => $this->ciudades,
            'parroquias' => $this->parroquias,
            'municipios' => $this->municipios,
            'pastores' => $this->pastores,
            'empresas' => $this->empresas,
            'sucursales' => $this->sucursales,
        ])->layout('components.layouts.auth-basic', ['title' => 'Actualizar Datos']);
    }

    public function openConyugeModal()
    {
        $this->resetConyugeData();
        $this->showConyugeModal = true;
        $this->dispatch('open-conyuge-modal');
    }

    public function closeConyugeModal()
    {
        $this->showConyugeModal = false;
        $this->resetConyugeData();
        $this->dispatch('close-conyuge-modal');
    }

    private function resetConyugeData()
    {
        $this->conyuge_nombres = '';
        $this->conyuge_apellidos = '';
        $this->conyuge_documento = '';
        $this->conyuge_genero = '';
    }

    public function saveConyuge()
    {
        // Validar solo si se está creando un nuevo cónyuge
        $this->validate([
            'conyuge_nombres' => 'required|string|max:255',
            'conyuge_apellidos' => 'required|string|max:255',
            'conyuge_documento' => 'required|string|max:50|unique:pastores,documento',
            'conyuge_genero' => 'nullable|string|max:50',
        ]);

        // Generar un código único para el cónyuge
        $codigo = 'CONY-' . strtoupper(substr(md5(time() . $this->conyuge_documento), 0, 8));

        // Crear el registro del cónyuge como pastor
        $conyuge = Pastor::create([
            'codigo' => $codigo,
            'nombres' => $this->conyuge_nombres,
            'apellidos' => $this->conyuge_apellidos,
            'documento' => $this->conyuge_documento,
            'genero' => $this->conyuge_genero,
            'estado_civil' => 'Casado',
            'status' => true, // El cónyuge debe estar activo para que pueda buscar su perfil
            'pertenece_ministerio' => false,
        ]);

        // Actualizar la lista de pastores y seleccionar el nuevo cónyuge
        $conyugeActualId = $this->pastor->conyuge_id;
        $this->pastores = Pastor::where(function($query) use ($conyugeActualId, $conyuge) {
                $query->whereNull('conyuge_id')
                      ->where('id', '!=', $this->pastor->id);

                // Incluir el cónyuge actual si existe
                if ($conyugeActualId) {
                    $query->orWhere('id', $conyugeActualId);
                }

                // Incluir el nuevo cónyuge creado
                $query->orWhere('id', $conyuge->id);
            })
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();
        $this->conyuge_id = $conyuge->id;
        $this->conyuge_es_pastor = true;

        // Asegurar que el pastor actual tenga estado civil "Casado"
        if ($this->estado_civil !== 'Casado') {
            $this->estado_civil = 'Casado';
        }

        // Cerrar la modal
        $this->showConyugeModal = false;
        $this->dispatch('close-conyuge-modal');

        session()->flash('message', 'Cónyuge registrado correctamente.');
    }

    public function updatedFotoTemporal()
    {
        if ($this->foto_temporal) {
            try {
                // Eliminar foto anterior si existe
                if ($this->foto) {
                    Storage::disk('public')->delete($this->foto);
                }

                // Crear instancia del ImageManager con driver GD
                $manager = new ImageManager(new Driver());

                // Procesar la imagen
                $image = $manager->read($this->foto_temporal->getRealPath());

                // Recortar a cuadrado centrado (aspecto tipo carnet)
                $image->cover(300, 300);

                // Generar nombre único para la imagen
                $filename = 'pastores/' . uniqid('pastor_') . '.jpg';

                // Guardar la imagen procesada
                Storage::disk('public')->put($filename, $image->encodeByExtension('jpg', quality: 90));

                // Actualizar la propiedad foto con la ruta
                $this->foto = $filename;

                // Limpiar el campo temporal
                $this->reset('foto_temporal');

                session()->flash('message', 'Foto actualizada correctamente.');

            } catch (\Exception $e) {
                $this->reset('foto_temporal');
                session()->flash('error', 'Error al procesar la imagen: ' . $e->getMessage());
            }
        }
    }

    
    public function updatedConyugeBusqueda($value)
    { 
         if (!empty($value)) {
                $this->buscarConyugePorTexto($value);
            } else {
                $this->resultados_conyuge = collect([]);
            }
    }

    public function buscarConyugePorTexto($texto)
{
    $query = Pastor::whereNull('conyuge_id')
        ->where('id', '!=', $this->pastor->id); // No se puede seleccionar a sí mismo

    // Filtrar por género opuesto al actual
    if ($this->genero === 'Femenino') {
        $query->where('genero', 'Masculino');
    } else {
        $query->where('genero', 'Femenino');
    }

    // Filtrar por zona y distrito del pastor actual
    if ($this->zona) {
        $query->where('zona', $this->zona);
    }
    if ($this->distrito) {
        $query->where('distrito', $this->distrito);
    }

    // Filtrar por nombre o documento
    $query->where(function($q) use ($texto) {
        $q->where('nombres', 'LIKE', '%' . $texto . '%')
          ->orWhere('apellidos', 'LIKE', '%' . $texto . '%')
          ->orWhere('documento', 'LIKE', '%' . $texto . '%');
    });

    $this->resultados_conyuge = $query->limit(10)
        ->orderBy('nombres')
        ->orderBy('apellidos')
        ->get();
}


public function seleccionarConyuge($id)
{
    $pastor = Pastor::find($id);
    if ($pastor) {
        $this->conyuge_id = $pastor->id;
        $this->conyuge_encontrado = $pastor;
        $this->conyuge_busqueda = $pastor->nombres . ' ' . $pastor->apellidos;
        $this->resultados_conyuge = collect([]);
    }
}

public function limpiarConyuge()
{
    $this->conyuge_id = null;
    $this->conyuge_encontrado = null;
    $this->conyuge_busqueda = '';
    $this->resultados_conyuge = collect([]);
}

    
    public function removeFoto()
    {
        if ($this->foto) {
            Storage::disk('public')->delete($this->foto);
            $this->foto = '';
            session()->flash('message', 'Foto eliminada correctamente.');
        }
    }

    public function updatedZona($value)
    {
        // Recargar la lista de pastores disponibles cuando se cambia la zona
        if ($this->genero == 'Femenino') {
            // La persona actual es la esposa, por lo tanto debe poder seleccionar al esposo (masculino)
            $this->pastores = Pastor::whereNull('conyuge_id')
                ->where('genero', 'Masculino')
                ->where('id', '!=', $this->pastor->id)  // No se puede seleccionar a sí misma
                ->when($value, function ($query, $zona) {
                    return $query->where('zona', $zona);
                })
                ->when($this->distrito, function ($query, $distrito) {
                    return $query->where('distrito', $distrito);
                })
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();
        } else {
            // La persona actual es el esposo, por lo tanto debe poder seleccionar a la esposa (femenina)
            $this->pastores = Pastor::whereNull('conyuge_id')
                ->where('genero', 'Femenino')
                ->where('id', '!=', $this->pastor->id)  // No se puede seleccionar a sí mismo
                ->when($value, function ($query, $zona) {
                    return $query->where('zona', $zona);
                })
                ->when($this->distrito, function ($query, $distrito) {
                    return $query->where('distrito', $distrito);
                })
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();
        }
    }

    public function updatedDistrito($value)
    {
        // Recargar la lista de pastores disponibles cuando se cambia el distrito
        if ($this->genero == 'Femenino') {
            // La persona actual es la esposa, por lo tanto debe poder seleccionar al esposo (masculino)
            $this->pastores = Pastor::whereNull('conyuge_id')
                ->where('genero', 'Masculino')
                ->where('id', '!=', $this->pastor->id)  // No se puede seleccionar a sí misma
                ->when($this->zona, function ($query, $zona) {
                    return $query->where('zona', $zona);
                })
                ->when($value, function ($query, $distrito) {
                    return $query->where('distrito', $distrito);
                })
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();
        } else {
            // La persona actual es el esposo, por lo tanto debe poder seleccionar a la esposa (femenina)
            $this->pastores = Pastor::whereNull('conyuge_id')
                ->where('genero', 'Femenino')
                ->where('id', '!=', $this->pastor->id)  // No se puede seleccionar a sí mismo
                ->when($this->zona, function ($query, $zona) {
                    return $query->where('zona', $zona);
                })
                ->when($value, function ($query, $distrito) {
                    return $query->where('distrito', $distrito);
                })
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();
        }
    }

    // Propiedad para almacenar resultados de búsqueda de cónyuge
    public $searchResults = [];

    public function searchConyuge($searchTerm = '')
    {
        $query = Pastor::whereNull('conyuge_id')
            ->where('id', '!=', $this->pastor->id); // No se puede seleccionar a sí mismo

        // Filtrar por género opuesto al actual
        if ($this->pastor->genero === 'Femenino') {
            $query->where('genero', 'Masculino');
        } else {
            $query->where('genero', 'Femenino');
        }

        // Filtrar por zona y distrito del pastor actual
        if ($this->pastor->zona) {
            $query->where('zona', $this->pastor->zona);
        }
        if ($this->pastor->distrito) {
            $query->where('distrito', $this->pastor->distrito);
        }

        // Si hay término de búsqueda, filtrar por nombre o documento
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombres', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('apellidos', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('documento', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $this->searchResults = $query->limit(10) // Limitar resultados
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get()
            ->toArray();
    }
}
