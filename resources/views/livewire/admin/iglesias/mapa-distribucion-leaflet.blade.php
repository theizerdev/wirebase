<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ri ri-map-pin-line me-2"></i>Distribución Nacional de Extensiones
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-3">Total: {{ $total }} Extensiones</span>
                        <button wire:click="loadEstadosConIglesias" class="btn btn-sm btn-outline-primary">
                            <i class="ri ri-refresh-line"></i> Actualizar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 col-md-7">
                            <div id="mapa-venezuela-leaflet" style="height: 500px; width: 100%; border-radius: 8px; position: relative; min-height: 500px; background-color: #f0f0f0;">
                                <div id="map-loading-leaflet" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000; background: rgba(255,255,255,0.9); padding: 20px; border-radius: 8px; text-align: center;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2 mb-0">Cargando mapa...</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-5">
                            <!-- Leyenda Interactiva -->
                            <div class="card border">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Leyenda</h6>
                                    <button id="toggle-legend" class="btn btn-sm btn-outline-secondary" title="Alternar leyenda">
                                        <i class="ri ri-eye-line"></i>
                                    </button>
                                </div>
                                <div class="card-body" id="legend-content">
                                    <div class="legend-item d-flex align-items-center mb-2" data-range="0">
                                        <div class="legend-color" style="width: 20px; height: 20px; background-color: #ffffff; border: 1px solid #ddd; margin-right: 10px; border-radius: 3px;"></div>
                                        <small>Sin extensiones</small>
                                        <span class="badge bg-secondary ms-auto" id="count-0">0</span>
                                    </div>
                                    <div class="legend-item d-flex align-items-center mb-2" data-range="1-5">
                                        <div class="legend-color" style="width: 20px; height: 20px; background-color: #e3f2fd; margin-right: 10px; border-radius: 3px;"></div>
                                        <small>1-5 extensiones</small>
                                        <span class="badge bg-secondary ms-auto" id="count-1-5">0</span>
                                    </div>
                                    <div class="legend-item d-flex align-items-center mb-2" data-range="6-15">
                                        <div class="legend-color" style="width: 20px; height: 20px; background-color: #bbdefb; margin-right: 10px; border-radius: 3px;"></div>
                                        <small>6-15 extensiones</small>
                                        <span class="badge bg-secondary ms-auto" id="count-6-15">0</span>
                                    </div>
                                    <div class="legend-item d-flex align-items-center mb-2" data-range="16-30">
                                        <div class="legend-color" style="width: 20px; height: 20px; background-color: #90caf9; margin-right: 10px; border-radius: 3px;"></div>
                                        <small>16-30 extensiones</small>
                                        <span class="badge bg-secondary ms-auto" id="count-16-30">0</span>
                                    </div>
                                    <div class="legend-item d-flex align-items-center mb-2" data-range="31-50">
                                        <div class="legend-color" style="width: 20px; height: 20px; background-color: #64b5f6; margin-right: 10px; border-radius: 3px;"></div>
                                        <small>31-50 extensiones</small>
                                        <span class="badge bg-secondary ms-auto" id="count-31-50">0</span>
                                    </div>
                                    <div class="legend-item d-flex align-items-center mb-2" data-range="51-100">
                                        <div class="legend-color" style="width: 20px; height: 20px; background-color: #42a5f5; margin-right: 10px; border-radius: 3px;"></div>
                                        <small>51-100 extensiones</small>
                                        <span class="badge bg-secondary ms-auto" id="count-51-100">0</span>
                                    </div>
                                    <div class="legend-item d-flex align-items-center" data-range="100+">
                                        <div class="legend-color" style="width: 20px; height: 20px; background-color: #2196f3; margin-right: 10px; border-radius: 3px;"></div>
                                        <small>Más de 100 extensiones</small>
                                        <span class="badge bg-secondary ms-auto" id="count-100+">0</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Estadísticas -->
                            <div class="mt-3">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Estadísticas Rápidas</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <small>Estados con iglesias:</small>
                                            <strong id="estados-con-iglesias">0</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <small>Promedio por estado:</small>
                                            <strong id="promedio-iglesias">0</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small>Estado con más iglesias:</small>
                                            <strong id="estado-top">-</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Top 5 Estados -->
                            <div class="mt-3">
                                <h6>Top 5 Estados</h6>
                                <div class="list-group list-group-flush" id="top-estados">
                                    @foreach(array_slice($estados, 0, 5) as $index => $estado)
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 estado-item" 
                                             data-estado="{{ strtolower($estado['nombre']) }}" 
                                             data-cantidad="{{ $estado['cantidad'] }}"
                                             style="cursor: pointer;">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .leaflet-popup-content-wrapper {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .leaflet-popup-content {
            margin: 12px;
            min-width: 200px;
        }
        
        .legend-item {
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .legend-item:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
        }
        
        .legend-item.active {
            background-color: #e3f2fd;
            border-left: 3px solid #2196f3;
        }
        
        .estado-item {
            transition: all 0.2s ease;
            border-radius: 4px;
        }
        
        .estado-item:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
        }
        
        .estado-item.highlighted {
            background-color: #e3f2fd;
            border-left: 3px solid #2196f3;
        }
        
        .legend-color {
            transition: transform 0.2s ease;
        }
        
        .legend-item:hover .legend-color {
            transform: scale(1.1);
        }
        
        .leaflet-marker-icon {
            transition: all 0.2s ease;
        }
        
        .leaflet-marker-icon:hover {
            transform: scale(1.1);
        }
        
        .leaflet-marker-icon.highlighted {
            transform: scale(1.2);
            z-index: 1000 !important;
        }
        
        #legend-content {
            transition: all 0.3s ease;
        }
        
        #legend-content.collapsed {
            display: none;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        
        .stats-card .card-header {
            background: rgba(255,255,255,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .stats-card .card-body {
            background: transparent;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            console.log('=== INICIANDO CARGA DEL MAPA CON LEAFLET ===');
            
            // Verificar contenedor del mapa
            const mapContainer = document.getElementById('mapa-venezuela-leaflet');
            if (!mapContainer) {
                console.error('Map container not found!');
                return;
            }
            
            // Datos de estados desde Livewire
            const estadosData = @json($estados);
            const geoJsonData = @json($geoJsonData);
            
            console.log('Estados data:', estadosData);
            console.log('GeoJSON data:', geoJsonData);
            
            try {
                // Inicializar el mapa de Leaflet
                const map = L.map('mapa-venezuela-leaflet', {
                    center: [6.4238, -66.5897], // Centro de Venezuela
                    zoom: 6,
                    zoomControl: true,
                    attributionControl: true
                });
                
                // Agregar capa de teselas (tiles)
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 18,
                    minZoom: 5
                }).addTo(map);
                
                // Agregar controles adicionales
                L.control.scale().addTo(map);
                
                // Crear capa de grupo para marcadores
                const markersGroup = L.layerGroup().addTo(map);
                
                // Función para obtener color según cantidad de extensiones
                function getColorByQuantity(quantity) {
                    if (quantity === 0) return '#ffffff';
                    if (quantity <= 5) return '#e3f2fd';
                    if (quantity <= 15) return '#bbdefb';
                    if (quantity <= 30) return '#90caf9';
                    if (quantity <= 50) return '#64b5f6';
                    if (quantity <= 100) return '#42a5f5';
                    return '#2196f3';
                }
                
                // Función para crear ícono personalizado
                function createCustomIcon(color, quantity) {
                    const size = Math.min(20 + (quantity * 0.5), 40); // Tamaño dinámico
                    
                    return L.divIcon({
                        className: 'custom-marker-icon',
                        html: `
                            <div style="
                                background-color: ${color};
                                border: 2px solid #1976d2;
                                border-radius: 50%;
                                width: ${size}px;
                                height: ${size}px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: ${quantity > 30 ? 'white' : '#1976d2'};
                                font-weight: bold;
                                font-size: ${Math.max(10, size * 0.4)}px;
                                box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                                transition: all 0.2s ease;
                            ">
                                ${quantity > 0 ? quantity : '•'}
                            </div>
                        `,
                        iconSize: [size, size],
                        iconAnchor: [size/2, size/2],
                        popupAnchor: [0, -size/2]
                    });
                }
                
                // Función para crear popup
                function createPopupContent(estado) {
                    const color = getColorByQuantity(estado.cantidad);
                    const porcentaje = ((estado.cantidad / {{ $total }}) * 100).toFixed(1);
                    
                    return `
                        <div style="min-width: 220px;">
                            <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                <div style="
                                    width: 16px; 
                                    height: 16px; 
                                    background-color: ${color}; 
                                    border: 1px solid #1976d2; 
                                    border-radius: 3px; 
                                    margin-right: 8px;
                                "></div>
                                <h6 style="margin: 0; color: #1976d2;"><strong>${estado.nombre}</strong></h6>
                            </div>
                            <div style="margin-bottom: 8px;">
                                <p style="margin: 0; font-size: 14px;">
                                    <strong>${estado.cantidad}</strong> extensión${estado.cantidad !== 1 ? 's' : ''}
                                </p>
                                <small style="color: #666;">${porcentaje}% del total nacional</small>
                            </div>
                            ${estado.iglesia_ejemplo ? `
                                <div style="margin-bottom: 12px;">
                                    <small style="color: #666;">Ejemplo:</small><br>
                                    <small style="font-style: italic;">${estado.iglesia_ejemplo}</small>
                                </div>
                            ` : ''}
                            <div style="display: flex; gap: 8px;">
                                <button 
                                    class="btn btn-primary btn-sm" 
                                    onclick="Livewire.dispatch('verDetallesEstado', { estado_nombre: '${estado.nombre}' })"
                                    style="flex: 1;"
                                >
                                    Ver detalles
                                </button>
                                <button 
                                    class="btn btn-outline-primary btn-sm" 
                                    onclick="highlightEstado('${estado.nombre.toLowerCase()}')"
                                    style="flex: 1;"
                                >
                                    Resaltar
                                </button>
                            </div>
                        </div>
                    `;
                }
                
                // Agregar marcadores para cada estado
                const legendCounts = {
                    '0': 0, '1-5': 0, '6-15': 0, '16-30': 0, '31-50': 0, '51-100': 0, '100+': 0
                };
                
                estadosData.forEach(function(estado) {
                    if (estado.latitud && estado.longitud) {
                        const color = getColorByQuantity(estado.cantidad);
                        const icon = createCustomIcon(color, estado.cantidad);
                        
                        const marker = L.marker([estado.latitud, estado.longitud], { icon: icon })
                            .addTo(markersGroup)
                            .bindPopup(createPopupContent(estado));
                        
                        // Almacenar referencia al marcador
                        estado.marker = marker;
                        
                        // Actualizar contador de leyenda
                        if (estado.cantidad === 0) legendCounts['0']++;
                        else if (estado.cantidad <= 5) legendCounts['1-5']++;
                        else if (estado.cantidad <= 15) legendCounts['6-15']++;
                        else if (estado.cantidad <= 30) legendCounts['16-30']++;
                        else if (estado.cantidad <= 50) legendCounts['31-50']++;
                        else if (estado.cantidad <= 100) legendCounts['51-100']++;
                        else legendCounts['100+']++;
                    }
                });
                
                // Actualizar contadores de leyenda
                Object.keys(legendCounts).forEach(range => {
                    const element = document.getElementById(`count-${range}`);
                    if (element) {
                        element.textContent = legendCounts[range];
                    }
                });
                
                // Calcular y actualizar estadísticas
                const estadosConIglesias = estadosData.filter(e => e.cantidad > 0).length;
                const promedio = estadosConIglesias > 0 ? Math.round({{ $total }} / estadosConIglesias) : 0;
                const estadoTop = estadosData.reduce((max, estado) => 
                    estado.cantidad > max.cantidad ? estado : max, estadosData[0]
                );
                
                document.getElementById('estados-con-iglesias').textContent = estadosConIglesias;
                document.getElementById('promedio-iglesias').textContent = promedio;
                document.getElementById('estado-top').textContent = estadoTop ? estadoTop.nombre : '-';
                
                // Función para resaltar estado
                window.highlightEstado = function(nombreEstado) {
                    // Remover resaltados anteriores
                    document.querySelectorAll('.estado-item').forEach(item => {
                        item.classList.remove('highlighted');
                    });
                    
                    // Remover resaltados de marcadores
                    document.querySelectorAll('.leaflet-marker-icon').forEach(icon => {
                        icon.classList.remove('highlighted');
                    });
                    
                    // Resaltar en la lista
                    const listItem = document.querySelector(`[data-estado="${nombreEstado.toLowerCase()}"]`);
                    if (listItem) {
                        listItem.classList.add('highlighted');
                        listItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                    
                    // Resaltar marcador y abrir popup
                    const estado = estadosData.find(e => e.nombre.toLowerCase() === nombreEstado.toLowerCase());
                    if (estado && estado.marker) {
                        const iconElement = estado.marker.getElement();
                        if (iconElement) {
                            iconElement.classList.add('highlighted');
                        }
                        
                        // Centrar mapa y abrir popup
                        map.setView([estado.latitud, estado.longitud], 8);
                        estado.marker.openPopup();
                    }
                };
                
                // Funcionalidad de leyenda interactiva
                document.querySelectorAll('.legend-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const range = this.dataset.range;
                        
                        // Alternar clase activa
                        this.classList.toggle('active');
                        
                        // Filtrar marcadores
                        const isActive = this.classList.contains('active');
                        
                        estadosData.forEach(estado => {
                            let shouldShow = true;
                            
                            if (isActive) {
                                // Si hay filtros activos, mostrar solo los que coinciden
                                const activeRanges = Array.from(document.querySelectorAll('.legend-item.active')).map(item => item.dataset.range);
                                
                                if (activeRanges.length > 0) {
                                    const quantity = estado.cantidad;
                                    shouldShow = false;
                                    
                                    activeRanges.forEach(activeRange => {
                                        if (activeRange === '0' && quantity === 0) shouldShow = true;
                                        else if (activeRange === '1-5' && quantity >= 1 && quantity <= 5) shouldShow = true;
                                        else if (activeRange === '6-15' && quantity >= 6 && quantity <= 15) shouldShow = true;
                                        else if (activeRange === '16-30' && quantity >= 16 && quantity <= 30) shouldShow = true;
                                        else if (activeRange === '31-50' && quantity >= 31 && quantity <= 50) shouldShow = true;
                                        else if (activeRange === '51-100' && quantity >= 51 && quantity <= 100) shouldShow = true;
                                        else if (activeRange === '100+' && quantity > 100) shouldShow = true;
                                    });
                                }
                            }
                            
                            if (shouldShow && estado.marker) {
                                estado.marker.addTo(markersGroup);
                            } else if (estado.marker) {
                                markersGroup.removeLayer(estado.marker);
                            }
                        });
                    });
                });
                
                // Funcionalidad de clic en lista de estados
                document.querySelectorAll('.estado-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const estadoNombre = this.dataset.estado;
                        highlightEstado(estadoNombre);
                    });
                });
                
                // Alternar visibilidad de leyenda
                document.getElementById('toggle-legend').addEventListener('click', function() {
                    const legendContent = document.getElementById('legend-content');
                    const icon = this.querySelector('i');
                    
                    legendContent.classList.toggle('collapsed');
                    icon.classList.toggle('ri-eye-line');
                    icon.classList.toggle('ri-eye-off-line');
                });
                
                // Escuchar eventos de Livewire
                Livewire.on('refreshMap', function() {
                    location.reload();
                });
                
                // Ocultar indicador de carga
                const loadingElement = document.getElementById('map-loading-leaflet');
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }
                
                console.log('✅ Mapa de Leaflet cargado exitosamente!');
                console.log(`📍 ${estadosData.length} estados cargados`);
                console.log(`🏠 {{ $total }} extensiones en total`);
                
            } catch (error) {
                console.error('❌ Error al inicializar el mapa:', error);
                const loadingElement = document.getElementById('map-loading-leaflet');
                if (loadingElement) {
                    loadingElement.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Error al cargar el mapa:</strong><br>
                            ${error.message}<br>
                            <button class="btn btn-sm btn-danger mt-2" onclick="location.reload()">
                                <i class="ri ri-refresh-line"></i> Reintentar
                            </button>
                        </div>
                    `;
                }
            }
        });
        
        // Función global para manejar errores
        window.addEventListener('error', function(e) {
            console.error('Error global:', e.error);
        });
        
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Promesa no manejada:', e.reason);
        });
    </script>
    @endpush
</div>