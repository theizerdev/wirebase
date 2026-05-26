<?php

namespace App\Livewire\Public\Iglesias;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Iglesia;
use App\Models\Pastor;
use App\Models\Ciudad;
use App\Models\Estado;
use App\Models\Parroquia;
use App\Models\TipoLocal;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Editar extends Component
{
    use HasDynamicLayout;

    public Iglesia $iglesia;

    // Control de pasos
    public $currentStep = 1;
    public $totalSteps = 4; // Agregado paso 5 para inventario

    // Paso 1: Datos básicos
    public $nombre = '';
    public $direccion = '';
    public $telefono = '';
    public $email = '';

    // Paso 2: Ubicación geográfica
    public $estado_id = '';
    public $ciudad_id = '';
    public $municipio_id = '';
    public $parroquia_id = '';
    public $zona = '';
    public $distrito = '';
    public $sector = '';
    public $calle = '';
    public $avenida = '';
    public $latitud = '';
    public $longitud = '';
    public $aniosActiva = '';

    // Paso 3: Información de la extensión
    public $fecha_fundacion = '';
        public $descripcion = '';
    public $pastor_id = '';
    public $tipo_local_id = '';
    public $activa = true;

    // Paso 4: Estadísticas y medios de comunicación
    public $miembros_activos = '';
    public $cantidad_campos_blancos = '';
    public $miembro_probante = '';
    public $logros_obtenidos = '';
    public $tiempo_trabajo = '';
    public $iglesias_fundadas = '';
    public $pastores_ministerio = '';
    public $posee_medio_comunicacion = false;

    // Lista de medios
    public $medios_comunicacion_list = [];
    public $nuevo_medio_tipo = '';
    public $nuevo_medio_nombre = '';
    public $nuevo_medio_ubicacion = '';

    // Listas desplegables
    public $estados = [];
    public $ciudades = [];
    public $municipios = [];
    public $parroquias = [];
    public $pastores = [];
    public $tiposLocal = [];

    // Información del cónyuge
    public $pastorSeleccionado = null;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'direccion' => 'nullable|string|max:500',
        'telefono' => 'nullable|string|max:50',
        'email' => 'nullable|email|max:255',
        'estado_id' => 'nullable|exists:estados,id',
        'ciudad_id' => 'nullable|exists:ciudades,id',
        'municipio_id' => 'nullable|exists:municipios,id',
        'parroquia_id' => 'nullable|exists:parroquias,id',
        'latitud' => 'nullable|numeric|between:-90,90',
        'longitud' => 'nullable|numeric|between:-180,180',
        'zona' => 'nullable|string|max:100',
        'distrito' => 'nullable|string|max:100',
        'fecha_fundacion' => 'nullable|date',
        'descripcion' => 'nullable|string',
        'activa' => 'boolean',
        'pastor_id' => 'nullable|exists:pastores,id',
        'tipo_local_id' => 'nullable|exists:tipo_locales,id',
        'medios_comunicacion_list' => 'nullable|array',
    ];

    public $pastorFijo = null;
    public $esNuevoRegistro = false;

    public function agregarMedio()
    {
        $this->validate([
            'nuevo_medio_tipo' => 'required|string|max:100',
            'nuevo_medio_nombre' => 'required|string|max:255',
            'nuevo_medio_ubicacion' => 'nullable|string|max:255',
        ], [
            'nuevo_medio_tipo.required' => 'El tipo de medio es requerido.',
            'nuevo_medio_nombre.required' => 'El nombre del medio es requerido.',
        ]);

        $this->medios_comunicacion_list[] = [
            'tipo' => $this->nuevo_medio_tipo,
            'nombre' => $this->nuevo_medio_nombre,
            'ubicacion' => $this->nuevo_medio_ubicacion,
        ];

        $this->nuevo_medio_tipo = '';
        $this->nuevo_medio_nombre = '';
        $this->nuevo_medio_ubicacion = '';
    }

    public function eliminarMedio($index)
    {
        unset($this->medios_comunicacion_list[$index]);
        $this->medios_comunicacion_list = array_values($this->medios_comunicacion_list);
    }

    public function mount(Pastor $pastor, Iglesia $iglesia = null)
    {
        // En la ruta pública no verificamos permisos de Auth, pero sí verificamos que el pastor esté activo
        if (!$pastor->status) {
            abort(404, 'Pastor no encontrado o inactivo.');
        }

        $this->pastorFijo = $pastor;

        // Si el pastor es esposa y tiene un pastor principal, asignamos el pastor_id al esposo
        if ($pastor->esConyuge() && $pastor->pastorPrincipal) {
            $this->pastor_id = $pastor->pastorPrincipal->id;
            $this->pastorSeleccionado = $pastor; // Para mostrar el bloque de matrimonio correctamente
        } else {
            $this->pastor_id = $pastor->id;
            $this->pastorSeleccionado = $pastor; // Para mostrar el bloque de matrimonio correctamente
        }

        // Si se pasa una extensión, significa que vamos a editarla
        // Si no se pasa, significa que vamos a crear una nueva para este pastor
        if ($iglesia && $iglesia->exists) {
            // Verificar que la extensión pertenezca al pastor
            if ($iglesia->pastor_id != $pastor->id) {
                abort(403, 'No tiene permisos para editar esta iglesia.');
            }

            $this->iglesia = $iglesia;
            $this->esNuevoRegistro = false;

            // Paso 1: Datos básicos
            $this->nombre = $iglesia->nombre;
            $this->direccion = $iglesia->direccion;
            $this->telefono = $iglesia->telefono;
            $this->email = $iglesia->email;

            // Paso 2: Ubicación geográfica
            $this->estado_id = $iglesia->estado_id;
            $this->ciudad_id = $iglesia->ciudad_id;
            $this->municipio_id = $iglesia->municipio_id;
            $this->parroquia_id = $iglesia->parroquia_id;
            $this->latitud = $iglesia->latitud;
            $this->longitud = $iglesia->longitud;
            $this->zona = $iglesia->zona;
            $this->distrito = $iglesia->distrito;
            $this->sector = $iglesia->sector;
            $this->calle = $iglesia->calle;
            $this->avenida = $iglesia->avenida;

            // Paso 3: Información de la extensión
            $fecha = $iglesia->fecha_fundacion;
            $this->fecha_fundacion = $fecha ? $fecha->format('Y-m-d') : '';
            $this->descripcion = $iglesia->descripcion;
            $this->activa = $iglesia->activa;
            $this->pastor_id = $iglesia->pastor_id;
            $this->tipo_local_id = $iglesia->tipo_local_id;

            // Paso 4: Estadísticas y medios de comunicación
            $this->miembros_activos = $iglesia->miembros_activos;
            $this->cantidad_campos_blancos = $iglesia->cantidad_campos_blancos;
            $this->miembro_probante = $iglesia->miembro_probante;
            $this->logros_obtenidos = $iglesia->logros_obtenidos;
            $this->tiempo_trabajo = $iglesia->tiempo_trabajo;
            $this->iglesias_fundadas = $iglesia->iglesias_fundadas;
            $this->pastores_ministerio = $iglesia->pastores_ministerio;
            $this->posee_medio_comunicacion = (bool)$iglesia->posee_medio_comunicacion;

            // Manejar el campo medio_comunicacion como array
            if (is_array($iglesia->medio_comunicacion)) {
                $this->medios_comunicacion_list = $iglesia->medio_comunicacion;
            } else if (is_string($iglesia->medio_comunicacion) && !empty($iglesia->medio_comunicacion)) {
                $decoded = json_decode($iglesia->medio_comunicacion, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $this->medios_comunicacion_list = $decoded;
                } else {
                    // Formato legacy, convertir a array
                    $this->medios_comunicacion_list = [[
                        'tipo' => $iglesia->medio_comunicacion,
                        'nombre' => $iglesia->nombre_medio_comunicacion ?? 'No especificado',
                        'ubicacion' => $iglesia->donde_medio_comunicacion ?? ''
                    ]];
                }
            }
            $this->aniosActiva = $iglesia->anios_activa ?? '0';

            // Emitir evento para que el mapa se centre en las coordenadas guardadas
            $this->dispatch('iglesia-coordinates-loaded',
                latitud: $iglesia->latitud,
                longitud: $iglesia->longitud
            );
        } else {
            // Es un registro nuevo
            $this->iglesia = new Iglesia();
            $this->esNuevoRegistro = true;
            $this->activa = true;
            $this->posee_medio_comunicacion = false;
        }

        // Cargar listas desplegables
        $this->estados = Estado::orderBy('nombre')->get();
        $this->pastores = Pastor::activos()->orderBy('nombres')->orderBy('apellidos')->get();
        $this->tiposLocal = TipoLocal::where('status', true)->orderBy('nombre')->get();

        // Inicializar pastor seleccionado con su cónyuge si existe
        if ($this->pastor_id) {
            $this->pastorSeleccionado = Pastor::with('pastoresConyuge')->find($this->pastor_id);
        }

        if ($this->estado_id) {
            $this->ciudades = Ciudad::where('estado_id', $this->estado_id)->orderBy('nombre')->get();
        } else {
            $this->ciudades = collect();
        }

        if ($this->ciudad_id && $this->estado_id) {
            $this->municipios = \App\Models\Municipio::where('estado_id', $this->estado_id)->orderBy('nombre')->get();
        } else {
            $this->municipios = collect();
        }

        if ($this->municipio_id) {
            $this->parroquias = Parroquia::where('municipio_id', $this->municipio_id)->orderBy('nombre')->get();
        } else {
            $this->parroquias = collect();
        }
    }

    public function updatedFechaFundacion($value)
    {

        $this->aniosActiva = $this->getAniosActivaAttribute();

    }

    /**
     * Calcula los años activos basados en la fecha de fundación
     * Este método se puede usar en la vista si se necesita mostrar el valor
     */
    public function getAniosActivaAttribute()
    {
        if ($this->fecha_fundacion) {
            try {
                $fecha = \Carbon\Carbon::parse($this->fecha_fundacion);
                $ahora = \Carbon\Carbon::now();

                // Asegurarse de que la fecha no sea futura
                if ($fecha->isFuture()) {
                    return '0';
                }

                $anios = (int) $ahora->diffInYears($fecha);
                return (string) abs($anios); // Valor absoluto para asegurar positivo
            } catch (\Exception $e) {
                return '0';
            }
        }
        return '0';
    }

    public function updatedEstadoId($value)
    {
        if ($value) {
            $this->ciudades = Ciudad::where('estado_id', $value)->orderBy('nombre')->get();
        } else {
            $this->ciudades = collect();
        }
        $this->ciudad_id = null;
        $this->municipio_id = null;
        $this->parroquia_id = null;
        $this->municipios = collect();
        $this->parroquias = collect();
    }

    public function updatedCiudadId($value)
    {
        if ($value) {
            $this->municipios = \App\Models\Municipio::where('estado_id', $this->estado_id)->orderBy('nombre')->get();
        } else {
            $this->municipios = collect();
        }
        $this->municipio_id = null;
        $this->parroquia_id = null;
        $this->parroquias = collect();
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

    public function actualizarCoordenadas($lat, $lng, $direccion = null)
    {
        $this->latitud = $lat;
        $this->longitud = $lng;

        // Si se proporciona una dirección, actualizarla también
        if ($direccion) {
            $this->direccion = $direccion;
        }
    }

    public function getCoordenadasCiudad()
    {
        if ($this->ciudad_id) {
            $ciudad = \App\Models\Ciudad::find($this->ciudad_id);
            if ($ciudad && $ciudad->latitud && $ciudad->longitud) {
                return [
                    'lat' => $ciudad->latitud,
                    'lng' => $ciudad->longitud
                ];
            }
        }

        // Coordenadas por defecto de Venezuela
        return [
            'lat' => 6.4238,
            'lng' => -66.5897
        ];
    }

    public function updatedPastorId($value)
    {
        if ($value) {
            $pastor = Pastor::with(['conyuge', 'pastoresConyuge'])->find($value);
            $this->pastorSeleccionado = $pastor;
        } else {
            $this->pastorSeleccionado = null;
        }
    }

    public function nextStep()
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->validateStep();
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
            $this->currentStep = $step;
        }
    }

    protected function validateStep()
    {
        switch ($this->currentStep) {
            case 1:
                $this->validate([
                    'nombre' => 'required|string|max:255',
                    'direccion' => 'nullable|string|max:500',
                    'telefono' => 'nullable|string|max:50',
                    'email' => 'nullable|email|max:255',
                ]);
                break;
            case 2:
                $this->validate([
                    'estado_id' => 'nullable|exists:estados,id',
                    'ciudad_id' => 'nullable|exists:ciudades,id',
                    'municipio_id' => 'nullable|exists:municipios,id',
                    'parroquia_id' => 'nullable|exists:parroquias,id',
                    'latitud' => 'nullable|numeric|between:-90,90',
                    'longitud' => 'nullable|numeric|between:-180,180',
                    'zona' => 'nullable|string|max:100',
                    'distrito' => 'nullable|string|max:100',
                    'sector' => 'nullable|string|max:100',
                    'calle' => 'nullable|string|max:100',
                    'avenida' => 'nullable|string|max:100',
                ]);
                break;
            case 3:
                $this->validate([
                    'fecha_fundacion' => 'nullable|date',
                    'descripcion' => 'nullable|string',
                    'activa' => 'boolean',
                    'pastor_id' => 'nullable|exists:pastores,id',
                    'tipo_local_id' => 'nullable|exists:tipo_locales,id',
                ]);
                break;
        }
    }

    #[On('location-updated')]
    public function updateLocation($latitude, $longitude, $address)
    {
        $this->latitud = $latitude;
        $this->longitud = $longitude;
    }

    public function save()
    {
       
        $this->validate();

        $data = [
            // Paso 1: Datos básicos
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'email' => $this->email,

            // Paso 2: Ubicación geográfica
            'estado_id' => $this->estado_id ?: null,
            'ciudad_id' => $this->ciudad_id ?: null,
            'municipio_id' => $this->municipio_id ?: null,
            'parroquia_id' => $this->parroquia_id ?: null,
            'tipo_local_id' => $this->tipo_local_id ?: null,
            'latitud' => $this->latitud ?: null,
            'longitud' => $this->longitud ?: null,
            'zona' => $this->zona,
            'distrito' => $this->distrito,
            'sector' => $this->sector,
            'calle' => $this->calle,
            'avenida' => $this->avenida,

            // Paso 3: Información de la extensión
            'fecha_fundacion' => $this->fecha_fundacion ?: null,
            'descripcion' => $this->descripcion,
            'activa' => $this->activa,
            'pastor_id' => $this->pastor_id ?: null,

            // Paso 4: Estadísticas y medios de comunicación
            'miembros_activos' => $this->miembros_activos,
            'cantidad_campos_blancos' => $this->cantidad_campos_blancos,
            'miembro_probante' => $this->miembro_probante,
            'logros_obtenidos' => $this->logros_obtenidos,
            'tiempo_trabajo' => $this->tiempo_trabajo,
            'iglesias_fundadas' => $this->iglesias_fundadas,
            'pastores_ministerio' => $this->pastores_ministerio,
            'posee_medio_comunicacion' => $this->posee_medio_comunicacion,
            'medio_comunicacion' => $this->medios_comunicacion_list,
            'nombre_medio_comunicacion' => null,
            'donde_medio_comunicacion' => null,
        ];

        if ($this->pastorFijo) {
            // Asegurar que el pastor de la extensión sea el esposo si quien registra es la esposa
            if ($this->pastorFijo->esConyuge() && $this->pastorFijo->pastorPrincipal) {
                $data['pastor_id'] = $this->pastorFijo->pastorPrincipal->id;
            } else {
                $data['pastor_id'] = $this->pastorFijo->id;
            }

            // Agregar empresa_id y sucursal_id basados en el pastor
            $pastor = $this->pastorFijo;
            $data['empresa_id'] = $pastor->empresa_id; // Usar la empresa del pastor
            $data['sucursal_id'] = $pastor->sucursal_id; // Usar la sucursal del pastor
        }

        if ($this->esNuevoRegistro) {
            $data['user_id'] = 1; // Un usuario por defecto ya que no hay Auth
            $this->iglesia = Iglesia::create($data);
            $mensaje = 'Extensión registrada exitosamente.';
        } else {
            $this->iglesia->update($data);
            $mensaje = 'Extensión actualizada exitosamente.';
        }

        session()->flash('message', $mensaje);

        // Redirigir al inventario para que el usuario pueda gestionar los bienes
        return redirect()->route('public.completado', $this->pastor_id);
    }

    public function render()
    {
        return view('livewire.public.iglesias.editar', [
            'estados' => $this->estados,
            'ciudades' => $this->ciudades,
            'municipios' => $this->municipios,
            'parroquias' => $this->parroquias,
            'pastores' => $this->pastores,
            'tiposLocal' => $this->tiposLocal,
        ])->layout('components.layouts.auth-basic', ['title' => 'Actualizar Extensión']);
    }
}
