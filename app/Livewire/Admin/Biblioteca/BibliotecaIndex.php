<?php

namespace App\Livewire\Admin\Biblioteca;

use App\Models\BibliotecaArchivo;
use App\Models\BibliotecaCategoria;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BibliotecaIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $categoriaSeleccionada = null;
    public $vistaActiva = 'grid';
    public $mostrarFormulario = false;
    public $filtroActivo = 'todos';
    
    // Propiedades para nuevo archivo
    public $nuevoArchivo;
    public $titulo = '';
    public $descripcion = '';
    public $categoriaId = '';
    public $visibilidad = 'privado';
    public $usuariosAutorizados = [];
    public $etiquetas = '';

    protected $rules = [
        'nuevoArchivo' => 'required|file|max:10240', // 10MB máximo
        'titulo' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'categoriaId' => 'nullable|exists:biblioteca_categorias,id',
        'visibilidad' => 'required|in:publico,privado,restringido',
        'usuariosAutorizados' => 'required_if:visibilidad,restringido|array',
        'usuariosAutorizados.*' => 'exists:users,id',
        'etiquetas' => 'nullable|string',
    ];

    public function mount()
    {
        $this->resetearFormulario();
    }

    public function resetearFormulario()
    {
        $this->nuevoArchivo = null;
        $this->titulo = '';
        $this->descripcion = '';
        $this->categoriaId = '';
        $this->visibilidad = 'privado';
        $this->usuariosAutorizados = [];
        $this->etiquetas = '';
    }

    public function abrirFormulario()
    {
        $this->mostrarFormulario = true;
        $this->resetearFormulario();
    }

    public function cerrarFormulario()
    {
        $this->mostrarFormulario = false;
        $this->resetearFormulario();
    }

    public function subirArchivo()
    {
        $this->validate();

        // Subir archivo
        $path = $this->nuevoArchivo->store('biblioteca/' . auth()->user()->empresa_id . '/' . auth()->user()->sucursal_id, 'public');
        
        // Crear registro
        $archivo = BibliotecaArchivo::create([
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'nombre_archivo' => $this->nuevoArchivo->getClientOriginalName(),
            'ruta_archivo' => $path,
            'tamaño' => $this->nuevoArchivo->getSize(),
            'tipo_mime' => $this->nuevoArchivo->getMimeType(),
            'categoria_id' => $this->categoriaId ?: null,
            'usuario_subida_id' => auth()->id(),
            'visibilidad' => $this->visibilidad,
            'empresa_id' => auth()->user()->empresa_id,
            'sucursal_id' => auth()->user()->sucursal_id,
            'etiquetas' => $this->etiquetas ? explode(',', $this->etiquetas) : null,
        ]);

        // Adjuntar usuarios autorizados si es restringido
        if ($this->visibilidad === 'restringido' && !empty($this->usuariosAutorizados)) {
            $archivo->usuariosAutorizados()->attach($this->usuariosAutorizados);
        }

        $this->cerrarFormulario();
        session()->flash('message', 'Archivo subido exitosamente.');
    }

    public function descargarArchivo($archivoId)
    {
        $archivo = BibliotecaArchivo::findOrFail($archivoId);
        
        // Verificar permisos
        if (!$this->puedeAccederArchivo($archivo)) {
            session()->flash('error', 'No tienes permisos para descargar este archivo.');
            return;
        }

        // Registrar descarga
        $archivo->incrementarDescargas();
        \App\Models\BibliotecaDescarga::create([
            'archivo_id' => $archivo->id,
            'usuario_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'descargado_en' => now(),
        ]);

        return response()->download(storage_path('app/public/' . $archivo->ruta_archivo), $archivo->nombre_archivo);
    }

    public function eliminarArchivo($archivoId)
    {
        $archivo = BibliotecaArchivo::findOrFail($archivoId);
        
        // Verificar permisos
        if (auth()->id() !== $archivo->usuario_subida_id && !auth()->user()->can('delete biblioteca')) {
            session()->flash('error', 'No tienes permisos para eliminar este archivo.');
            return;
        }

        // Eliminar archivo físico
        Storage::disk('public')->delete($archivo->ruta_archivo);
        
        // Eliminar registro
        $archivo->delete();

        session()->flash('message', 'Archivo eliminado exitosamente.');
    }

    public function puedeAccederArchivo($archivo): bool
    {
        // Super administrador puede acceder a todo
        if (auth()->user()->hasRole('Super Administrador')) {
            return true;
        }

        // El propietario puede acceder
        if (auth()->id() === $archivo->usuario_subida_id) {
            return true;
        }

        // Verificar visibilidad
        switch ($archivo->visibilidad) {
            case 'publico':
                return true;
            case 'privado':
                return false;
            case 'restringido':
                return $archivo->usuariosAutorizados()->where('user_id', auth()->id())->exists();
        }

        return false;
    }

    public function filtrar($tipo)
    {
        $this->filtroActivo = $tipo;
        $this->categoriaSeleccionada = null;
    }

    public function filtrarCategoria($categoriaId)
    {
        $this->categoriaSeleccionada = $categoriaId;
        $this->filtroActivo = 'categoria';
    }

    public function cambiarVista($vista)
    {
        $this->vistaActiva = $vista;
    }

    public function getArchivosProperty()
    {
        $query = BibliotecaArchivo::visiblePara(auth()->user())
            ->forUser(auth()->user())
            ->with(['categoria', 'usuarioSubida'])
            ->when($this->filtroActivo === 'mis-archivos', function ($q) {
                $q->where('usuario_subida_id', auth()->id());
            })
            ->when($this->filtroActivo === 'compartidos', function ($q) {
                $q->whereHas('usuariosAutorizados', function($sq) {
                    $sq->where('user_id', auth()->id());
                });
            })
            ->when($this->filtroActivo === 'recientes', function ($q) {
                $q->where('created_at', '>=', now()->subDays(7));
            })
            ->when($this->categoriaSeleccionada, function ($q) {
                $q->porCategoria($this->categoriaSeleccionada);
            })
            ->when($this->search, function ($q) {
                $q->busqueda($this->search);
            });

        return $query->orderBy('created_at', 'desc')
            ->paginate(12);
    }

    public function getTotalArchivosProperty()
    {
        return BibliotecaArchivo::visiblePara(auth()->user())
            ->forUser(auth()->user())
            ->count();
    }

    public function getCategoriasProperty()
    {
        return BibliotecaCategoria::activas()
            ->forUser(auth()->user())
            ->withCount('archivos')
            ->get();
    }

    public function getUsuariosProperty()
    {
        return \App\Models\User::where('id', '!=', auth()->id())
            ->forUser()
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.biblioteca.biblioteca-index')
            ->layout('components.layouts.admin', [
                'title' => 'Biblioteca Digital',
                'description' => 'Gestión de archivos y documentos'
            ]);
    }
}