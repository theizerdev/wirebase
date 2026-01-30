<?php

namespace App\Livewire\Admin\Grades;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use App\Models\Student;
use App\Models\Grade;
use App\Models\GradeSummary;
use App\Models\EvaluationPeriod;
use App\Models\Subject;

class StudentGrades extends Component
{
    use HasDynamicLayout, HasRegionalFormatting;

    public Student $student;
    public $evaluation_period_id = '';
    public $subject_id = '';

    public function mount(Student $student)
    {
        $this->student = $student->load(['matriculas.schoolPeriod', 'nivelEducativo', 'turno']);
    }

    public function getGradesProperty()
    {
        return Grade::with(['evaluation.subject', 'evaluation.evaluationType', 'evaluation.evaluationPeriod'])
            ->where('student_id', $this->student->id)
            ->when($this->evaluation_period_id !== '', function ($query) {
                $query->whereHas('evaluation', function ($q) {
                    $q->where('evaluation_period_id', $this->evaluation_period_id);
                });
            })
            ->when($this->subject_id !== '', function ($query) {
                $query->whereHas('evaluation', function ($q) {
                    $q->where('subject_id', $this->subject_id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getSummariesProperty()
    {
        return GradeSummary::with(['subject', 'evaluationPeriod'])
            ->where('student_id', $this->student->id)
            ->orderBy('evaluation_period_id')
            ->get()
            ->groupBy('subject_id');
    }

    public function getStatsProperty()
    {
        $grades = $this->grades;
        $gradedGrades = $grades->where('status', 'graded');

        return [
            'total_evaluations' => $grades->count(),
            'graded' => $gradedGrades->count(),
            'pending' => $grades->where('status', 'pending')->count(),
            'average' => $gradedGrades->count() > 0 ? round($gradedGrades->avg('score'), 2) : 0,
        ];
    }

    public function render()
    {
        $evaluationPeriods = EvaluationPeriod::active()->orderBy('number')->get();
        $subjects = Subject::active()->orderBy('name')->get();

        return view('livewire.admin.grades.student-grades', compact('evaluationPeriods', 'subjects'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Notas del Estudiante';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.students.index' => 'Estudiantes',
            'admin.grades.student' => 'Notas'
        ];
    }
}
