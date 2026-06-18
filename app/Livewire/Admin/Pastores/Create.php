<?php

namespace App\Livewire\Admin\Pastores;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Models\Pastor;
use App\Models\Ciudad;
use App\Models\Estado;
use App\Models\Parroquia;
use App\Models\Municipio;
use Illuminate\Support\Facades\Schema;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class Create extends Component
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
    public $empresa_id = '';
    public $sucursal_id = '';

    // Listas desplegables
    public $estados = [];
    public $ciudades = [];
    public $parroquias = [];
    public $municipios = [];
    public $pastores = [];
    public $empresas = [];
    public $sucursales = [];
    public $searchConyuge = '';
    public $viewPastor = null;

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

        // Datos del cónyuge
        'conyuge_nombres' => 'required|string|max:255',
        'conyuge_apellidos' => 'required|string|max:255',
        'conyuge_documento' => 'required|string|max:50|unique:pastores,documento',
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
        // El código se generará automáticamente al guardar con formato: ID-Últimos4Cédula
        $this->codigo = 'Se generará al guardar';

        // Cargar listas desplegables
        $this->estados = Estado::orderBy('nombre')->get();
        $this->ciudades = collect();
        $this->municipios = collect();
        $this->parroquias = collect();

        // Cargar pastores disponibles para selección de cónyuge
        $this->pastores = $this->loadPastoresDisponibles();

        // Cargar empresas y sucursales
        $this->empresas = Empresa::where('status', true)->orderBy('razon_social')->get();
        $this->sucursales = collect();
    }

    public function updatedEstadoId($value)
    {
        if ($value) {
            // Cargar municipios para el estado
            $this->municipios = Municipio::where('estado_id', $value)->orderBy('nombre')->get();

            // Cargar ciudades: preferir ciudades vinculadas a municipio (si existe columna municipio_id)
            if (Schema::hasColumn('ciudades', 'municipio_id')) {
                // Dejamos ciudades vacías hasta que se seleccione un municipio
                $this->ciudades = collect();
            } else {
                // Compatibilidad: cargar ciudades por estado si la tabla ciudades aún usa estado_id
                $this->ciudades = Ciudad::where('estado_id', $value)->orderBy('nombre')->get();
            }
        } else {
            $this->ciudades = collect();
            $this->municipios = collect(); // Limpiar municipios
        }

        $this->ciudad_id = null;
        $this->municipio_id = null; // Limpiar municipio_id
        $this->parroquia_id = null;
        $this->parroquias = collect();
    }

    public function updatedSearchConyuge($value)
    {
        $this->pastores = $this->loadPastoresDisponibles();
    }

    public function updatedGenero($value)
    {
        $this->pastores = $this->loadPastoresDisponibles();
    }

    public function updatedEstadoCivil($value)
    {
        if ($value !== 'Casado') {
            $this->conyuge_id = null;
            $this->searchConyuge = '';
        }

        $this->pastores = $this->loadPastoresDisponibles();
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

    public function updatedCiudadId($value)
    {
        // No cargar parroquias por ciudad_id, ya que la relación es con municipio_id
        // Solo limpiar si es necesario
        $this->parroquia_id = null;
    }

    public function updatedMunicipioId($value)
    {
        if ($value) {
            // Cargar parroquias por municipio
            $this->parroquias = Parroquia::where('municipio_id', $value)->orderBy('nombre')->get();

            // Cargar ciudades relacionadas al municipio si existe la columna municipio_id
            if (Schema::hasColumn('ciudades', 'municipio_id')) {
                $this->ciudades = Ciudad::where('municipio_id', $value)->orderBy('nombre')->get();
            }
        } else {
            $this->parroquias = collect();
            // Si la tabla ciudades no usa municipio_id, mantenerla vacía para evitar confusiones
            if (Schema::hasColumn('ciudades', 'municipio_id')) {
                $this->ciudades = collect();
            }
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
        $this->pastores = $this->loadPastoresDisponibles();
    }

    public function selectConyuge($id)
    {
        $this->conyuge_id = $id;
        // Optionally set the search to the selected pastor's name
        $pastor = Pastor::find($id);
        if ($pastor) {
            $this->searchConyuge = $pastor->nombres . ' ' . $pastor->apellidos;
        }
        $this->pastores = $this->loadPastoresDisponibles();
    }

    public function showPastorProfile($id)
    {
        $this->viewPastor = Pastor::find($id);
        $this->dispatch('open-view-pastor-modal');
    }

    public function closeViewPastor()
    {
        $this->viewPastor = null;
        $this->dispatch('close-view-pastor-modal');
    }

    private function loadPastoresDisponibles()
    {
        if ($this->estado_civil !== 'Casado') {
            return collect();
        }
        // If no gender selected AND no search provided, don't return candidates
        $search = trim($this->searchConyuge);
        if (!in_array($this->genero, ['Masculino', 'Femenino']) && $search === '') {
            return collect();
        }

        $query = Pastor::query()
            ->where(function ($query) {
                $query->whereNull('conyuge_id');
                if ($this->conyuge_id) {
                    $query->orWhere('id', $this->conyuge_id);
                }
            });

        // Apply gender filter only when genero is set; otherwise search across both genders
        if ($this->genero === 'Masculino') {
            $query->where('genero', 'Femenino');
        } elseif ($this->genero === 'Femenino') {
            $query->where('genero', 'Masculino');
        }

        if ($search !== '') {
            $like = "%{$search}%";
            $query->where(function ($q) use ($like) {
                $q->where('nombres', 'like', $like)
                  ->orWhere('apellidos', 'like', $like)
                  ->orWhere('documento', 'like', $like);
            });
        }

        return $query->orderBy('nombres')->orderBy('apellidos')->get();
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
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,

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
            'user_id' => auth()->id(),
            'empresa_id' => auth()->user()->empresa_id,
            'sucursal_id' => auth()->user()->sucursal_id,
        ]);

        // Generar código con formato: ID-Últimos4Cédula (ej: 001-2293)
        $codigo = Pastor::generarCodigoPastor($pastor->id, $pastor->documento);
        $pastor->update(['codigo' => $codigo]);

        // Si se ha seleccionado un cónyuge, actualizar también esa relación (asegurar mutualidad)
        if ($this->estado_civil === 'Casado' && $this->conyuge_id) {
            $conyuge = Pastor::find($this->conyuge_id);
            if ($conyuge) {
                // Vincular ambos lados: el nuevo pastor y el cónyuge
                $conyuge->update([
                    'conyuge_id' => $pastor->id,
                    'estado_civil' => 'Casado'
                ]);

                // Asegurar que el pastor creado tenga estado_civil y conyuge_id correctos
                if ($pastor->conyuge_id != $conyuge->id || $pastor->estado_civil !== 'Casado') {
                    $pastor->update([
                        'conyuge_id' => $conyuge->id,
                        'estado_civil' => 'Casado'
                    ]);
                }
            }
        }

        session()->flash('message', 'Pastor creado correctamente.');

        // Limpiar la imagen temporal
        $this->foto_temporal = null;

        return redirect()->route('admin.pastores.index');
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.pastores.create', [
            'estados' => $this->estados,
            'ciudades' => $this->ciudades,
            'parroquias' => $this->parroquias,
            'municipios' => $this->municipios,
            'pastores' => $this->pastores,
            'empresas' => $this->empresas,
            'sucursales' => $this->sucursales,
        ], [
            'title' => 'Nuevo Pastor',
            'description' => 'Registrar un nuevo pastor en el sistema'
        ]);
    }
}