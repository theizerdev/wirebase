<?php

namespace App\Livewire\Admin\Monitoreo;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Student;
use App\Models\NivelEducativo;
use Livewire\Attributes\On;

class Estudiantes extends Component
{



    use HasDynamicLayout;public $lastUpdate;

    public function mount()
    {
        abort_unless(auth()->user()->can('view monitoreo estudiantes'), 403);
        $this->lastUpdate = now()->format('H:i:s');
    }

    #[On('refresh-estudiantes')]
    public function refreshData()
    {
        $this->lastUpdate = now()->format('H:i:s');
    }

    public function render()
    {
        $query = Student::query();

        if (!auth()->user()->hasRole('Super Administrador')) {
            $query->where('empresa_id', auth()->user()->empresa_id)
                  ->where('sucursal_id', auth()->user()->sucursal_id);
        }

        $baseQuery = clone $query;

        $stats = [
            'total' => $baseQuery->count(),
            'activos' => (clone $query)->where('status', 1)->count(),
            'inactivos' => (clone $query)->where('status', 0)->count(),
            'nuevos_mes' => (clone $query)->whereMonth('created_at', now()->month)->count(),
        ];

        // Crear queries independientes para evitar conflictos
        $levelQuery = Student::query();
        $gradeQuery = Student::query();
        $sectionQuery = Student::query();
        $recentQuery = Student::query();

        if (!auth()->user()->hasRole('Super Administrador')) {
            $levelQuery->where('empresa_id', auth()->user()->empresa_id)
                      ->where('sucursal_id', auth()->user()->sucursal_id);
            $gradeQuery->where('empresa_id', auth()->user()->empresa_id)
                       ->where('sucursal_id', auth()->user()->sucursal_id);
            $sectionQuery->where('empresa_id', auth()->user()->empresa_id)
                         ->where('sucursal_id', auth()->user()->sucursal_id);
            $recentQuery->where('empresa_id', auth()->user()->empresa_id)
                        ->where('sucursal_id', auth()->user()->sucursal_id);
        }

        $byLevel = $levelQuery->join('niveles_educativos', 'students.nivel_educativo_id', '=', 'niveles_educativos.id')
            ->selectRaw('niveles_educativos.nombre as nivel, COUNT(students.id) as count')
            ->groupBy('niveles_educativos.nombre')
            ->get();

        $byGrade = $gradeQuery->selectRaw('grado, COUNT(*) as count')
            ->where('students.status', 1)
            ->groupBy('grado')
            ->orderBy('grado')
            ->get();

        $bySection = $sectionQuery->selectRaw('seccion, COUNT(*) as count')
            ->where('students.status', 1)
            ->groupBy('seccion')
            ->orderBy('seccion')
            ->get();

        $recent = $recentQuery->orderBy('created_at', 'desc')->take(8)->get();

        return view('livewire.admin.monitoreo.estudiantes', compact('stats', 'byLevel', 'byGrade', 'bySection', 'recent'))
            ->layout('components.layouts.admin', ['title' => 'Monitoreo de Estudiantes']);
    }
}





