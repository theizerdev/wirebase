<?php

namespace App\Livewire\Admin\Series;
use App\Traits\HasDynamicLayout;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Serie;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '';
    public $tipo_documento = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'tipo_documento' => ['except' => ''],
        'perPage' => ['except' => 10]
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->tipo_documento = '';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function toggleActivo($id)
    {
        $serie = Serie::findOrFail($id);
        $serie->update(['activo' => !$serie->activo]);
        
        session()->flash('message', 'Estado actualizado correctamente');
    }

    public function delete($id)
    {
        Serie::findOrFail($id)->delete();
        session()->flash('message', 'Serie eliminada correctamente');
    }

    public function render()
    {
        $series = Serie::query()
            ->when($this->search, fn($q) => $q->where('serie', 'like', "%{$this->search}%"))
            ->when($this->tipo_documento, fn($q) => $q->where('tipo_documento', $this->tipo_documento))
            ->with(['empresa', 'sucursal'])
            ->orderBy('tipo_documento')
            ->orderBy('serie')
            ->paginate($this->perPage);

        return $this->renderWithLayout('livewire.admin.series.index', [
            'series' => $series,
            'tipos' => Serie::getTiposDocumento()
        ], [
            'description' => 'Gestión de ',
        ]);
    }
}
