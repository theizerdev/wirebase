<?php

namespace App\Livewire\Admin\Evaluations;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Evaluation;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\EvaluationPeriod;
use App\Models\EvaluationType;

class Index extends Component
{
    use WithPagination, HasDynamicLayout, HasRegionalFormatting;

    public $search = '';
    public $subject_id = '';
    public $teacher_id = '';
    public $evaluation_period_id = '';
    public $evaluation_type_id = '';
    public $is_published = '';
    public $sortBy = 'evaluation_date';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'subject_id' => ['except' => ''],
        'teacher_id' => ['except' => ''],
        'evaluation_period_id' => ['except' => ''],
        'evaluation_type_id' => ['except' => ''],
        'is_published' => ['except' => ''],
        'sortBy' => ['except' => 'evaluation_date'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function getStatsProperty()
    {
        $baseQuery = $this->getBaseQuery();

        return [
            'total' => (clone $baseQuery)->count(),
            'publicadas' => (clone $baseQuery)->where('is_published', true)->count(),
            'pendientes' => (clone $baseQuery)->where('is_published', false)->count(),
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

    public function delete(Evaluation $evaluation)
    {
        if (!auth()->user()->can('delete evaluations')) {
            session()->flash('error', 'No tienes permiso para eliminar evaluaciones.');
            return;
        }

        try {
            $evaluation->delete();
            session()->flash('message', 'Evaluación eliminada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function togglePublished(Evaluation $evaluation)
    {
        if (!auth()->user()->can('edit evaluations')) {
            session()->flash('error', 'No tienes permiso para editar evaluaciones.');
            return;
        }

        $evaluation->is_published = !$evaluation->is_published;
        $evaluation->save();
        session()->flash('message', $evaluation->is_published ? 'Notas publicadas.' : 'Notas despublicadas.');
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->subject_id = '';
        $this->teacher_id = '';
        $this->evaluation_period_id = '';
        $this->evaluation_type_id = '';
        $this->is_published = '';
        $this->sortBy = 'evaluation_date';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    private function getBaseQuery()
    {
        return Evaluation::with(['subject', 'teacher.user', 'evaluationPeriod', 'evaluationType']);
    }

    private function getQuery()
    {
        return $this->getBaseQuery()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->subject_id !== '', function ($query) {
                $query->where('subject_id', $this->subject_id);
            })
            ->when($this->teacher_id !== '', function ($query) {
                $query->where('teacher_id', $this->teacher_id);
            })
            ->when($this->evaluation_period_id !== '', function ($query) {
                $query->where('evaluation_period_id', $this->evaluation_period_id);
            })
            ->when($this->evaluation_type_id !== '', function ($query) {
                $query->where('evaluation_type_id', $this->evaluation_type_id);
            })
            ->when($this->is_published !== '', function ($query) {
                $query->where('is_published', $this->is_published === '1');
            })
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function render()
    {
        $evaluations = $this->getQuery()->paginate($this->perPage);
        $subjects = Subject::active()->orderBy('name')->get();
        $teachers = Teacher::active()->with('user')->get();
        $evaluationPeriods = EvaluationPeriod::active()->orderBy('number')->get();
        $evaluationTypes = EvaluationType::active()->orderBy('name')->get();

        return view('livewire.admin.evaluations.index', compact(
            'evaluations', 'subjects', 'teachers', 'evaluationPeriods', 'evaluationTypes'
        ))->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Gestión de Evaluaciones';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.evaluations.index' => 'Evaluaciones'
        ];
    }
}
