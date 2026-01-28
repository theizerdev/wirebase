<?php

namespace App\Livewire\Admin\Subjects;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use App\Traits\Exportable;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Subject;
use App\Models\Programa;
use App\Models\NivelEducativo;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout, HasRegionalFormatting;

    public $search = '';
    public $program_id = '';
    public $educational_level_id = '';
    public $is_active = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'program_id' => ['except' => ''],
        'educational_level_id' => ['except' => ''],
        'is_active' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function getStatsProperty()
    {
        $baseQuery = $this->getBaseQuery();

        return [
            'total' => (clone $baseQuery)->count(),
            'activas' => (clone $baseQuery)->where('is_active', true)->count(),
            'inactivas' => (clone $baseQuery)->where('is_active', false)->count(),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedProgramId()
    {
        $this->resetPage();
    }

    public function updatedEducationalLevelId()
    {
        $this->resetPage();
    }

    public function updatedIsActive()
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

    public function delete(Subject $subject)
    {
        if (!auth()->user()->can('delete subjects')) {
            session()->flash('error', 'No tienes permiso para eliminar materias.');
            return;
        }

        try {
            $subject->delete();
            session()->flash('message', 'Materia eliminada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la materia: ' . $e->getMessage());
        }

        $this->resetPage();
    }

    public function toggleStatus(Subject $subject)
    {
        if (!auth()->user()->can('edit subjects')) {
            session()->flash('error', 'No tienes permiso para editar materias.');
            return;
        }

        $subject->is_active = !$subject->is_active;
        $subject->save();
        
        session()->flash('message', 'Estado de la materia actualizado correctamente.');
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->program_id = '';
        $this->educational_level_id = '';
        $this->is_active = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function getExportQuery()
    {
        return $this->getQuery();
    }

    public function getExportHeaders()
    {
        return [
            'Código', 'Nombre', 'Descripción', 'Créditos', 'Horas/Semana', 'Programa', 'Nivel Educativo', 'Estado'
        ];
    }

    public function formatExportRow($subject)
    {
        return [
            $subject->code,
            $subject->name,
            $subject->description ?? '',
            $subject->credits,
            $subject->hours_per_week,
            $subject->programa->nombre ?? '',
            $subject->educationalLevel->nombre ?? '',
            $subject->is_active ? 'Activa' : 'Inactiva'
        ];
    }

    private function getBaseQuery()
    {
        return Subject::with(['programa', 'educationalLevel', 'createdBy', 'updatedBy']);
    }

    private function getQuery()
    {
        return $this->getBaseQuery()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('programa', function ($subQuery) {
                            $subQuery->where('nombre', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('educationalLevel', function ($subQuery) {
                            $subQuery->where('nombre', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->program_id !== '', function ($query) {
                $query->where('program_id', $this->program_id);
            })
            ->when($this->educational_level_id !== '', function ($query) {
                $query->where('educational_level_id', $this->educational_level_id);
            })
            ->when($this->is_active !== '', function ($query) {
                $query->where('is_active', $this->is_active === '1');
            })
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function render()
    {
        $subjects = $this->getQuery()->paginate($this->perPage);
        $programs = Programa::orderBy('nombre')->get();
        $educationalLevels = NivelEducativo::orderBy('nombre')->get();

        return view('livewire.admin.subjects.index', compact('subjects', 'programs', 'educationalLevels'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Gestión de Materias';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.subjects.index' => 'Materias'
        ];
    }
}