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
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Create extends Component
{
    use HasDynamicLayout;

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
    public $casas_alimentacion = [];
    public $casas = [];

    public $empresa_id = '';
    public $sucursal_id = '';

    public $estados = [];
    public $municipios = [];
    public $parroquias = [];
    public $empresas = [];
    public $sucursales = [];

    // Campos para la creación automática de usuario
    public $create_user = false;
    public $username;
    public $email;
    public $user_password = '12345678';
    public $user_role = 'Administrador';
    public $user_status = true;

    protected $rules = [
        'nombre_completo' => 'required|string|max:255',
        'cedula' => 'required|string|max:20|unique:responsables,cedula',
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
        // Reglas para la creación de usuario
        'create_user' => 'boolean',
        'username' => 'nullable|string|max:255|unique:users',
        'email' => 'nullable|email|max:255|unique:users',
        'user_password' => 'nullable|string|min:8',
        'user_role' => 'nullable|exists:roles,name',
    ];

    public function mount()
    {
        $this->estados = Estado::all();
        $this->casas_alimentacion = \App\Models\CasaAlimentacion::orderBy('codigo')->get();


    }

    public function updatedEstadoId($value)
    {
        $this->municipio_id = '';
        $this->parroquia_id = '';
        $this->empresa_id = '';
        $this->sucursal_id = '';
        $this->codigo_casa_alimentacion = '';

        if ($value) {
            $this->municipios = Municipio::where('estado_id', $value)->get();
        } else {
            $this->municipios = [];
        }

        $this->parroquias = [];
        $this->empresas = [];
        $this->sucursales = [];

        $this->cargarCasas();
    }

    public function updatedMunicipioId($value)
    {
        $this->parroquia_id = '';
        $this->empresa_id = '';
        $this->sucursal_id = '';
        $this->codigo_casa_alimentacion = '';

        if ($value) {
            $this->parroquias = Parroquia::where('municipio_id', $value)->get();
        } else {
            $this->parroquias = [];
        }

        $this->empresas = [];
        $this->sucursales = [];

        $this->cargarCasas();
    }

    public function updatedParroquiaId($value)
    {
        $this->empresa_id = '';
        $this->sucursal_id = '';
        $this->codigo_casa_alimentacion = '';

        if ($value) {
            // Cargar empresas que tienen sucursales en la parroquia seleccionada
            $this->empresas = Empresa::whereHas('sucursales', function($query) use ($value) {
                $query->where('parroquia_id', $value);
            })->get();
        } else {
            $this->empresas = [];
        }

        $this->sucursales = [];

        $this->cargarCasas();
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

    public function updatedNombreCompleto()
    {
        if ($this->create_user) {
            $this->generateUsernameFromName();
            $this->generateEmailFromName();
        }
    }

    /**
     * Generar username automáticamente a partir del nombre del responsable
     * Formato: primera letra del primer nombre + primer apellido
     */
    public function generateUsernameFromName()
    {
        if (empty($this->nombre_completo)) {
            return;
        }

        // Limpiar el nombre: eliminar acentos y convertir a minúsculas
        $name = strtolower($this->nombre_completo);
        $name = $this->removeAccents($name);

        // Dividir el nombre en palabras
        $words = explode(' ', trim($name));

        if (count($words) < 2) {
            $this->username = Str::slug($this->nombre_completo);
            return;
        }

        // Obtener la primera letra del primer nombre
        $firstInitial = substr($words[0], 0, 1);

        // Obtener el primer apellido (última palabra)
        $lastName = end($words);

        // Generar el username base
        $baseUsername = $firstInitial . $lastName;

        // Verificar si el username base existe
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            // Si existe y hay segundo nombre, agregar su inicial
            if (count($words) > 2 && $counter === 1) {
                $secondInitial = substr($words[1], 0, 1);
                $username = $firstInitial . $secondInitial . $lastName;
            } else {
                // Si aún existe, agregar número incremental
                $username = $baseUsername . $counter;
            }
            $counter++;

            // Prevenir bucle infinito
            if ($counter > 10) {
                $username = $baseUsername . '_' . Str::random(4);
                break;
            }
        }

        $this->username = $username;
    }

    /**
     * Generar email automáticamente a partir del nombre del responsable
     * Formato: username@proal.gob.ve
     */
    public function generateEmailFromName()
    {
        if (empty($this->username)) {
            return;
        }

        $this->email = $this->username . '@proal.gob.ve';
    }

    /**
     * Eliminar acentos de una cadena
     */
    private function removeAccents($string)
    {
        $search = ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü'];
        $replace = ['a', 'e', 'i', 'o', 'u', 'n', 'u', 'A', 'E', 'I', 'O', 'U', 'N', 'U'];

        return str_replace($search, $replace, $string);
    }

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

    public function save()
    {
        $this->validate();

        try {
            $casaAlimentacionId = null;

            if (!empty($this->codigo_casa_alimentacion)) {
                $casa = CasaAlimentacion::where('codigo', $this->codigo_casa_alimentacion)->first();
                $casaAlimentacionId = $casa?->id;
            }

            $responsable = Responsable::create([
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

        // Crear usuario automáticamente si se ha marcado la casilla
        if ($this->create_user) {
            $this->createUserForResponsable($responsable);
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Responsable '{$this->nombre_completo}' creado exitosamente." . ($this->create_user ? " Usuario asociado creado." : ""),
            'duration' => 4000
        ]);

        return redirect()->route('admin.responsables.index');
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        $this->dispatch('notify', [
            'type' => 'error',
            'message' => "Error al crear el responsable '{$this->nombre_completo}'.",
            'duration' => 4000
        ]);
        }
    }


    /**
     * Crear un usuario asociado al responsable
     */
    private function createUserForResponsable($responsable)
    {

        $this->generateUsername();

        $user = new User();
        $user->name = $this->nombre_completo;
        $user->username = $this->username;
        $user->email = $this->username.'@proal.gob.ve';
        $user->responsable_id = $responsable->id;
        $user->password = Hash::make($this->cedula);
        $user->empresa_id = 1;
        $user->sucursal_id = 1;
        $user->status = $this->user_status;
        $user->phone = $this->telefono;
        $user->email_verified_at = now();
        $user->save();

        // Asignar rol al usuario
        if ($this->user_role) {
            $role = Role::where('name', 'Responsable')->first();
            if ($role) {
                $user->assignRole($role);
            }
        }
    }


      /**
     * Generar username automáticamente a partir del nombre
     * Formato: primera letra del primer nombre + primer apellido
     * Si existe, agregar inicial del segundo nombre
     */
    public function generateUsername()
    {
        if (empty($this->nombre_completo)) {
            return;
        }

        // Limpiar el nombre: eliminar acentos y convertir a minúsculas
        $name = strtolower($this->nombre_completo);
        $name = $this->removeAccents($name);

        // Dividir el nombre en palabras
        $words = explode(' ', trim($name));

        if (count($words) < 2) {
            return;
        }

        // Obtener la primera letra del primer nombre
        $firstInitial = substr($words[0], 0, 1);

        // Obtener el primer apellido (última palabra)
        $lastName = end($words);

        // Generar el username base
        $baseUsername = $firstInitial . $lastName;

        // Verificar si el username base existe
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            // Si existe y hay segundo nombre, agregar su inicial
            if (count($words) > 2 && $counter === 1) {
                $secondInitial = substr($words[1], 0, 1);
                $username = $firstInitial . $secondInitial . $lastName;
            } else {
                // Si aún existe, agregar número incremental
                $username = $baseUsername . $counter;
            }
            $counter++;

            // Prevenir bucle infinito
            if ($counter > 10) {
                break;
            }
        }

        $this->username = $username;
    }

    public function render()
    {
        $roles = Role::all();
        return $this->renderWithLayout('livewire.admin.responsables.create', [
            'estados' => $this->estados,
            'municipios' => $this->municipios,
            'parroquias' => $this->parroquias,
            'empresas' => $this->empresas,
            'sucursales' => $this->sucursales,
            'casas' => $this->casas,
            'roles' => $roles,
        ], [
            'title' => 'Crear Responsable',
            'description' => 'Registrar nuevo responsable'
        ]);
    }
}
