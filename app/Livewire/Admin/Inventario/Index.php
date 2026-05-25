<?php

namespace App\Livewire\Admin\Inventario;

use App\Models\InventarioIglesia;
use App\Models\Iglesia;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Traits\HasDynamicLayout;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination;
    use HasDynamicLayout;
    use Exportable;

    public string $search = '';
    public string $iglesiaFilter = '';
    public string $category = '';
    public string $condition = '';
    public string $sortBy = 'fecha_adquisicion';
    public string $sortDirection = 'desc';
    public int $perPage = 12;

    // Estadísticas
    public array $stats = [];

    // Categorías predefinidas para inventario de iglesias
    public const CATEGORIAS_INVENTARIO = [
        'Mobiliario' => 'Mobiliario',
        'Equipos de Sonido' => 'Equipos de Sonido',
        'Equipos de Video' => 'Equipos de Video',
        'Instrumentos Musicales' => 'Instrumentos Musicales',
        'Equipos Eléctricos' => 'Equipos Eléctricos',
        'Decoración' => 'Decoración',
        'Utensilios de Cocina' => 'Utensilios de Cocina',
        'Herramientas' => 'Herramientas',
        'Vestimenta Litúrgica' => 'Vestimenta Litúrgica',
        'Libros y Material Educativo' => 'Libros y Material Educativo',
        'Equipos de Limpieza' => 'Equipos de Limpieza',
        'Aire Acondicionado' => 'Aire Acondicionado',
        'Computadoras y Tecnología' => 'Computadoras y Tecnología',
        'Otros' => 'Otros',
    ];

    public function mount(): void
    {
        $this->loadStats();
    }

    protected function loadStats(): void
    {
        $query = InventarioIglesia::query();

        $this->stats = [
            'total_items' => (clone $query)->count(),
            'total_valor' => (clone $query)->sum('valor') ?? 0,
            'total_iglesias' => (clone $query)->distinct('iglesia_id')->count('iglesia_id'),
            'categorias_unicas' => (clone $query)->distinct('categoria')->whereNotNull('categoria')->count('categoria'),
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingIglesiaFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function updatingCondition(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->iglesiaFilter = '';
        $this->category = '';
        $this->condition = '';
        $this->sortBy = 'fecha_adquisicion';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function setSortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function deleteItem(int $itemId): void
    {
        $item = InventarioIglesia::find($itemId);

        if (!$item) {
            session()->flash('error', 'Ítem no encontrado.');
            return;
        }

        $itemName = $item->nombre;
        $item->delete();

        $this->loadStats();
        session()->flash('message', "Ítem '{$itemName}' eliminado correctamente.");
    }

    protected function getExportQuery()
    {
        return InventarioIglesia::with('iglesia')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('categoria', 'like', '%' . $this->search . '%')
                        ->orWhere('condicion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->iglesiaFilter !== '', function ($query) {
                $query->where('iglesia_id', $this->iglesiaFilter);
            })
            ->when($this->category !== '', function ($query) {
                $query->where('categoria', 'like', '%' . $this->category . '%');
            })
            ->when($this->condition !== '', function ($query) {
                $query->where('condicion', 'like', '%' . $this->condition . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    protected function getExportHeaders(): array
    {
        return [
            'ID',
            'Extensión',
            'Nombre',
            'Categoría',
            'Cantidad',
            'Valor',
            'Tipo de Bien',
            'Condición',
            'Fecha Adquisición',
            'Descripción'
        ];
    }

    protected function formatExportRow($item): array
    {
        return [
            $item->id,
            $item->iglesia ? $item->iglesia->nombre : '-',
            $item->nombre,
            $item->categoria ?? '-',
            $item->cantidad,
            $item->valor ?? 0,
            $item->tipo_bien ?? '-',
            $item->condicion ?? '-',
            $item->fecha_adquisicion ? \Carbon\Carbon::parse($item->fecha_adquisicion)->format('d/m/Y') : '-',
            $item->descripcion ?? '-'
        ];
    }

    public function getExportFileName(): string
    {
        return 'inventario_general_' . now()->format('Y-m-d');
    }

    public function render()
    {
        $query = InventarioIglesia::with('iglesia');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('categoria', 'like', '%' . $this->search . '%')
                    ->orWhere('condicion', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->iglesiaFilter !== '') {
            $query->where('iglesia_id', $this->iglesiaFilter);
        }

        if ($this->category !== '') {
            $query->where('categoria', 'like', '%' . $this->category . '%');
        }

        if ($this->condition !== '') {
            $query->where('condicion', 'like', '%' . $this->condition . '%');
        }

        // Obtener condiciones únicas para los filtros
        $conditions = InventarioIglesia::query()
            ->distinct('condicion')
            ->whereNotNull('condicion')
            ->pluck('condicion')
            ->filter()
            ->values()
            ->toArray();

        // Obtener iglesias para el filtro
        $iglesias = Iglesia::activas()
            ->orderBy('nombre')
            ->get();

        $items = $query
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.inventario.index', [
            'items' => $items,
            'iglesias' => $iglesias,
            'categories' => self::CATEGORIAS_INVENTARIO,
            'conditions' => $conditions,
        ])->layout($this->getLayout());
    }
}
