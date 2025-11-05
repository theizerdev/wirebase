<?php

namespace App\Livewire\Admin\Monitoreo;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\StudentAccessLog;
use Carbon\Carbon;
use Livewire\Attributes\On;

class Accesos extends Component
{
    use HasDynamicLayout;


    public $startDate;
    public $endDate;
    public $lastUpdate;

    public function mount()
    {
        abort_unless(auth()->user()->can('view monitoreo accesos'), 403);
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->lastUpdate = now()->format('H:i:s');
    }

    #[On('refresh-accesos')]
    public function refreshData()
    {
        $this->lastUpdate = now()->format('H:i:s');
    }

    public function exportExcel()
    {
        abort_unless(auth()->user()->can('export monitoreo accesos'), 403);

        return response()->streamDownload(function () {
            $logs = StudentAccessLog::with('student')
                ->whereBetween('access_time', [$this->startDate, $this->endDate])
                ->orderBy('access_time', 'desc')
                ->get();

            $csv = fopen('php://output', 'w');
            fputcsv($csv, ['Fecha', 'Hora', 'Código', 'Estudiante', 'Grado', 'Sección', 'Tipo', 'Registrado Por']);

            foreach ($logs as $log) {
                fputcsv($csv, [
                    $log->access_time->format('Y-m-d'),
                    $log->access_time->format('H:i:s'),
                    $log->student->codigo,
                    $log->student->nombres . ' ' . $log->student->apellidos,
                    $log->student->grado,
                    $log->student->seccion,
                    ucfirst($log->type),
                    $log->registeredBy->name ?? 'N/A',
                ]);
            }

            fclose($csv);
        }, 'accesos_' . now()->format('Y-m-d_His') . '.csv');
    }

    public function render()
    {
        return view('livewire.admin.monitoreo.accesos')->layout($this->getLayout());
    }
}



