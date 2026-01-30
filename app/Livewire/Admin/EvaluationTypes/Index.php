<?php

namespace App\Livewire\Admin\EvaluationTypes;

use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EvaluationType;

class Index extends Component
{
    use WithPagination, HasDynamicLayout, HasRegionalFormatting;

    public $search = '';
    public $is_active = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'is_active' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10]
    ];

    public function getStatsProperty()
    {
        return [
            'total' => EvaluationType::count(),
            'activos' => EvaluationType::where('is_active', true)->count(),
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

    public function delete(EvaluationType $type)
    {
        if (!auth()->user()->can('delete evaluation_types')) {
            session()->flash('error', 'No tienes permiso para eliminar tipos de evaluación.');
            return;
        }

        try {
            $type->delete();
            session()->flash('message', 'Tipo de evaluación eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function toggleStatus(EvaluationType $type)
    {
        if (!auth()->user()->can('edit evaluation_types')) {
            session()->flash('error', 'No tienes permiso para editar tipos de evaluación.');
            return;
        }

        $type->is_active = !$type->is_active;
        $type->save();
        session()->flash('message', 'Estado actualizado correctamente.');
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->is_active = '';
        $this->sortBy = 'name';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function render()
    {
        $types = EvaluationType::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->when($this->is_active !== '', function ($query) {
                $query->where('is_active', $this->is_active === '1');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.evaluation-types.index', compact('types'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Tipos de Evaluación';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.evaluation-types.index' => 'Tipos de Evaluación'
        ];
    }
}
