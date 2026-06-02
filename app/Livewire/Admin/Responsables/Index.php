<?php

namespace App\Livewire\Admin\Responsables;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Responsable;

class Index extends Component
{
    use HasDynamicLayout, WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = ['search'];

    public function render()
    {
        $responsables = Responsable::with(['estado', 'municipio', 'parroquia', 'empresa', 'sucursal'])
            ->when($this->search, function ($query) {
                $query->where('nombre_completo', 'like', '%' . $this->search . '%')
                      ->orWhere('cedula', 'like', '%' . $this->search . '%')
                      ->orWhereHas('empresa', function ($subQuery) {
                          $subQuery->where('razon_social', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('sucursal', function ($subQuery) {
                          $subQuery->where('nombre', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return $this->renderWithLayout('livewire.admin.responsables.index', [
            'responsables' => $responsables
        ], [
            'title' => 'Responsables',
            'description' => 'Listado de responsables'
        ]);
    }

    public function delete($id)
    {
        $responsable = Responsable::findOrFail($id);
        $nombre = $responsable->nombre_completo;
        $responsable->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Responsable '{$nombre}' eliminado exitosamente.",
            'duration' => 4000
        ]);
    }
    
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    public function clearFilters()
    {
        $this->reset(['search']);
    }
}