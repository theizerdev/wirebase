<?php

namespace App\Livewire\Admin\Iglesias;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Iglesia;
use App\Models\Pastor;
use App\Models\Ciudad;
use App\Models\Estado;
use App\Models\Parroquia;
use App\Models\TipoLocal;
use Livewire\Attributes\On;

class Create extends Component
{
    use HasDynamicLayout;

    // Control de pasos
    public $currentStep = 1;
    public $totalSteps = 4;

    // Paso 1: Datos básicos
    public $nombre = '';
    public $direccion = '';
    public $telefono = '';
    public $email = '';
    public $aniosActiva = '';

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

    // Paso 3: Información de la extensión
    public $fecha_fundacion = '';
    public $descripcion = '';
    public $pastor_id = '';
    public $tipo_local_id = '';
    public $activa = true;

    // Búsqueda de pastor en tiempo real
    public $pastorSearch = '';
    public $pastoresBuscados = [];
    public $mostrarResultadosPastor = false;

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
        // Paso 1: Datos básicos
        'nombre' => 'required|string|max:255',
        'direccion' => 'nullable|string|max:500',
        'telefono' => 'nullable|string|max:50',
        'email' => 'nullable|email|max:255',

        // Paso 2: Ubicación geográfica
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

        // Paso 3: Información de la extensión
        'fecha_fundacion' => 'nullable|date',
        'descripcion' => 'nullable|string',
        'activa' => 'boolean',
        'pastor_id' => 'nullable|exists:pastores,id',
        'tipo_local_id' => 'nullable|exists:tipo_locales,id',

        // Paso 4: Estadísticas y medios de comunicación
        'miembros_activos' => 'nullable|integer|min:0',
        'cantidad_campos_blancos' => 'nullable|integer|min:0',
        'miembro_probante' => 'nullable|integer|min:0',
        'logros_obtenidos' => 'nullable|string',
        'tiempo_trabajo' => 'nullable|string|max:100',
        'iglesias_fundadas' => 'nullable|integer|min:0',
        'pastores_ministerio' => 'nullable|integer|min:0',
        'posee_medio_comunicacion' => 'boolean',
        'medios_comunicacion_list' => 'nullable|array',
    ];

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

    public function mount()
    {
        $this->estados = Estado::orderBy('nombre')->get();
        $this->pastores = Pastor::activos()->orderBy('nombres')->orderBy('apellidos')->get();
        $this->tiposLocal = TipoLocal::where('status', true)->orderBy('nombre')->get();
        $this->ciudades = collect();
        $this->municipios = collect();
        $this->parroquias = collect();
    }


    public function nextStep()
    {
        $this->validateStep();
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

    public function updatedEstadoId($value)
    {
        if ($value) {
            $this->ciudades = Ciudad::where('estado_id', $value)->orderBy('nombre')->get();
            $this->dispatch('update-map-location');
        } else {
            $this->ciudades = collect();
        }
        $this->ciudad_id = null;
        $this->municipio_id = null;
        $this->parroquia_id = null;
        $this->municipios = collect();
        $this->parroquias = collect();
    }

    public function updatedFechaFundacion($value)
    {
        // Solo mantener el valor de fecha_fundacion, no actualizar otras propiedades
        // para evitar conflictos de re-renderizado

        $this->aniosActiva = $this->getAniosActivaAttribute();
        //dd($this->fecha_fundacion);
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

    public function updatedCiudadId($value)
    {
        if ($value) {
            $this->municipios = \App\Models\Municipio::where('estado_id', $this->estado_id)->orderBy('nombre')->get();
            $this->dispatch('update-map-location');
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

    public function updatedPastorSearch($value)
    {
        if (strlen($value) >= 2) {
            $this->pastoresBuscados = Pastor::activos()
                ->where(function($query) use ($value) {
                    $query->where('nombres', 'like', '%' . $value . '%')
                          ->orWhere('apellidos', 'like', '%' . $value . '%')
                          ->orWhere('documento', 'like', '%' . $value . '%');
                })
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->limit(10)
                ->get();
            $this->mostrarResultadosPastor = true;
        } else {
            $this->pastoresBuscados = [];
            $this->mostrarResultadosPastor = false;
        }
    }

    public function seleccionarPastor($pastorId)
    {
        $pastor = Pastor::find($pastorId);
        if ($pastor) {
            $this->pastor_id = $pastorId;
            $this->pastorSearch = $pastor->nombres . ' ' . $pastor->apellidos . ' (' . $pastor->documento . ')';
            $this->mostrarResultadosPastor = false;
            $this->updatedPastorId($pastorId);
        }
    }

    public function limpiarBusquedaPastor()
    {
        $this->pastor_id = '';
        $this->pastorSearch = '';
        $this->pastoresBuscados = [];
        $this->mostrarResultadosPastor = false;
        $this->pastorSeleccionado = null;
    }

    #[On('closePastorDropdown')]
    public function closePastorDropdown()
    {
        $this->mostrarResultadosPastor = false;
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

    public function actualizarCoordenadas($lat, $lng, $direccion = null)
    {
        $this->latitud = $lat;
        $this->longitud = $lng;

        // Si se proporciona una dirección, actualizarla también
        if ($direccion) {
            $this->direccion = $direccion;
        }
    }

    public function getCoordenadasEstado()
    {
        if ($this->estado_id) {
            $estado = \App\Models\Estado::find($this->estado_id);
            // Si tuviéramos latitud/longitud en la tabla estados, la usaríamos así:
            // if ($estado && $estado->latitud && $estado->longitud) {
            //     return ['lat' => $estado->latitud, 'lng' => $estado->longitud];
            // }

            // Por ahora, devolvemos null para que el frontend intente usar Nominatim con el nombre del estado
            if ($estado) {
                return ['nombre' => $estado->nombre . ', Venezuela'];
            }
        }
        return null;
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

    #[On('location-updated')]
    public function updateLocation($latitude, $longitude, $address)
    {
        $this->latitud = $latitude;
        $this->longitud = $longitude;
    }

    public function save()
    {
        $this->validate();

        Iglesia::create([
            // Paso 1: Datos básicos
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'email' => $this->email,

            // Paso 2: Ubicación geográfica
            'estado_id' => $this->estado_id,
            'ciudad_id' => $this->ciudad_id,
            'municipio_id' => $this->municipio_id,
            'parroquia_id' => $this->parroquia_id,
            'tipo_local_id' => $this->tipo_local_id,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            'zona' => $this->zona,
            'distrito' => $this->distrito,
            'sector' => $this->sector,
            'calle' => $this->calle,
            'avenida' => $this->avenida,

            // Paso 3: Información de la extensión
            'fecha_fundacion' => $this->fecha_fundacion,
            'descripcion' => $this->descripcion,
            'activa' => $this->activa,
            'pastor_id' => $this->pastor_id,
            'empresa_id' => 1,
            'sucursal_id' => 1,

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

            'usuario_registro_id' => auth()->id(),
        ]);

        session()->flash('message', 'Extensión creada correctamente.');

        return redirect()->route('admin.iglesias.index');
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.iglesias.create', [
            'estados' => $this->estados,
            'ciudades' => $this->ciudades,
            'municipios' => $this->municipios,
            'parroquias' => $this->parroquias,
            'pastores' => $this->pastores,
            'tiposLocal' => $this->tiposLocal,
        ], [
            'title' => 'Crear Extensión',
            'description' => 'Registra una nueva extensión en el sistema'
        ]);
    }
}
