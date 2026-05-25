<?php

namespace App\Livewire\Admin\Iglesias;

use Livewire\Component;
use App\Models\Iglesia;
use App\Models\Estado;
use Illuminate\Support\Facades\DB;
use App\Traits\HasDynamicLayout;

class MapaDistribucion extends Component
{
    use HasDynamicLayout;

    public $estadosConIglesias = [];
    public $totalIglesias = 0;
    public $mapboxAccessToken;
    public $geoJsonData = null;
    public $estadoSeleccionadoId = '';

    // Propiedades para el modal de detalles
    public $mostrarModal = false;
    public $estadoSeleccionado = null;
    public $iglesiasEstado = [];


    protected $listeners = [
        'refreshMap' => '$refresh',
        'verDetallesEstado' => 'mostrarDetallesEstado',
        'mostrarModalEstado' => 'handleMostrarModalEstado'
    ];

    public function mount()
    {

        $this->mapboxAccessToken = config('services.mapbox.token');
        \Log::info('MapaDistribucion mounted', [
            'token_exists' => !empty($this->mapboxAccessToken),
            'token_preview' => substr($this->mapboxAccessToken, 0, 10) . '...'
        ]);

        // No necesitamos Mapbox para LeafletJS
        $this->mapboxAccessToken = null;
        \Log::info('MapaDistribucion mounted with LeafletJS');
        $this->loadEstadosConIglesias();
    }

    public function loadEstadosConIglesias()
    {
        \Log::info('Loading estados con iglesias...');

        // Obtener el total de extensiones en el país
        $totalIglesiasNacional = Iglesia::count();

        // Cargar estados con cantidad de extensiones
        $this->estadosConIglesias = Estado::select('estados.id', 'estados.nombre', 'estados.iso_3166_2', 'estados.latitud', 'estados.longitud')
            ->leftJoin('extensiones', 'estados.id', '=', 'iglesias.estado_id')
            ->selectRaw('COUNT(iglesias.id) as cantidad_iglesias')
            ->selectRaw('MAX(iglesias.nombre) as iglesia_ejemplo')
            ->selectRaw('AVG(iglesias.latitud) as latitud_iglesias')
            ->selectRaw('AVG(iglesias.longitud) as longitud_iglesias')
            ->groupBy('estados.id', 'estados.nombre', 'estados.iso_3166_2', 'estados.latitud', 'estados.longitud')
            ->orderBy('cantidad_iglesias', 'desc')
            ->get()
            ->map(function ($estado) use ($totalIglesiasNacional) {
                return [
                    'id' => $estado->id,
                    'nombre' => $estado->nombre,
                    'codigo' => $estado->iso_3166_2,
                    'latitud' => $estado->latitud,
                    'longitud' => $estado->longitud,
                    'cantidad' => $estado->cantidad_iglesias,
                    'iglesia_ejemplo' => $estado->iglesia_ejemplo,
                    'color' => $this->getColorPorCantidad($estado->cantidad_iglesias),
                    'latitud_iglesias' => $estado->latitud_iglesias,
                    'longitud_iglesias' => $estado->longitud_iglesias,
                    'total_iglesias' => $totalIglesiasNacional
                ];
            })
            ->toArray();


        $this->totalIglesias = array_sum(array_column($this->estadosConIglesias, 'cantidad'));


        $this->totalIglesias = $totalIglesiasNacional;

        // Preparar datos para Leaflet
        $this->prepararDatosGeoJson();


        \Log::info('Estados loaded', [
            'count' => count($this->estadosConIglesias),
            'total_iglesias' => $this->totalIglesias,
            'sample_states' => array_slice($this->estadosConIglesias, 0, 3)
        ]);
    }

    /**
     * Preparar datos GeoJSON para Leaflet
     */
    private function prepararDatosGeoJson()
    {
        // Obtener el total de extensiones en el país
        $totalIglesiasNacional = Iglesia::count();

        // Crear estructura GeoJSON básica con puntos para cada estado
        $features = [];

        foreach ($this->estadosConIglesias as $estado) {
            if ($estado['latitud'] && $estado['longitud']) {
                // Usar las coordenadas promedio de las extensiones si están disponibles
                $lat = $estado['latitud_iglesias'] ?? $estado['latitud'];
                $lng = $estado['longitud_iglesias'] ?? $estado['longitud'];

                $features[] = [
                    'type' => 'Feature',
                    'properties' => [
                        'name' => $estado['nombre'],
                        'nombre' => $estado['nombre'],
                        'cantidad_iglesias' => $estado['cantidad'],
                        'color' => $estado['color'],
                        'iglesia_ejemplo' => $estado['iglesia_ejemplo'],
                        'estado_id' => $estado['id'],
                        'latitud_centro' => $lat,
                        'longitud_centro' => $lng,
                        'total_iglesias' => $totalIglesiasNacional
                    ],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [$lng, $lat]
                    ]
                ];
            }
        }

        $this->geoJsonData = [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }

    private function getColorPorCantidad($cantidad)
    {
        if ($cantidad == 0) return '#ffffff';
        if ($cantidad <= 5) return '#e3f2fd';
        if ($cantidad <= 15) return '#bbdefb';
        if ($cantidad <= 30) return '#90caf9';
        if ($cantidad <= 50) return '#64b5f6';
        if ($cantidad <= 100) return '#42a5f5';
        return '#2196f3';
    }

    public function getGeoJsonData()
    {
        // Cargar el GeoJSON de Venezuela
        $geoJsonPath = public_path('geojson/venezuela-states.json');

        if (file_exists($geoJsonPath)) {
            $geoJson = json_decode(file_get_contents($geoJsonPath), true);




            // Recargar estados para asegurarnos de tener datos actualizados
            $this->loadEstadosConIglesias();


            // Agregar datos de cantidad de extensiones a cada estado
            $estadosMap = collect($this->estadosConIglesias)->keyBy('nombre');

            foreach ($geoJson['features'] as &$feature) {
                $stateName = $feature['properties']['name'];
                if ($estadosMap->has($stateName)) {
                    $feature['properties']['cantidad_iglesias'] = $estadosMap[$stateName]['cantidad'];
                    $feature['properties']['color'] = $estadosMap[$stateName]['color'];
                    $feature['properties']['estado_id'] = $estadosMap[$stateName]['id'];
                } else {
                    $feature['properties']['cantidad_iglesias'] = 0;
                    $feature['properties']['color'] = '#ffffff';
                    $feature['properties']['estado_id'] = null;
                }
            }

            return response()->json($geoJson);
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => []
        ]);
    }


    public function getIglesiasConCoordenadas($estadoNombre)
    {
        // Buscar el estado por nombre
        $estado = collect($this->estadosConIglesias)->firstWhere('nombre', $estadoNombre);

        if (!$estado) {
            return ['extensiones' => [], 'estado' => null];
        }

        // Obtener extensiones con coordenadas del estado
        $iglesias = Iglesia::select('id', 'nombre', 'latitud', 'longitud', 'direccion', 'telefono', 'email')
            ->with(['pastor:id,nombres,apellidos', 'ciudad:id,nombre', 'municipio:id,nombre'])
            ->where('estado_id', $estado['id'])
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->where('latitud', '!=', 0)
            ->where('longitud', '!=', 0)
            ->orderBy('nombre')
            ->get()
            ->map(function($iglesia) {
                return [
                    'id' => $iglesia->id,
                    'nombre' => $iglesia->nombre,
                    'latitud' => (float) $iglesia->latitud,
                    'longitud' => (float) $iglesia->longitud,
                    'direccion' => $iglesia->direccion,
                    'telefono' => $iglesia->telefono,
                    'email' => $iglesia->email,
                    'pastor' => $iglesia->pastor ? $iglesia->pastor->nombres . ' ' . $iglesia->pastor->apellidos : 'Sin pastor',
                    'ciudad' => $iglesia->ciudad ? $iglesia->ciudad->nombre : 'Sin ciudad',
                    'municipio' => $iglesia->municipio ? $iglesia->municipio->nombre : 'Sin municipio'
                ];
            });

        return [
            'iglesias' => $iglesias,
            'estado' => [
                'id' => $estado['id'],
                'nombre' => $estado['nombre'],
                'cantidad' => $estado['cantidad']
            ]
        ];
    }



    public function mostrarDetallesEstado($estado_id)
    {
        // Validar que el ID no sea nulo
        if (!$estado_id) {
            return;
        }

        // Buscar el estado por ID
        $estado = Estado::find($estado_id);


        if ($estado) {
            // Obtener las extensiones de este estado
            $iglesias = Iglesia::with(['pastor', 'ciudad', 'municipio', 'parroquia'])
                ->where('estado_id', $estado_id)
                ->orderBy('nombre')
                ->get();

            // Emitir evento para mostrar modal o redirigir
            $this->dispatch('mostrarModalEstado', [
                'estado' => [
                    'id' => $estado->id,
                    'nombre' => $estado->nombre,
                    'codigo' => $estado->iso_3166_2,
                    'cantidad_iglesias' => $iglesias->count()
                ],
                'iglesias' => $iglesias->toArray()
            ]);
        }
    }



    public function handleMostrarModalEstado($data)
    {
        $this->estadoSeleccionado = $data['estado'];
        $this->iglesiasEstado = $data['extensiones'];
        $this->mostrarModal = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->estadoSeleccionado = null;
        $this->iglesiasEstado = [];
    }

    public function centrarEnEstadoSeleccionado()
    {
        if ($this->estadoSeleccionadoId) {
            $this->dispatch('centrarEnEstado', $this->estadoSeleccionadoId);
        }
    }


    public function render()
    {
        return view('livewire.admin.iglesias.mapa-distribucion', [
            'estados' => $this->estadosConIglesias,
            'total' => $this->totalIglesias,
            'geoJsonData' => $this->geoJsonData
        ])->layout($this->getLayout());
    }

}

