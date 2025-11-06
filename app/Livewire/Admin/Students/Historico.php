<?php

namespace App\Livewire\Admin\Students;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use App\Models\Student;
use App\Models\Matricula;
use App\Models\Pago;
use App\Models\StudentAccessLog;
use App\Models\PaymentSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Historico extends Component
{
    use HasDynamicLayout;
    use HasRegionalFormatting;


    public $student;
    public $selectedPeriod = '6months';
    public $selectedYear;

    public function mount(Student $student)
    {
        $this->student = $student->load(['matriculas.programa', 'matriculas.pagos']);
        $this->selectedYear = now()->year;
    }

    public function getAccessDataProperty()
    {
        $startDate = $this->getStartDate();

        return StudentAccessLog::where('student_id', $this->student->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'accesos' => $item->total
                ];
            });
    }

    public function getPaymentDataProperty()
    {
        $startDate = $this->getStartDate();

        return Pago::whereHas('matricula', function($q) {
                $q->where('estudiante_id', $this->student->id);
            })
            ->where('fecha', '>=', $startDate)
            ->where('estado', 'aprobado')
            ->selectRaw('MONTH(fecha) as month, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::create()->month($item->month)->format('M'),
                    'total' => $item->total
                ];
            });
    }

    public function getPendingPaymentsProperty()
    {
        return PaymentSchedule::whereHas('matricula', function($q) {
                $q->where('estudiante_id', $this->student->id);
            })
            ->where('estado', 'pendiente')
            ->get()
            ->sum(function($schedule) {
                return $schedule->monto - $schedule->monto_pagado;
            });
    }

    public function getOverduePaymentsProperty()
    {
        return PaymentSchedule::whereHas('matricula', function($q) {
                $q->where('estudiante_id', $this->student->id);
            })
            ->where('estado', 'pendiente')
            ->where('fecha_vencimiento', '<', now())
            ->count();
    }

    public function getOverduePaymentListProperty()
    {
        return PaymentSchedule::with('matricula.programa')
            ->whereHas('matricula', function($q) {
                $q->where('estudiante_id', $this->student->id);
            })
            ->where('estado', 'pendiente')
            ->where('fecha_vencimiento', '<', now())
            ->orderBy('fecha_vencimiento')
            ->get();
    }

    public function getTotalPaidProperty()
    {
        return Pago::whereHas('matricula', function($q) {
                $q->where('estudiante_id', $this->student->id);
            })
            ->where('estado', 'aprobado')
            ->sum('total');
    }

    public function getActiveEnrollmentsProperty()
    {
        return $this->student->matriculas()
            ->where('estado', 'activo')
            ->count();
    }

    public function getMonthlyAccessStatsProperty()
    {
        $currentMonth = now()->startOfMonth();

        return StudentAccessLog::where('student_id', $this->student->id)
            ->where('created_at', '>=', $currentMonth)
            ->count();
    }

    public function updatedSelectedPeriod()
    {
        $this->dispatch('periodChanged');
    }

    private function getStartDate()
    {
        return match($this->selectedPeriod) {
            '3months' => now()->subMonths(3),
            '6months' => now()->subMonths(6),
            '1year' => now()->subYear(),
            'all' => $this->student->created_at,
            default => now()->subMonths(6)
        };
    }

    public function render()
    {
        return view('livewire.admin.students.historico')->layout($this->getLayout());
    }
}
