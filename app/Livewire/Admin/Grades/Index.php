<?php

namespace App\Livewire\Admin\Grades;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\EvaluationPeriod;
use App\Models\Student;

class Index extends Component
{
    use WithPagination, HasDynamicLayout, HasRegionalFormatting;

    public $search = '';
    public $subject_id = '';
    public $evaluation_period_id = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 25;

    protected $queryString = [
        'search' => ['except' => ''],
        'subject_id' => ['except' => ''],
        'evaluation_period_id' => ['except' => ''],
        'status' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 25]
    ];

    public function getStatsProperty()
    {
        return [
            'total' => Grade::count(),
            'calificadas' => Grade::where('status', 'graded')->count(),
            'pendientes' => Grade::where('status', 'pending')->count(),
            'promedio_general' => Grade::where('status', 'graded')->avg('score'),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortBy = $field;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->subject_id = '';
        $this->evaluation_period_id = '';
        $this->status = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function render()
    {
        $grades = Grade::with(['student', 'evaluation.subject', 'evaluation.evaluationPeriod', 'gradedBy'])
            ->when($this->search, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('nombres', 'like', '%' . $this->search . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                      ->orWhere('codigo', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->subject_id !== '', function ($query) {
                $query->whereHas('evaluation', function ($q) {
                    $q->where('subject_id', $this->subject_id);
                });
            })
            ->when($this->evaluation_period_id !== '', function ($query) {
                $query->whereHas('evaluation', function ($q) {
                    $q->where('evaluation_period_id', $this->evaluation_period_id);
                });
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $subjects = Subject::active()->orderBy('name')->get();
        $evaluationPeriods = EvaluationPeriod::active()->orderBy('number')->get();

        return view('livewire.admin.grades.index', compact('grades', 'subjects', 'evaluationPeriods'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Calificaciones';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.grades.index' => 'Calificaciones'
        ];
    }
}
