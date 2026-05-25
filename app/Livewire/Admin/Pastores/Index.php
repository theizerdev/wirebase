<?php

namespace App\Livewire\Admin\Pastores;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pastor;
use App\Traits\Exportable;
use App\Traits\HasDynamicLayout;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout;

    public $search = '';
    public $status = '';
    public $zona = '';
    public $sortBy = 'nombres';
    public $sortDirection = 'asc';
    public $perPage = 12;
    public $viewMode = 'grid'; // 'grid' o 'list'

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'zona' => ['except' => ''],
        'sortBy' => ['except' => 'nombres'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 12],
        'viewMode' => ['except' => 'grid']
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingZona()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
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

    public function toggleStatus($pastorId)
    {
        $pastor = Pastor::findOrFail($pastorId);

        $pastor->status = !$pastor->status;
        $pastor->save();

        session()->flash('message', $pastor->status ? 'Pastor activado exitosamente.' : 'Pastor desactivado exitosamente.');
    }

    public function deletePastor($pastorId)
    {
        $pastor = Pastor::findOrFail($pastorId);

        // Verificar si tiene extensiones asociadas
        if ($pastor->iglesias()->exists()) {
            session()->flash('error', 'No se puede eliminar el pastor porque tiene extensiones asociadas.');
            return;
        }

        try {
            $pastor->delete();
            session()->flash('message', 'Pastor eliminado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el pastor: ' . $e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->zona = '';
        $this->sortBy = 'nombres';
        $this->sortDirection = 'asc';
        $this->perPage = 10;
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    protected function getExportQuery()
    {
        return $this->getBaseQuery();
    }

    protected function getExportHeaders(): array
    {
        return [
            'ID',
            'Código',
            'Nombres',
            'Apellidos',
            'Documento',
            'Nivel Ministerial',
            'Zona',
            'Distrito',
            'Teléfono',
            'Email',
            'Status'
        ];
    }

    protected function formatExportRow($pastor): array
    {
        return [
            $pastor->id,
            $pastor->codigo,
            $pastor->nombres,
            $pastor->apellidos,
            $pastor->documento,
            $pastor->nivel_ministerial,
            $pastor->zona,
            $pastor->distrito,
            $pastor->telefono_hab,
            $pastor->email,
            $pastor->status ? 'Activo' : 'Inactivo'
        ];
    }

    public function descargarPlanillasZona()
    {
        if (empty($this->zona)) {
            session()->flash('error', 'Por favor seleccione una zona primero.');
            return;
        }

        try {
            // Ejecutar el comando artisan
            \Illuminate\Support\Facades\Artisan::call('planillas:zona', [
                'zona' => $this->zona
            ]);

            // Obtener la salida del comando para extraer el nombre del archivo
            $output = \Illuminate\Support\Facades\Artisan::output();

            // Buscar el nombre del archivo en la salida
            preg_match('/public\/storage\/(Planillas_Zona_[^.]+\.zip)/', $output, $matches);

            if (isset($matches[1])) {
                $FileName = $matches[1];
                $FilePath = storage_path('app/public/' . $FileName);

                if (file_exists($FilePath)) {
                    return response()->download($FilePath)->deleteFileAfterSend(true);
                }
            }

            session()->flash('error', 'No se pudo generar o encontrar el archivo ZIP. Asegúrese de que hay pastores en esta zona.');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar las planillas: ' . $e->getMessage());
        }
    }

    private function getBaseQuery()
    {
        return Pastor::with(['ciudad:id,nombre,estado_id', 'estado:id,nombre', 'municipio:id,nombre,estado_id', 'parroquia:id,nombre,municipio_id'])
            ->when($this->search, function ($query) {
                $query->where('nombres', 'like', '%' . $this->search . '%')
                    ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                    ->orWhere('documento', 'like', '%' . $this->search . '%')
                    ->orWhere('codigo', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status == 'activo' ? 1 : 0);
            })
            ->when($this->zona !== '', function ($query) {
                $query->where('zona', $this->zona);
            })
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function render()
    {
        // Intentar capturar errores de la consulta
        try {
            $pastores = $this->getBaseQuery()->paginate($this->perPage);
        } catch (\Exception $e) {
            // Loggear el error para diagnóstico
            \Log::error('Error en la consulta de pastores: ' . $e->getMessage());
            // Devolver una colección vacía en caso de error
            $pastores = collect([]);
        }
       
        // Obtener las zonas como colección separada
        $zonas = Pastor::select('zona')->whereNotNull('zona')->distinct()->orderBy('zona')->pluck('zona');
        
        // Calcular estadísticas
        $totalPastores = Pastor::count();
        $pastoresActivos = Pastor::where('status', true)->count();
        $pastoresInactivos = Pastor::where('status', false)->count();
        $totalZonas = $zonas->count();

        return $this->renderWithLayout('livewire.admin.pastores.index', [
            'pastores' => $pastores,
            'zonas' => $zonas,
            'totalPastores' => $totalPastores,
            'pastoresActivos' => $pastoresActivos,
            'pastoresInactivos' => $pastoresInactivos,
            'totalZonas' => $totalZonas
        ], [
            'title' => 'Lista de Pastores',
            'description' => 'Administra los pastores del sistema'
        ]);
    }
}