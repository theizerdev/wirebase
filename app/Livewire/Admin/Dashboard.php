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

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->loadStats();
        $this->loadChartData();
        $this->loadAlerts();
        $this->loadRecentBeneficiarios();
        $this->loadTopResponsables();

        $this->dispatch('chartDataUpdated', [
            'chartData' => $this->chartData,
        ]);
    }

    protected function loadStats()
    {
        $this->stats = [
            'total_beneficiarios' => Beneficiario::count(),
            'con_responsable' => Beneficiario::whereNotNull('responsable_id')->count(),
            'con_discapacidad' => Beneficiario::where('padece_discapacidad_enfermedad', true)->count(),
            'embarazadas' => Beneficiario::where('es_mujer_embarazada', true)->count(),
            'promedio_edad' => round((float) Beneficiario::avg('edad'), 1),
            'nuevos_ultimos_7_dias' => Beneficiario::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
        ];
    }

    protected function loadChartData()
    {
        $groups = [
            '0-12' => Beneficiario::whereBetween('edad', [0, 12])->count(),
            '13-18' => Beneficiario::whereBetween('edad', [13, 18])->count(),
            '19-35' => Beneficiario::whereBetween('edad', [19, 35])->count(),
            '36-60' => Beneficiario::whereBetween('edad', [36, 60])->count(),
            '60+' => Beneficiario::where('edad', '>=', 61)->count(),
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

        $sinResponsable = Beneficiario::whereNull('responsable_id')->count();
        if ($sinResponsable > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'ri-user-unfollow-line',
                'title' => 'Beneficiarios sin responsable',
                'message' => "$sinResponsable beneficiarios sin responsable asignado",
                'color' => '#f59e0b',
            ];
        }

        $sinTelefono = Beneficiario::where(function ($query) {
            $query->whereNull('telefono_principal')
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

        $sinCedula = Beneficiario::where(function ($query) {
            $query->whereNull('cedula')
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
        $this->recentBeneficiarios = Beneficiario::with('responsable')
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
                    'created_at' => $beneficiario->created_at?->format('d/m/Y') ?? '',
                ];
            })
            ->toArray();
    }

    protected function loadTopResponsables()
    {
        $topResponsables = Beneficiario::selectRaw('responsable_id, count(*) as total')
            ->whereNotNull('responsable_id')
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
        ])->layout($this->getLayout());
    }
}
