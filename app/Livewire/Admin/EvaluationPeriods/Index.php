<?php

namespace App\Livewire\Admin\EvaluationPeriods;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EvaluationPeriod;
use App\Models\SchoolPeriod;

class Index extends Component
{
    use WithPagination, HasDynamicLayout, HasRegionalFormatting;

    public $search = '';
    public $school_period_id = '';
    public $is_active = '';
    public $is_closed = '';
    public $sortBy = 'number';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'school_period_id' => ['except' => ''],
        'is_active' => ['except' => ''],
        'is_closed' => ['except' => ''],
        'sortBy' => ['except' => 'number'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10]
    ];

    public function getStatsProperty()
    {
        $baseQuery = $this->getBaseQuery();

        return [
            'total' => (clone $baseQuery)->count(),
            'activos' => (clone $baseQuery)->where('is_active', true)->count(),
            'cerrados' => (clone $baseQuery)->where('is_closed', true)->count(),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSchoolPeriodId()
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

    public function delete(EvaluationPeriod $period)
    {
        if (!auth()->user()->can('delete evaluation_periods')) {
            session()->flash('error', 'No tienes permiso para eliminar lapsos.');
            return;
        }

        try {
            $period->delete();
            session()->flash('message', 'Lapso eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el lapso: ' . $e->getMessage());
        }

        $this->resetPage();
    }

    public function toggleStatus(EvaluationPeriod $period)
    {
        if (!auth()->user()->can('edit evaluation_periods')) {
            session()->flash('error', 'No tienes permiso para editar lapsos.');
            return;
        }

        $period->is_active = !$period->is_active;
        $period->save();
        
        session()->flash('message', 'Estado del lapso actualizado correctamente.');
    }

    public function toggleClosed(EvaluationPeriod $period)
    {
        if (!auth()->user()->can('edit evaluation_periods')) {
            session()->flash('error', 'No tienes permiso para editar lapsos.');
            return;
        }

        $period->is_closed = !$period->is_closed;
        $period->save();
        
        session()->flash('message', $period->is_closed ? 'Lapso cerrado correctamente.' : 'Lapso abierto correctamente.');
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->school_period_id = '';
        $this->is_active = '';
        $this->is_closed = '';
        $this->sortBy = 'number';
        $this->sortDirection = 'asc';
        $this->perPage = 10;
        $this->resetPage();
    }

    private function getBaseQuery()
    {
        return EvaluationPeriod::with(['schoolPeriod']);
    }

    private function getQuery()
    {
        return $this->getBaseQuery()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('schoolPeriod', function ($subQuery) {
                            $subQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->school_period_id !== '', function ($query) {
                $query->where('school_period_id', $this->school_period_id);
            })
            ->when($this->is_active !== '', function ($query) {
                $query->where('is_active', $this->is_active === '1');
            })
            ->when($this->is_closed !== '', function ($query) {
                $query->where('is_closed', $this->is_closed === '1');
            })
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function render()
    {
        $periods = $this->getQuery()->paginate($this->perPage);
        $schoolPeriods = SchoolPeriod::orderBy('name', 'desc')->get();

        return view('livewire.admin.evaluation-periods.index', compact('periods', 'schoolPeriods'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Lapsos de Evaluación';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.evaluation-periods.index' => 'Lapsos de Evaluación'
        ];
    }
}
