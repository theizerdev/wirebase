<div>
    <div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="ri ri-map-pin-line me-2"></i>
                    Distribución Nacional de Extensiones
                </h3>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.location.reload()">
                        <i class="ri ri-refresh-line me-1"></i>
                        Actualizar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" wire:ignore id="selector-estado" wire:model="estadoSeleccionadoId">
                                <option value="">Seleccione un estado</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado['id'] }}">{{ $estado['nombre'] }} ({{ $estado['cantidad'] }} extensiones)</option>
                                @endforeach
                            </select>
                            <label for="selector-estado">Seleccionar Estado</label>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-primary" onclick="centrarEnEstado()">
                            <i class="ri ri-focus-3-line me-1"></i>
                            Centrar en Estado
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 col-md-7">
                            <div id="mapa-venezuela" style="height: 500px; width: 100%; border-radius: 8px; background-color: #f0f0f0;">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-5">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Leyenda</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="/img/logo.png" style="width: 12px; height: 12px; margin-right: 10px; object-fit: contain;" alt="Logo">
                                        <small>Estados con extensiones</small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div style="width: 12px; height: 12px; background-color: #ff4444; border: 2px solid #ffffff; border-radius: 50%; margin-right: 10px;"></div>
                                        <small>Ubicaciones de extensiones</small>
                                    </div>
                                    <hr class="my-2">
                                    <small class="text-muted">💡 Haz clic en los logos para ver ubicaciones</small>
                                </div>
                            </div>

                            <div class="mt-3">
                                <h6>Top 5 Estados</h6>
                                <div class="list-group list-group-flush">
                                    @foreach(array_slice($estados, 0, 5) as $index => $estado)
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div>
                                                <strong>{{ $index + 1 }}.</strong> {{ $estado['nombre'] }}
                                                <br><small class="text-muted">{{ $estado['iglesia_ejemplo'] ?? 'Sin datos' }}</small>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">{{ $estado['cantidad'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <p class="mb-0 text-muted">Cargando mapa...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src='https://api.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.css' rel='stylesheet' />

<script>
document.addEventListener('livewire:initialized', function () {
    const mapboxToken = '{{ $mapboxAccessToken }}';
    if (!mapboxToken) return;

    mapboxgl.accessToken = mapboxToken;
    const map = new mapboxgl.Map({
        container: 'mapa-venezuela',
        style: 'mapbox://styles/mapbox/light-v10',
        center: [-66.5897, 6.4238],
        zoom: 5.5
    });

    map.addControl(new mapboxgl.NavigationControl());
    map.addControl(new mapboxgl.FullscreenControl());

    map.on('load', function () {
        fetch('/geojson/venezuela-states.json')
            .then(response => response.json())
            .then(geojson => {
                const estadosData = @json($estados);

                geojson.features.forEach(function(feature) {
                    const estadoData = estadosData.find(e =>
                        e.nombre.toLowerCase().includes(feature.properties.name.toLowerCase()) ||
                        feature.properties.name.toLowerCase().includes(e.nombre.toLowerCase())
                    );

                    if (estadoData) {
                        feature.properties.cantidad_iglesias = estadoData.cantidad;
                        feature.properties.iglesia_ejemplo = estadoData.iglesia_ejemplo;
                    } else {
                        feature.properties.cantidad_iglesias = 0;
                        feature.properties.iglesia_ejemplo = null;
                    }
                });

                map.addSource('venezuela-states', {
                    type: 'geojson',
                    data: geojson
                });

                map.loadImage('/img/logo.png', function(error, image) {
                    if (error) return;

                    map.addImage('logo-marker', image);

                    const estadosConIglesias = geojson.features
                        .filter(f => f.properties.cantidad_iglesias > 0)
                        .map(feature => {
                            let coords = feature.geometry.coordinates[0];
                            let x = 0, y = 0;
                            coords.forEach(coord => {
                                x += coord[0];
                                y += coord[1];
                            });
                            x /= coords.length;
                            y /= coords.length;

                            return {
                                type: 'Feature',
                                geometry: {
                                    type: 'Point',
                                    coordinates: [x, y]
                                },
                                properties: feature.properties
                            };
                        });

                    map.addSource('extensiones-markers', {
                        type: 'geojson',
                        data: {
                            type: 'FeatureCollection',
                            features: estadosConIglesias
                        }
                    });

                    map.addLayer({
                        id: 'extensiones-symbols',
                        type: 'symbol',
                        source: 'extensiones-markers',
                        layout: {
                            'icon-image': 'logo-marker',
                            'icon-size': 0.10,
                            'icon-allow-overlap': true
                        }
                    });

                    map.on('click', 'extensiones-symbols', function (e) {
                        const feature = e.features[0];
                        const stateName = feature.properties.name;
                        
                        console.log('Click en estado:', stateName);
                        
                        Livewire.find('{{ $this->getId() }}').call('getIglesiasConCoordenadas', stateName)
                            .then(data => {
                                const extensiones = data.iglesias || [];
                                console.log('Extensiones encontradas:', iglesias.length);
                                
                                if (iglesias.length > 0) {
                                    document.querySelectorAll('.marker-iglesia-simple').forEach(m => m.remove());
                                    
                                    iglesias.forEach(extensión => {
                                        const el = document.createElement('div');
                                        el.className = 'marker-iglesia-simple';
                                        el.style.cssText = `
                                            background: red;
                                            width: 20px;
                                            height: 20px;
                                            border-radius: 50%;
                                            border: 2px solid white;
                                        `;
                                        
                                        new mapboxgl.Marker(el)
                                            .setLngLat([parseFloat(iglesia.longitud), parseFloat(iglesia.latitud)])
                                            .addTo(map);
                                            
                                        console.log('Marcador agregado:', iglesia.nombre);
                                    });
                                    
                                    const extensión = extensiones[0];
                                    map.jumpTo({
                                        center: [parseFloat(iglesia.longitud), parseFloat(iglesia.latitud)],
                                        zoom: 15
                                    });
                                    
                                    console.log('Zoom aplicado');
                                }
                            });
                    });

                    map.on('mouseenter', 'extensiones-symbols', function () {
                        map.getCanvas().style.cursor = 'pointer';
                    });

                    map.on('mouseleave', 'extensiones-symbols', function () {
                        map.getCanvas().style.cursor = '';
                    });
                    
                    const clearButton = document.createElement('button');
                    clearButton.innerHTML = '🗑️ Limpiar';
                    clearButton.className = 'btn btn-sm btn-outline-secondary';
                    clearButton.style.cssText = 'position: absolute; top: 10px; left: 10px; z-index: 1000;';
                    clearButton.onclick = function() {
                        document.querySelectorAll('.marker-iglesia-simple').forEach(m => m.remove());
                        map.jumpTo({
                            center: [-66.5897, 6.4238],
                            zoom: 5.5
                        });
                    };
                    document.getElementById('mapa-venezuela').appendChild(clearButton);
                });
            });
    });
});
</script>
@endpush
</div>
