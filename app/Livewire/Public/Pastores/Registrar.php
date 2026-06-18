<?php

namespace App\Livewire\Public\Pastores;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Models\Pastor;
use App\Models\Ciudad;
use App\Models\Estado;
use App\Models\Parroquia;
use App\Models\Municipio;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Log;

class Registrar extends Component
{
    use HasDynamicLayout, WithFileUploads;

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
    public $foto_temporal;
    public $nota = '';
    public $status = true;
    public $empresa_id = null;
    public $sucursal_id = null;

    // Listas desplegables
    public $estados = [];
    public $ciudades = [];
    public $parroquias = [];
    public $municipios = [];
    public $pastores = [];
    public $empresas = [];
    public $sucursales = [];

    protected $rules = [
        // Paso 1: Datos personales
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
        'latitud' => 'nullable|numeric|between:-90,90',
        'longitud' => 'nullable|numeric|between:-180,180',

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
            'foto_temporal' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif',
            'nota' => 'nullable|string',
            'status' => 'boolean',
            //'empresa_id' => 'nullable|exists:empresas,id',
            //'sucursal_id' => 'nullable|exists:sucursales,id',
    ];

    public function mount()
    {
        $this->documento = request()->query('cedula', '');
        $this->status = true; // Activo por defecto

        // El código se generará automáticamente al guardar con formato: ID-Últimos4Cédula
        $this->codigo = 'Se generará al guardar';

        // Cargar listas desplegables
        $this->estados = Estado::orderBy('nombre')->get();
        $this->ciudades = collect();
        $this->municipios = collect();
        $this->parroquias = collect();

        // Cargar empresas y sucursales
        $this->empresas = Empresa::where('status', true)->orderBy('razon_social')->get();
        
        // Cargar empresas y sucursales
        $this->empresas = Empresa::where('status', true)->orderBy('razon_social')->get();
        
        // Si no hay empresa_id seleccionada, establecer la primera como predeterminada
        if($this->empresa_id === null && $this->empresas->count() > 0) {
            $this->empresa_id = $this->empresas->first()->id;
        }
        
        $this->sucursales = collect();
        
        // Cargar y establecer sucursal_id solo si hay empresa_id
        if($this->empresa_id) {
            $this->sucursales = Sucursal::where('empresa_id', $this->empresa_id)->where('status', true)->orderBy('nombre')->get();
            if($this->sucursales->count() > 0 && $this->sucursal_id === null) {
                $this->sucursal_id = $this->sucursales->first()->id;
            }
        }
    
        // Inicializar searchResults
        $this->searchResults = [];
    }

    public function updatedEstadoId($value)
    {
        if ($value) {
            $this->ciudades = Ciudad::where('estado_id', $value)->orderBy('nombre')->get();
            $this->municipios = Municipio::where('estado_id', $value)->get(); // Cargar municipios
        } else {
            $this->ciudades = collect();
            $this->municipios = collect(); // Limpiar municipios
        }

        $this->ciudad_id = null;
        $this->municipio_id = null; // Limpiar municipio_id
        $this->parroquia_id = null;
        $this->parroquias = collect();
    }
    public function updatedGenero($value)
    {
          // Cargar pastores disponibles (excluir los que ya tienen cónyuge)
       if ($value == 'Femenino') {
         $this->pastores = Pastor::whereNull('conyuge_id')
            ->where('genero', 'Masculino')
            ->when($this->zona, function ($query, $zona) {
                return $query->where('zona', $zona);
            })
            ->when($this->distrito, function ($query, $distrito) {
                return $query->where('distrito', $distrito);
            })
            ->where('id', '!=', $this->pastor?->id)  // No se puede seleccionar a sí misma si está editando
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();
       } else {
           $this->pastores = Pastor::whereNull('conyuge_id')
           ->where('genero', 'Femenino')
           ->when($this->zona, function ($query, $zona) {
               return $query->where('zona', $zona);
           })
           ->when($this->distrito, function ($query, $distrito) {
               return $query->where('distrito', $distrito);
           })
           ->where('id', '!=', $this->pastor?->id)  // No se puede seleccionar a sí mismo si está editando
           ->orderBy('nombres')
           ->orderBy('apellidos')
           ->get();
       }
    }

    public function updatedEmpresaId($value)
    {
        if ($value) {
            $this->sucursales = Sucursal::where('empresa_id', $value)->where('status', true)->orderBy('nombre')->get();
            if($this->sucursales->count() > 0) {
                $this->sucursal_id = $this->sucursales->first()->id;
            } else {
                $this->sucursal_id = null; // Si no hay sucursales válidas, dejar como null
            }
        } else {
            $this->sucursales = collect();
            $this->sucursal_id = null;
        }
    }

    public function updatedCiudadId($value)
    {
        // No cargar parroquias por ciudad_id, ya que la relación es con municipio_id
        // Solo limpiar si es necesario
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

    public function updatedConyugeId($value)
    {
        // Recargar la lista de pastores disponibles cuando se cambia el cónyuge
        $this->pastores = Pastor::where(function($query) use ($value) {
                $query->whereNull('conyuge_id')
                      ->where('id', '!=', $value); // Excluir el seleccionado

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


    }

    public function updatedZona($value)
    {
        // Recargar la lista de pastores disponibles cuando se cambia la zona
        if ($this->genero == 'Femenino') {
            $this->pastores = Pastor::whereNull('conyuge_id')
                ->where('genero', 'Masculino')
                ->when($value, function ($query, $zona) {
                    return $query->where('zona', $zona);
                })
                ->when($this->distrito, function ($query, $distrito) {
                    return $query->where('distrito', $distrito);
                })
                ->where('id', '!=', $this->pastor?->id)  // No se puede seleccionar a sí misma si está editando
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();
        } else {
            $this->pastores = Pastor::whereNull('conyuge_id')
                ->where('genero', 'Femenino')
                ->when($value, function ($query, $zona) {
                    return $query->where('zona', $zona);
                })
                ->when($this->distrito, function ($query, $distrito) {
                    return $query->where('distrito', $distrito);
                })
                ->where('id', '!=', $this->pastor?->id)  // No se puede seleccionar a sí mismo si está editando
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();
        }
    }

    public function updatedDistrito($value)
    {
        // Recargar la lista de pastores disponibles cuando se cambia el distrito
        if ($this->genero == 'Femenino') {
            $this->pastores = Pastor::whereNull('conyuge_id')
                ->where('genero', 'Masculino')
                ->when($this->zona, function ($query, $zona) {
                    return $query->where('zona', $zona);
                })
                ->when($value, function ($query, $distrito) {
                    return $query->where('distrito', $distrito);
                })
                ->where('id', '!=', $this->pastor?->id)  // No se puede seleccionar a sí misma si está editando
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();
        } else {
            $this->pastores = Pastor::whereNull('conyuge_id')
                ->where('genero', 'Femenino')
                ->when($this->zona, function ($query, $zona) {
                    return $query->where('zona', $zona);
                })
                ->when($value, function ($query, $distrito) {
                    return $query->where('distrito', $distrito);
                })
                ->where('id', '!=', $this->pastor?->id)  // No se puede seleccionar a sí mismo si está editando
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get();
        }
        
        // Actualizar los resultados de búsqueda cuando cambie el distrito
        $this->searchConyuge();
    }
    
    /**
     * Busca cónyuges potenciales basándose en los criterios dados
     */
    public function searchConyuge($searchTerm = '')
    {
        $query = Pastor::whereNull('conyuge_id');
    
        // Filtrar por género opuesto al actual
        if ($this->genero === 'Femenino') {
            $query->where('genero', 'Masculino');
        } else {
            $query->where('genero', 'Femenino');
        }
    
        // Filtrar por zona y distrito si están definidos
        if ($this->zona) {
            $query->where('zona', $this->zona);
        }
        if ($this->distrito) {
            $query->where('distrito', $this->distrito);
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
                $this->validate([
                    'nombres' => 'required|string|max:255',
                    'apellidos' => 'required|string|max:255',
                    'documento' => 'required|string|max:50|unique:pastores,documento',
                    'fe_nacimiento' => 'nullable|date',
                    'edad' => 'nullable|integer|min:0',
                    'genero' => 'nullable|string|max:50',
                    'estado_civil' => 'nullable|string|max:50',
                    'telefono_hab' => 'nullable|string|max:50',
                    'telefono_tlf' => 'nullable|string|max:50',
                    'email' => 'nullable|email|max:255',
                    'conyuge_id' => 'nullable|exists:pastores,id',
                ]);
                break;
            case 2:
                $this->validate([
                    'municipio_id' => 'nullable|exists:municipios,id',
                    'estado_id' => 'nullable|exists:estados,id',
                    'ciudad_id' => 'nullable|exists:ciudades,id',
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
        $this->validate([
            'conyuge_nombres' => 'required|string|max:255',
            'conyuge_apellidos' => 'required|string|max:255',
            'conyuge_documento' => 'required|string|max:50|unique:pastores,documento',
            'conyuge_genero' => 'nullable|string|max:50',
        ]);

        // Crear el cónyuge con código temporal
        $conyuge = Pastor::create([
            'codigo' => 'TEMP',
            'nombres' => $this->conyuge_nombres,
            'apellidos' => $this->conyuge_apellidos,
            'documento' => $this->conyuge_documento,
            'genero' => $this->conyuge_genero,
            'estado_civil' => 'Casado',
            'status' => true,
            'pertenece_ministerio' => false,
        ]);

        // Generar código con formato: ID-Últimos4Cédula (ej: 005-4421)
        $codigo = Pastor::generarCodigoPastor($conyuge->id, $conyuge->documento);
        $conyuge->update(['codigo' => $codigo]);

        // Actualizar la lista de pastores y seleccionar el nuevo cónyuge
        $this->pastores = Pastor::whereNull('conyuge_id')
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();
        $this->conyuge_id = $conyuge->id;
        $this->conyuge_es_pastor = true;

        // Si el pastor actual ya existe y está siendo editado, actualizar su estado civil
        if ($this->estado_civil === 'Casado') {
            // Asegurar que el estado civil sea "Casado"
            $this->estado_civil = 'Casado';
        }

        // Cerrar la modal
        $this->showConyugeModal = false;
        $this->dispatch('close-conyuge-modal');

        // Resetear datos de la modal
        $this->resetConyugeData();

        session()->flash('message', 'Cónyuge registrado correctamente.');
    }

    public function updatedFotoTemporal()
    {
        $this->validate([
            'foto_temporal' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif',
        ]);

        try {
            // Crear instancia del ImageManager con driver GD
            $manager = new ImageManager(new Driver());

            // Procesar la imagen tipo carnet (cuadrado)
            $image = $manager->read($this->foto_temporal->getRealPath());

            // Recortar a cuadrado centrado y redimensionar a 300x300 para carnet
            $image->cover(300, 300);

            // Generar nombre único para la imagen
            $filename = 'pastor_' . time() . '_' . uniqid() . '.jpg';
            $path = 'pastores/' . $filename;

            // Guardar la imagen procesada
            Storage::disk('public')->put($path, $image->encodeByExtension('jpg', quality: 85));

            // Actualizar la propiedad foto con la ruta
            $this->foto = $path;

            session()->flash('message', 'Foto procesada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar la imagen: ' . $e->getMessage());
            $this->foto_temporal = null;
        }
    }

    public function removeFoto()
    {
        if ($this->foto) {
            // Eliminar la imagen anterior si existe
            Storage::disk('public')->delete($this->foto);
            $this->foto = '';
            $this->foto_temporal = null;
            session()->flash('message', 'Foto eliminada correctamente.');
        }
    }

    public function save()
    {
        // Validar todos los pasos antes de guardar
        //dd( $this->validate());
        $this->validate();
        $pastor = Pastor::create([
            // Paso 1: Datos personales
            'codigo' => 'TEMP',
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
            'user_id' => 1, // Usuario administrador por defecto
            'empresa_id' => $this->empresa_id, // Usar el valor verificado
            'sucursal_id' => $this->sucursal_id, // Usar el valor verificado
        ]);

        // Generar código con formato: ID-Últimos4Cédula (ej: 001-2293)
        $codigo = Pastor::generarCodigoPastor($pastor->id, $pastor->documento);
        $pastor->update(['codigo' => $codigo]);

        // Si se ha seleccionado un cónyuge, actualizar también esa relación
        if ($this->estado_civil === 'Casado' && $this->conyuge_id) {
            $conyuge = Pastor::find($this->conyuge_id);
            if ($conyuge) {
                // Asegurar que ambos pastores estén "Casados" y se conozcan mutuamente
                $conyuge->update([
                    'conyuge_id' => $pastor->id,
                    'estado_civil' => 'Casado'
                ]);
            }
        }

        // Limpiar la imagen temporal
        $this->foto_temporal = null;

        // Lógica para redirigir a la extensión o a la creación de extensión
        try {
            Log::info("Buscando extensiones para el nuevo pastor ID: {$pastor->id}");

            // Verificamos si es una esposa (cónyuge de alguien) y si ese alguien tiene extensiones
            $iglesia = null;
            if ($pastor->esConyuge() && $pastor->pastorPrincipal) {
                Log::info("El usuario recién registrado es cónyuge del pastor ID: {$pastor->pastorPrincipal->id}. Buscando extensiones del esposo.");
                $iglesia = $pastor->pastorPrincipal->iglesias()->first();
                if ($iglesia) {
                    Log::info("Se encontró la extensión del esposo (Extensión ID: {$iglesia->id}). Se omitirá la creación de extensión propia.");
                }
            }

            if ($iglesia) {
                // Si la extensión encontrada pertenece al esposo, redirigimos al buscador
                if ($iglesia->pastor_id !== $pastor->id) {
                     Log::info("La extensión pertenece al esposo. Redirigiendo a la pantalla de éxito.");
                     session()->flash('message', '¡Registro completado exitosamente! Al ser cónyuge, ha sido asociada automáticamente a la extensión de su esposo.');
                     return redirect()->route('public.completado', ['pastor' => $pastor->id]);
                } else {
                     session()->flash('message', '¡Registro completado exitosamente!');
                     return redirect()->route('public.completado', ['pastor' => $pastor->id]);
                }
            } else {
                Log::info("No se encontraron iglesias. Redirigiendo a creación de iglesia.");
                session()->flash('message', '¡Registro completado exitosamente! Por favor, registre los datos de su iglesia.');
                return redirect()->route('public.iglesias.crear', ['pastor' => $pastor->id]);
            }
        } catch (\Exception $e) {
            Log::error("Error al procesar la redirección a la extensión para el pastor ID: {$pastor->id}. Error: " . $e->getMessage());
            session()->flash('message', '¡Registro completado exitosamente! Sin embargo, hubo un problema al procesar la información de su iglesia.');
            return redirect()->route('public.completado', ['pastor' => $pastor->id]);
        }
    }

    public function render()
    {
        return view('livewire.public.pastores.registrar', [
            'estados' => $this->estados,
            'ciudades' => $this->ciudades,
            'parroquias' => $this->parroquias,
            'municipios' => $this->municipios,
            'pastores' => $this->pastores,
            'empresas' => $this->empresas,
            'sucursales' => $this->sucursales,
        ])->layout('components.layouts.auth-basic', ['title' => 'Registro de Pastor']);
    }
}