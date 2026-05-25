<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Pastor;
use App\Models\Iglesia;
use App\Models\TipoLocal;
use App\Models\Estado;
use App\Traits\HasDynamicLayout;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    use HasDynamicLayout;

    public $totalPastores;
    public $totalPastoresReconocidos;
    public $totalColaboradores;
    public $totalLicenciados;
    public $totalMinistroOrdenado;
    public $totalLaico;
    public $pastoresActivos;
    public $totalIglesias;
    public $iglesiasActivas;
    public $miembrosActivos;
    public $camposBlancos;

    // Datos para gráficos
    public $pastoresPorGenero;
    public $pastoresPorEstadoCivil;
    public $pastoresPorRangoEdad;
    public $pastoresPorGradoMinisterial;
    public $iglesiasPorTipoLocal;
    public $iglesiasPorEstado;

    // Crecimiento anual
    public $crecimientoPastores;
    public $crecimientoIglesias;

    // Datos para el mapa de distribución
    public $estadosConIglesias;
    public $totalIglesiasMapa;
    public $mapboxAccessToken;

    // Filtros y mejoras
    public $selectedYear;
    public $availableYears;
    public $isLoading = false;
    public $chartColors = [
        '#1f77b4', '#ff7f0e', '#2ca02c', '#d62728', '#9467bd',
        '#8c564b', '#e377c2', '#7f7f7f', '#bcbd22', '#17becf'
    ];

    public function mount()
    {
        $this->selectedYear = now()->year;
        $this->availableYears = range(now()->year - 5, now()->year);
        $this->cargarDatos();
    }

    public function updatedSelectedYear()
    {
        $this->cargarDatos();
        $this->dispatch('yearUpdated');
    }

    public function cargarDatos()
    {
        $this->isLoading = true;

        // KPIs principales
        $this->totalPastores = Pastor::count();

        $this->totalColaboradores = Pastor::whereRaw("LOWER(TRIM(nivel_ministerial)) = ?", ['colaborador'])->count();
        $this->totalLaico = Pastor::whereRaw("LOWER(TRIM(nivel_ministerial)) = ?", ['laico'])->count();
        $this->totalLicenciados = Pastor::whereRaw("LOWER(TRIM(nivel_ministerial)) = ?", ['licenciado'])->count();
        $this->totalMinistroOrdenado = Pastor::whereRaw("LOWER(TRIM(nivel_ministerial)) = ?", ['ministro ordenado'])->count();

        $this->totalPastoresReconocidos = Pastor::whereRaw("LOWER(TRIM(nivel_ministerial)) IN (?, ?, ?)", ['laico', 'licenciado', 'ministro ordenado'])->count();

        $this->pastoresActivos = Pastor::where('status', true)->count();
        $this->totalIglesias = Iglesia::count();
        $this->iglesiasActivas = Iglesia::where('activa', true)->count();
        $this->miembrosActivos = Iglesia::sum('miembros_activos');
        $this->camposBlancos = Iglesia::sum('cantidad_campos_blancos');

        // Pastores por género
        $this->pastoresPorGenero = Pastor::selectRaw('genero, COUNT(*) as total')
            ->groupBy('genero')
            ->get()
            ->map(function ($item) {
                if ($item->genero === 'M') {
                    $item->genero = 'Masculino';
                } elseif ($item->genero === 'F') {
                    $item->genero = 'Femenino';
                }
                return $item;
            });

        // Pastores por estado civil
        $this->pastoresPorEstadoCivil = Pastor::selectRaw('estado_civil, COUNT(*) as total')
            ->whereNotNull('estado_civil')
            ->groupBy('estado_civil')
            ->get();

        // Pastores por rango de edad
        $this->pastoresPorRangoEdad = collect([
            ['rango_edad' => '20-30 años', 'total' => Pastor::whereBetween('edad', [20, 30])->count()],
            ['rango_edad' => '31-40 años', 'total' => Pastor::whereBetween('edad', [31, 40])->count()],
            ['rango_edad' => '41-50 años', 'total' => Pastor::whereBetween('edad', [41, 50])->count()],
            ['rango_edad' => '51-60 años', 'total' => Pastor::whereBetween('edad', [51, 60])->count()],
            ['rango_edad' => '60+ años', 'total' => Pastor::where('edad', '>', 60)->count()],
        ])->filter(function ($item) {
            return $item['total'] > 0;
        });

        // Pastores por grado ministerial
        $this->pastoresPorGradoMinisterial = Pastor::selectRaw('nivel_ministerial, COUNT(*) as total')
            ->whereNotNull('nivel_ministerial')
            ->groupBy('nivel_ministerial')
            ->orderBy('total', 'desc')
            ->get();

        // iglesias por tipo de local
        $this->iglesiasPorTipoLocal = TipoLocal::withCount('iglesias')
            ->orderBy('iglesias_count', 'desc')
            ->get()
            ->filter(function ($item) {
                return $item->iglesias_count > 0;
            });

        // iglesias por estado
        $this->iglesiasPorEstado = Estado::withCount('iglesias')
            ->orderBy('iglesias_count', 'desc')
            ->get()
            ->filter(function ($item) {
                return $item->iglesias_count > 0;
            });

        $this->calcularCrecimientoAnual();
        $this->cargarDatosMapa();
        $this->isLoading = false;
    }

    private function cargarDatosMapa()
    {
        // Obtener token de Mapbox desde configuración
        $this->mapboxAccessToken = config('services.mapbox.token');

        // Debug: verificar que el token se esté cargando
        if (empty($this->mapboxAccessToken)) {
            \Log::warning('Token de Mapbox no configurado en services.php');
        }

        // Cargar estados con cantidad de iglesias
        $this->estadosConIglesias = Estado::query()
            ->select('estados.id', 'estados.nombre', 'estados.iso_3166_2')
            ->selectRaw('COUNT(iglesias.id) as cantidad_iglesias')
            ->selectRaw('MAX(iglesias.nombre) as ejemplo_iglesia')
            ->leftJoin('iglesias', 'estados.id', '=', 'iglesias.estado_id')
            ->groupBy('estados.id', 'estados.nombre', 'estados.iso_3166_2')
            ->orderBy('cantidad_iglesias', 'desc')
            ->get()
            ->map(function ($estado) {
                return [
                    'id' => $estado->id,
                    'nombre' => $estado->nombre,
                    'codigo' => $estado->iso_3166_2,
                    'cantidad_iglesias' => $estado->cantidad_iglesias,
                    'ejemplo_iglesia' => $estado->ejemplo_iglesia,
                    'color' => $this->getColorPorCantidad($estado->cantidad_iglesias)
                ];
            });

        $this->totalIglesiasMapa = $this->estadosConIglesias->sum('cantidad_iglesias');
    }

    private function getColorPorCantidad($cantidad)
    {
        if ($cantidad == 0) return '#e0e0e0'; // Gris para estados sin iglesias
        return '#2196f3'; // Azul para estados con iglesias
    }

    private function calcularCrecimientoAnual()
    {
        $currentYear = $this->selectedYear;
        $lastYear = $currentYear - 1;

        // Crecimiento de pastores - Total activos por año
        $pastoresCurrentYear = Pastor::where('status', true)
            ->whereYear('created_at', '<=', $currentYear)
            ->count();
        $pastoresLastYear = Pastor::where('status', true)
            ->whereYear('created_at', '<=', $lastYear)
            ->count();

        $this->crecimientoPastores = [
            'current_year' => $pastoresCurrentYear,
            'last_year' => $pastoresLastYear,
            'diferencia' => $pastoresCurrentYear - $pastoresLastYear,
            'porcentaje' => $pastoresLastYear > 0 ? round((($pastoresCurrentYear - $pastoresLastYear) / $pastoresLastYear) * 100, 2) : 0
        ];

        // Crecimiento de iglesias - Total activas por año
        $iglesiasCurrentYear = Iglesia::where('activa', true)
            ->whereYear('created_at', '<=', $currentYear)
            ->count();
        $iglesiasLastYear = Iglesia::where('activa', true)
            ->whereYear('created_at', '<=', $lastYear)
            ->count();

        $this->crecimientoIglesias = [
            'current_year' => $iglesiasCurrentYear,
            'last_year' => $iglesiasLastYear,
            'diferencia' => $iglesiasCurrentYear - $iglesiasLastYear,
            'porcentaje' => $iglesiasLastYear > 0 ? round((($iglesiasCurrentYear - $iglesiasLastYear) / $iglesiasLastYear) * 100, 2) : 0
        ];

        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
        ->layout($this->getLayout());
    }

    // Listener para ver detalles de un estado
    protected $listeners = ['verDetallesEstado' => 'mostrarDetallesEstado'];

    public function cargarExtensionesPorEstado($estadoId)
    {
        $estadoModel = Estado::find($estadoId);
        
        if (!$estadoModel) {
            $this->dispatch('error', 'Estado no encontrado');
            return;
        }

        // Obtener extensiones con latitud y longitud válidas
        $extensiones = Iglesia::with(['pastor', 'ciudad'])
            ->where('estado_id', $estadoModel->id)
            ->where('activa', true)
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get()
            ->map(function ($iglesia) {
                return [
                    'id' => $iglesia->id,
                    'nombre' => $iglesia->nombre,
                    'latitud' => (float) $iglesia->latitud,
                    'longitud' => (float) $iglesia->longitud,
                    'direccion' => $iglesia->direccion,
                    'pastor' => $iglesia->pastor ? $iglesia->pastor->nombres . ' ' . $iglesia->pastor->apellidos : 'Sin pastor asignado',
                    'ciudad' => $iglesia->ciudad ? $iglesia->ciudad->nombre : 'N/A',
                ];
            });

        if ($extensiones->isEmpty()) {
            $this->dispatch('error', 'No hay extensiones con coordenadas registradas en este estado.');
            return;
        }

        $this->dispatch('mostrarExtensionesEnMapa', [
            'estado' => $estadoModel->nombre,
            'extensiones' => $extensiones
        ]);
    }

    public function mostrarDetallesEstado($estado)
    {
        // Buscar el estado por nombre
        $estadoModel = Estado::where('nombre', 'like', '%' . $estado . '%')->first();

        if (!$estadoModel) {
            $this->dispatch('error', 'Estado no encontrado');
            return;
        }

        // Obtener iglesias del estado con información del pastor
        $iglesias = Iglesia::with(['pastor', 'ciudad', 'municipio', 'parroquia'])
            ->where('estado_id', $estadoModel->id)
            ->where('activa', true)
            ->orderBy('nombre')
            ->get()
            ->map(function ($iglesia) {
                return [
                    'nombre' => $iglesia->nombre,
                    'pastor' => $iglesia->pastor ? $iglesia->pastor->nombres . ' ' . $iglesia->pastor->apellidos : 'Sin pastor asignado',
                    'ciudad' => $iglesia->ciudad ? $iglesia->ciudad->nombre : 'N/A',
                    'municipio' => $iglesia->municipio ? $iglesia->municipio->nombre : 'N/A',
                    'parroquia' => $iglesia->parroquia ? $iglesia->parroquia->nombre : 'N/A',
                    'direccion' => $iglesia->direccion,
                    'miembros_activos' => $iglesia->miembros_activos
                ];
            });

        // Enviar evento para mostrar modal
        $this->dispatch('mostrarModalEstado', [
            'estado' => $estadoModel->nombre,
            'total_iglesias' => $iglesias->count(),
            'iglesias' => $iglesias->take(10), // Mostrar primeras 10 iglesias
            'tiene_mas' => $iglesias->count() > 10
        ]);
    }
}
