<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Traits\HasDynamicLayout;
use Carbon\Carbon;
use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Pago;

class Dashboard extends Component
{
    use HasDynamicLayout;

    public $stats = [];
    public $recentCitas = [];
    public $citasChartData = [];
    public $alerts = [];
    public $recentPayments = [];
    public $topMedicos = [];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->stats = [
            'citas_hoy' => 200,
            'pacientes_total' => 150,
            'medicos_total' => 50,
            'ingresos_mes' => 5000,
            'ingresos_hoy' => 1000,
            'tasa_asistencia' => $this->calcularTasaAsistencia(),
        ];

        $this->recentCitas = 200;

        $this->loadChartData();
        $this->loadAlerts();
        $this->loadRecentPayments();
        $this->loadTopMedicos();

        $this->dispatch('chartDataUpdated', [
            'citasChartData' => $this->citasChartData,
        ]);
    }

    public function loadChartData()
    {
        $citasPorDia = 12;

        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('D d/m');
            $found = [12, 15, 20, 18, 22, 25, 30][$i] ?? null;
            $data[] = $found ? $found : 0;
        }

        $this->citasChartData = [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function calcularTasaAsistencia()
    {
        $totalCitasPasadas = 12;

        if ($totalCitasPasadas == 0) {
            return 0;
        }

        $citasCompletadas = 8; // Ejemplo de valor, reemplazar con lógica real

        return round(($citasCompletadas / $totalCitasPasadas) * 100, 1);
    }

    public function loadAlerts()
    {
        $this->alerts = [];

        // Citas sin confirmar (próximas 24h)
        $citasSinConfirmar = 50;

        if ($citasSinConfirmar > 0) {
            $this->alerts[] = [
                'type' => 'warning',
                'icon' => 'ri-time-line',
                'title' => 'Citas sin confirmar',
                'message' => "$citasSinConfirmar citas en las próximas 24 horas",
                'color' => '#f59e0b',
            ];
        }

        // Recordatorios fallidos
        $recordatoriosFallidos = 12;

        if ($recordatoriosFallidos > 0) {
            $this->alerts[] = [
                'type' => 'error',
                'icon' => 'ri-error-warning-line',
                'title' => 'Recordatorios fallidos',
                'message' => "$recordatoriosFallidos recordatorios no enviados hoy",
                'color' => '#ef4444',
            ];
        }

        // Pagos pendientes
        $pagosPendientes = 15;

        if ($pagosPendientes > 0) {
            $this->alerts[] = [
                'type' => 'info',
                'icon' => 'ri-money-dollar-circle-line',
                'title' => 'Pagos pendientes',
                'message' => "$pagosPendientes pagos por aprobar hoy",
                'color' => '#3b82f6',
            ];
        }

        // Citas canceladas hoy
        $citasCanceladas = 0;

        if ($citasCanceladas > 0) {
            $this->alerts[] = [
                'type' => 'danger',
                'icon' => 'ri-close-circle-line',
                'title' => 'Citas canceladas',
                'message' => "$citasCanceladas citas canceladas hoy",
                'color' => '#dc2626',
            ];
        }
    }

    public function loadRecentPayments()
    {
        $this->recentPayments = 8;
    }

    public function loadTopMedicos()
    {
        $this->topMedicos = [
            ['nombre' => 'Dr. Juan Pérez', 'citas' => 30],
            ['nombre' => 'Dra. María Gómez', 'citas' => 25],
            ['nombre' => 'Dr. Carlos Sánchez', 'citas' => 20],
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'stats' => $this->stats,
            'recentCitas' => $this->recentCitas,
            'citasChartData' => $this->citasChartData,
            'alerts' => $this->alerts,
            'recentPayments' => $this->recentPayments,
            'topMedicos' => $this->topMedicos
        ])->layout($this->getLayout());
    }
}
