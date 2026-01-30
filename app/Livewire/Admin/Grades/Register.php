<?php

namespace App\Livewire\Admin\Grades;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Matricula;

class Register extends Component
{
    use HasDynamicLayout, HasRegionalFormatting;

    public Evaluation $evaluation;
    public $grades = [];
    public $showSaveConfirmation = false;

    public function mount(Evaluation $evaluation)
    {
        $this->evaluation = $evaluation->load(['subject', 'teacher.user', 'evaluationPeriod', 'evaluationType']);
        $this->loadStudentsAndGrades();
    }

    public function loadStudentsAndGrades()
    {
        // Obtener el programa asociado a la materia de la evaluación
        $programId = $this->evaluation->subject->program_id;
        
        $matriculas = Matricula::with('estudiante')
            ->where('empresa_id', $this->evaluation->empresa_id)
            ->where('sucursal_id', $this->evaluation->sucursal_id)
            ->where('programa_id', $programId)
            ->where('estado', 'activo')
            ->whereHas('estudiante', function($q) {
                $q->where('status', 1);
            })
            ->get();

        foreach ($matriculas as $matricula) {
            $existingGrade = Grade::where('evaluation_id', $this->evaluation->id)
                ->where('student_id', $matricula->estudiante_id)
                ->first();

            $this->grades[$matricula->estudiante_id] = [
                'student_id' => $matricula->estudiante_id,
                'matricula_id' => $matricula->id,
                'student_name' => $matricula->estudiante->nombres . ' ' . $matricula->estudiante->apellidos,
                'student_code' => $matricula->estudiante->codigo,
                'score' => $existingGrade ? $existingGrade->score : null,
                'status' => $existingGrade ? $existingGrade->status : 'pending',
                'observations' => $existingGrade ? $existingGrade->observations : '',
                'grade_id' => $existingGrade ? $existingGrade->id : null,
            ];
        }

        ksort($this->grades);
    }

    public function setStatus($studentId, $status)
    {
        if (isset($this->grades[$studentId])) {
            $this->grades[$studentId]['status'] = $status;
            if ($status === 'absent' || $status === 'exempt') {
                $this->grades[$studentId]['score'] = null;
            }
        }
    }

    public function saveGrades()
    {
        if (!auth()->user()->can('create grades')) {
            session()->flash('error', 'No tienes permiso para registrar calificaciones.');
            return;
        }

        try {
            $saved = 0;
            foreach ($this->grades as $studentId => $gradeData) {
                $score = $gradeData['score'];
                $status = $gradeData['status'];

                if ($score !== null && $score !== '') {
                    if ($score < 0 || $score > $this->evaluation->max_score) {
                        session()->flash('error', "Nota inválida para {$gradeData['student_name']}. Debe estar entre 0 y {$this->evaluation->max_score}.");
                        return;
                    }
                    $status = 'graded';
                }

                Grade::updateOrCreate(
                    [
                        'evaluation_id' => $this->evaluation->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'empresa_id' => auth()->user()->empresa_id,
                        'sucursal_id' => auth()->user()->sucursal_id,
                        'matricula_id' => $gradeData['matricula_id'],
                        'score' => $score,
                        'status' => $status,
                        'observations' => $gradeData['observations'],
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                    ]
                );
                $saved++;
            }

            session()->flash('message', "Se guardaron {$saved} calificaciones correctamente.");
            $this->loadStudentsAndGrades();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function getStatsProperty()
    {
        $total = count($this->grades);
        $graded = collect($this->grades)->where('status', 'graded')->count();
        $pending = collect($this->grades)->where('status', 'pending')->count();
        $absent = collect($this->grades)->where('status', 'absent')->count();
        $exempt = collect($this->grades)->where('status', 'exempt')->count();
        $scores = collect($this->grades)->where('status', 'graded')->pluck('score')->filter();

        return [
            'total' => $total,
            'graded' => $graded,
            'pending' => $pending,
            'absent' => $absent,
            'exempt' => $exempt,
            'average' => $scores->count() > 0 ? round($scores->avg(), 2) : 0,
            'max' => $scores->count() > 0 ? $scores->max() : 0,
            'min' => $scores->count() > 0 ? $scores->min() : 0,
        ];
    }

    public function render()
    {
        return view('livewire.admin.grades.register')
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Registrar Calificaciones';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.evaluations.index' => 'Evaluaciones',
            'admin.grades.register' => 'Registrar Notas'
        ];
    }
}
