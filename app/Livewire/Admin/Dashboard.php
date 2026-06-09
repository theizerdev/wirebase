<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Traits\HasDynamicLayout;
use Carbon\Carbon;
use App\Models\Beneficiario;
use App\Models\Responsable;

class Dashboard extends Component
{
    use HasDynamicLayout;

    public $stats = [];
    public $chartData = [];
    public $alerts = [];
    public $recentBeneficiarios = [];
    public $topResponsables = [];
    public $allResponsablesConBeneficiarios = []; // Nuevo: Todos los responsables con conteo (como el export)
    public $responsablesConBeneficiarios = []; // Nuevo: Responsables con conteo de beneficiarios

    // Filtros por Estado y Casa de Alimentación
    public $filtro_estado_id = null;
    public $filtro_casa_alimentacion_id = null;

    public function mount()
    {
        $this->loadDashboardData();
    }

    /**
     * Se ejecuta automáticamente cuando cambia el filtro de estado
     */
    public function updatedFiltroEstadoId($value)
    {
        // Limpiar el filtro de casa_alimentacion cuando cambia el estado
        $this->filtro_casa_alimentacion_id = null;

        // Recargar todos los datos del dashboard
        $this->loadDashboardData();
    }

    /**
     * Se ejecuta automáticamente cuando cambia el filtro de casa de alimentación
     */
    public function updatedFiltroCasaAlimentacionId($value)
    {
        // Recargar todos los datos del dashboard
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->loadStats();
        $this->loadChartData();
        $this->loadAlerts();
        $this->loadRecentBeneficiarios();
        $this->loadTopResponsables();
        $this->loadResponsablesConBeneficiarios(); // Nuevo método

        $this->dispatch('chartDataUpdated', [
            'chartData' => $this->chartData,
        ]);
    }

    protected function loadStats()
    {
        $query = Beneficiario::query()
            ->whereNotNull('casa_alimentacion_id'); // Solo beneficiarios con casa de alimentación

        // Aplicar filtros si están seleccionados
        if ($this->filtro_casa_alimentacion_id) {
            $query->where('casa_alimentacion_id', $this->filtro_casa_alimentacion_id);
        } elseif ($this->filtro_estado_id) {
            // Si solo hay estado seleccionado, filtrar por casas en ese estado
            $casasIds = \App\Models\CasaAlimentacion::where('estado_id', $this->filtro_estado_id)->pluck('id');
            $query->whereIn('casa_alimentacion_id', $casasIds);
        }

        // Contar casas de alimentación activas según los filtros
        $casasQuery = \App\Models\CasaAlimentacion::query();
        if ($this->filtro_casa_alimentacion_id) {
            $casasQuery->where('id', $this->filtro_casa_alimentacion_id);
        } elseif ($this->filtro_estado_id) {
            $casasQuery->where('estado_id', $this->filtro_estado_id);
        }
        $totalCasas = $casasQuery->count();

        $this->stats = [
            'total_beneficiarios' => $query->count(),
            'total_casas_alimentacion' => $totalCasas,
            'con_responsable' => $query->clone()->whereNotNull('responsable_id')->count(),
            'con_discapacidad' => $query->clone()->where('padece_discapacidad_enfermedad', true)->count(),
            'embarazadas' => $query->clone()->where('es_mujer_embarazada', true)->count(),
            'promedio_edad' => round((float) $query->clone()->avg('edad'), 1),
            'nuevos_ultimos_7_dias' => $query->clone()->where('created_at', '>=', Carbon::now()->subDays(7))->count(),
        ];
    }

    protected function loadChartData()
    {
        $query = Beneficiario::query()
            ->whereNotNull('casa_alimentacion_id'); // Solo beneficiarios con casa de alimentación

        // Aplicar filtros si están seleccionados
        if ($this->filtro_casa_alimentacion_id) {
            $query->where('casa_alimentacion_id', $this->filtro_casa_alimentacion_id);
        } elseif ($this->filtro_estado_id) {
            $casasIds = \App\Models\CasaAlimentacion::where('estado_id', $this->filtro_estado_id)->pluck('id');
            $query->whereIn('casa_alimentacion_id', $casasIds);
        }

        $groups = [
            '0-12' => $query->clone()->whereBetween('edad', [0, 12])->count(),
            '13-18' => $query->clone()->whereBetween('edad', [13, 18])->count(),
            '19-35' => $query->clone()->whereBetween('edad', [19, 35])->count(),
            '36-60' => $query->clone()->whereBetween('edad', [36, 60])->count(),
            '60+' => $query->clone()->where('edad', '>=', 61)->count(),
        ];

        $labels = array_keys($groups);
        $data = array_values($groups);

        if (empty(array_filter($data))) {
            $data = array_fill(0, count($labels), 0);
        }

        $this->chartData = [
            'labels' => $labels,
            'data' => $data,
            'subtitle' => 'Beneficiarios por grupo de edad',
        ];
    }

    protected function loadAlerts()
    {
        $alerts = [];

        $query = Beneficiario::query()
            ->whereNotNull('casa_alimentacion_id'); // Solo beneficiarios con casa de alimentación

        // Aplicar filtros si están seleccionados
        if ($this->filtro_casa_alimentacion_id) {
            $query->where('casa_alimentacion_id', $this->filtro_casa_alimentacion_id);
        } elseif ($this->filtro_estado_id) {
            $casasIds = \App\Models\CasaAlimentacion::where('estado_id', $this->filtro_estado_id)->pluck('id');
            $query->whereIn('casa_alimentacion_id', $casasIds);
        }

        $sinResponsable = $query->clone()->whereNull('responsable_id')->count();
        if ($sinResponsable > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'ri-user-unfollow-line',
                'title' => 'Beneficiarios sin responsable',
                'message' => "$sinResponsable beneficiarios sin responsable asignado",
                'color' => '#f59e0b',
            ];
        }

        $sinTelefono = $query->clone()->where(function ($q) {
            $q->whereNull('telefono_principal')
                ->orWhere('telefono_principal', '');
        })->count();

        if ($sinTelefono > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'ri-phone-missed-line',
                'title' => 'Falta información de contacto',
                'message' => "$sinTelefono beneficiarios sin teléfono principal",
                'color' => '#3b82f6',
            ];
        }

        $sinCedula = $query->clone()->where(function ($q) {
            $q->whereNull('cedula')
                ->orWhere('cedula', '');
        })->count();

        if ($sinCedula > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'ri-id-card-line',
                'title' => 'Datos incompletos',
                'message' => "$sinCedula beneficiarios sin cédula registrada",
                'color' => '#ef4444',
            ];
        }

        $this->alerts = $alerts;
    }

    protected function loadRecentBeneficiarios()
    {
        $query = Beneficiario::with(['responsable', 'casaAlimentacion'])
            ->whereNotNull('casa_alimentacion_id'); // Solo beneficiarios con casa de alimentación

        // Aplicar filtros si están seleccionados
        if ($this->filtro_casa_alimentacion_id) {
            $query->where('casa_alimentacion_id', $this->filtro_casa_alimentacion_id);
        } elseif ($this->filtro_estado_id) {
            $casasIds = \App\Models\CasaAlimentacion::where('estado_id', $this->filtro_estado_id)->pluck('id');
            $query->whereIn('casa_alimentacion_id', $casasIds);
        }

        $this->recentBeneficiarios = $query
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(function (Beneficiario $beneficiario) {
                return [
                    'id' => $beneficiario->id,
                    'nombre' => trim($beneficiario->nombres . ' ' . $beneficiario->apellidos),
                    'edad' => $beneficiario->edad,
                    'telefono' => $beneficiario->telefono_principal,
                    'responsable' => $beneficiario->responsable?->nombre_completo ?? 'Sin responsable',
                    'casa_alimentacion' => $beneficiario->casaAlimentacion?->codigo ?? 'N/A',
                    'created_at' => $beneficiario->created_at?->format('d/m/Y') ?? '',
                ];
            })
            ->toArray();
    }

    protected function loadTopResponsables()
    {
        $query = Beneficiario::selectRaw('responsable_id, count(*) as total')
            ->whereNotNull('responsable_id')
            ->whereNotNull('casa_alimentacion_id'); // Solo beneficiarios con casa de alimentación

        // Aplicar filtros si están seleccionados
        if ($this->filtro_casa_alimentacion_id) {
            $query->where('casa_alimentacion_id', $this->filtro_casa_alimentacion_id);
        } elseif ($this->filtro_estado_id) {
            $casasIds = \App\Models\CasaAlimentacion::where('estado_id', $this->filtro_estado_id)->pluck('id');
            $query->whereIn('casa_alimentacion_id', $casasIds);
        }

        $topResponsables = $query
            ->groupBy('responsable_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $responsables = Responsable::whereIn('id', $topResponsables->pluck('responsable_id'))
            ->get()
            ->keyBy('id');

        $this->topResponsables = $topResponsables->map(function ($row) use ($responsables) {
            $responsable = $responsables->get($row->responsable_id);

            return [
                'nombre' => $responsable?->nombre_completo ?? 'Responsable desconocido',
                'candidatos' => $row->total,
                'telefono' => $responsable?->telefono,
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'stats' => $this->stats,
            'chartData' => $this->chartData,
            'alerts' => $this->alerts,
            'recentBeneficiarios' => $this->recentBeneficiarios,
            'topResponsables' => $this->topResponsables,
            'responsablesConBeneficiarios' => $this->responsablesConBeneficiarios, // Nuevo
            'estados' => \App\Models\Estado::orderBy('nombre')->get(['id', 'nombre']),
            'casasAlimentacion' => $this->getCasasAlimentacion(),
        ])->layout($this->getLayout());
    }

    /**
     * Obtener casas de alimentación filtradas por estado seleccionado
     */
    public function getCasasAlimentacion()
    {
        if ($this->filtro_estado_id) {
            return \App\Models\CasaAlimentacion::where('estado_id', $this->filtro_estado_id)
                ->orderBy('codigo')
                ->get(['id', 'codigo']);
        }

        return collect();
    }

    /**
     * Limpiar todos los filtros
     */
    public function limpiarFiltros()
    {
        $this->filtro_estado_id = null;
        $this->filtro_casa_alimentacion_id = null;
        $this->loadDashboardData();
    }

    /**
     * Cargar lista completa de responsables con sus beneficiarios (como en el Excel)
     */
    protected function loadResponsablesConBeneficiarios()
    {
        $query = Beneficiario::with('responsable')
            ->whereNotNull('casa_alimentacion_id')
            ->whereNotNull('responsable_id');

        // Aplicar filtros si están seleccionados
        if ($this->filtro_casa_alimentacion_id) {
            $query->where('casa_alimentacion_id', $this->filtro_casa_alimentacion_id);
        } elseif ($this->filtro_estado_id) {
            $casasIds = \App\Models\CasaAlimentacion::where('estado_id', $this->filtro_estado_id)->pluck('id');
            $query->whereIn('casa_alimentacion_id', $casasIds);
        }

        // Agrupar por responsable y contar
        $responsablesGrouped = $query->get()
            ->groupBy(function ($item) {
                return $item->responsable?->nombre_completo ?? 'Sin responsable';
            })
            ->map->count()
            ->sortDesc();

        // Convertir a array para la vista
        $this->responsablesConBeneficiarios = $responsablesGrouped->toArray();
    }
}
