@push('styles')
<style>
    .hover-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .bg-label-primary {
        background: linear-label(135deg, #667eea 0%, #764ba2 100%);
    }
    .bg-label-success {
        background: linear-label(135deg, #56ab2f 0%, #a8e6cf 100%);
    }
    .bg-label-warning {
        background: linear-label(135deg, #f093fb 0%, #f5576c 100%);
    }
    .bg-label-info {
        background: linear-label(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .kpi-content h3 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .progress-sm {
        height: 4px;
    }
    .apexcharts-tooltip {
        background: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid #e0e0e0 !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    .chart-container {
        position: relative;
        background: #fff;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    @media (max-width: 768px) {
        .kpi-content h3 {
            font-size: 1.5rem;
        }
    }
    /* Estilos para el mapa */
    #mapaVenezuela {
        height: 500px;
        width: 100%;
        border-radius: 8px;
        position: relative;
    }
    .mapa-leyenda {
        position: absolute;
        bottom: 20px;
        left: 20px;
        background: rgba(255, 255, 255, 0.95);
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        z-index: 1001;
        border: 1px solid rgba(0,0,0,0.1);
        backdrop-filter: blur(2px);
        transition: opacity 0.3s ease, transform 0.3s ease;
        opacity: 0;
        transform: translateY(10px);
    }
    .mapa-leyenda.show {
        opacity: 1;
        transform: translateY(0);
    }
    .mapa-leyenda h6 {
        margin-bottom: 10px;
        font-weight: 600;
        color: #2d3748;
    }
    .leyenda-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        font-size: 12px;
        color: #4a5568;
    }
    .leyenda-color {
        width: 20px;
        height: 12px;
        margin-right: 8px;
        border-radius: 2px;
    }
    .leaflet-popup {
        max-width: 300px !important;
    }
    .leaflet-popup-content {
        padding: 5px !important;
        border-radius: 8px !important;
        margin: 10px !important;
    }
    .estado-popup h6 {
        margin-bottom: 8px;
        color: #2d3748;
    }
    .estado-popup .badge {
        font-size: 14px;
        padding: 5px 10px;
    }
    .top-estados-list {
        max-height: 200px;
        overflow-y: auto;
    }
    .top-estados-list .list-group-item {
        padding: 8px 12px;
        border: none;
        border-bottom: 1px solid #f1f5f9;
    }
    .top-estados-list .list-group-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

<div>

    <!-- Filtros y Controles -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">
                                <i class="ri ri-dashboard-line text-primary"></i>
                                Dashboard Principal
                            </h5>
                            <small class="text-muted">Estadísticas generales del sistema</small>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="btn-group" role="group">
                                <label class="btn btn-outline-primary btn-sm" for="yearSelect">
                                    <i class="ri ri-calendar-line"></i> Año:
                                </label>
                                <select wire:model="selectedYear" class="form-select form-select-sm d-inline-block w-auto" id="yearSelect">
                                    @foreach($availableYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button wire:click="cargarDatos" class="btn btn-outline-secondary btn-sm ms-2" {{ $isLoading ? 'disabled' : '' }}>
                                <i class="ri ri-refresh-line {{ $isLoading ? 'ri-spin' : '' }}"></i>
                                Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- KPIs Principales -->
<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100 hover-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ri ri-group-line ri-24px"></i>
                        </span>
                    </div>

                </div>
                <div>
                    <span class="fw-semibold d-block mb-1 text-muted">Total Pastores en general</span>
                    <h3 class="card-title mb-2 fw-bold text-primary">{{ number_format($totalPastores) }}</h3>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2">{{ $pastoresActivos }} activos</span>
                        <small class="text-muted">{{ $totalPastores > 0 ? round(($pastoresActivos / $totalPastores) * 100, 1) : 0 }}% activos</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $totalPastores > 0 ? ($pastoresActivos / $totalPastores) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100 hover-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="ri ri-building-line ri-24px"></i>
                        </span>
                    </div>

                </div>
                <div class="kpi-content">
                    <span class="fw-semibold d-block mb-1 text-muted">Total Extensiones</span>
                    <h3 class="card-title mb-2 fw-bold text-success">{{ number_format($totalIglesias) }}</h3>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2">{{ $iglesiasActivas }} activas</span>
                        <small class="text-muted">{{ $totalIglesias > 0 ? round(($iglesiasActivas / $totalIglesias) * 100, 1) : 0 }}% activas</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <div class="progress progress-sm">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalIglesias > 0 ? ($iglesiasActivas / $totalIglesias) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100 hover-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="ri ri-user-heart-line ri-24px"></i>
                        </span>
                    </div>

                </div>
                <div class="kpi-content">
                    <span class="fw-semibold d-block mb-1 text-muted">Miembros Activos</span>
                    <h3 class="card-title mb-2 fw-bold text-warning">{{ number_format($miembrosActivos) }}</h3>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-warning me-2">Promedio</span>
                        <small class="text-muted">{{ $totalIglesias > 0 ? round($miembrosActivos / $totalIglesias) : 0 }} por extensión</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <div class="progress progress-sm">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 75%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100 hover-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="ri ri-map-pin-line ri-24px"></i>
                        </span>
                    </div>

                </div>
                <div class="kpi-content">
                    <span class="fw-semibold d-block mb-1 text-muted">Campos Blancos</span>
                    <h3 class="card-title mb-2 fw-bold text-info">{{ number_format($camposBlancos) }}</h3>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-info me-2">Oportunidades</span>
                        <small class="text-muted">Nuevas obras misioneras</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <div class="progress progress-sm">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 60%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-md-6 mb-4">
        <div class="card h-100 hover-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ri ri-group-line ri-24px"></i>
                        </span>
                    </div>

                </div>
                <div>
                    <span class="fw-semibold d-block mb-1 text-muted">Total Pastores Reconocidos</span>
                    <h3 class="card-title mb-2 fw-bold text-primary">{{ number_format($totalPastoresReconocidos) }}</h3>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2">{{ $pastoresActivos }} activos</span>
                        <small class="text-muted">{{ $totalPastoresReconocidos > 0 ? round(($totalPastoresReconocidos / $pastoresActivos) * 100, 1) : 0 }}% activos</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $totalPastores > 0 ? ($pastoresActivos / $totalPastores) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100 hover-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ri ri-group-line ri-24px"></i>
                        </span>
                    </div>

                </div>
                <div>
                    <span class="fw-semibold d-block mb-1 text-muted">Total Colaboradores</span>
                    <h3 class="card-title mb-2 fw-bold text-primary">{{ number_format($totalColaboradores) }}</h3>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2">{{ $pastoresActivos }} activos</span>
                        <small class="text-muted">{{ $totalColaboradores > 0 ? round(($totalColaboradores / $pastoresActivos) * 100, 1) : 0 }}% activos</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $totalPastores > 0 ? ($pastoresActivos / $totalPastores) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100 hover-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ri ri-group-line ri-24px"></i>
                        </span>
                    </div>

                </div>
                <div>
                    <span class="fw-semibold d-block mb-1 text-muted">Total Pastores Laico</span>
                    <h3 class="card-title mb-2 fw-bold text-primary">{{ number_format($totalLaico) }}</h3>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2">{{ $pastoresActivos }} activos</span>
                        <small class="text-muted">{{ $totalLaico > 0 ? round(($totalLaico / $pastoresActivos) * 100, 1) : 0 }}% activos</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $totalPastores > 0 ? ($pastoresActivos / $totalPastores) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100 hover-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ri ri-group-line ri-24px"></i>
                        </span>
                    </div>

                </div>
                <div>
                    <span class="fw-semibold d-block mb-1 text-muted">Total de Pastores Licenciados</span>
                    <h3 class="card-title mb-2 fw-bold text-primary">{{ number_format($totalLicenciados) }}</h3>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2">{{ $pastoresActivos }} activos</span>
                        <small class="text-muted">{{ $totalLicenciados > 0 ? round(($totalLicenciados / $pastoresActivos) * 100, 1) : 0 }}% activos</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $totalPastores > 0 ? ($pastoresActivos / $totalPastores) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100 hover-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ri ri-group-line ri-24px"></i>
                        </span>
                    </div>

                </div>
                <div>
                    <span class="fw-semibold d-block mb-1 text-muted">Total Pastores Ministro Ordenado</span>
                    <h3 class="card-title mb-2 fw-bold text-primary">{{ number_format($totalMinistroOrdenado) }}</h3>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2">{{ $pastoresActivos }} activos</span>
                        <small class="text-muted">{{ $totalMinistroOrdenado > 0 ? round(($totalMinistroOrdenado / $pastoresActivos) * 100, 1) : 0 }}% activos</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $totalPastores > 0 ? ($pastoresActivos / $totalPastores) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Pastores -->
<div class="row">
    <div class="col-12">
        <h4 class="mb-3">
            <i class="ri ri-group-line text-primary"></i>
            Estadísticas de Pastores
        </h4>
    </div>

    <!-- Pastores por Género -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Pastores por Género</h5>
            </div>
            <div class="card-body text-center">
                @if($pastoresPorGenero->count() > 0)
                    <div id="chartPastoresGenero"></div>
                @else
                    <p class="text-muted">No hay datos de género disponibles</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Pastores por Estado Civil -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Pastores por Estado Civil</h5>
            </div>
            <div class="card-body text-center">
                    @if($pastoresPorEstadoCivil->count() > 0)
                        <div id="chartPastoresEstadoCivil"></div>
                    @else
                        <p class="text-muted">No hay datos de estado civil disponibles</p>
                    @endif
                </div>
        </div>
    </div>

    <!-- Pastores por Rango de Edad -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Pastores por Rango de Edad</h5>
            </div>
            <div class="card-body text-center">

                    <div id="chartPastoresEdad"></div>

            </div>
        </div>
    </div>

    <!-- Pastores por Grado Ministerial -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Pastores por Grado Ministerial</h5>
            </div>
            <div class="card-body text-center">
                @if($pastoresPorGradoMinisterial->count() > 0)
                    <div id="chartPastoresGrado"></div>
                @else
                    <p class="text-muted">No hay datos de grado ministerial disponibles</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Sección de Extensiones -->
<div class="row">
    <div class="col-12">
        <h4 class="mb-3">
            <i class="ri ri-building-line text-success"></i>
            Estadísticas de Extensiones
        </h4>
    </div>

    <!-- Extensiones por Tipo de Local -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Iglesias por Tipo de Local</h5>
            </div>
            <div class="card-body text-center">
                    @if($iglesiasPorTipoLocal->count() > 0)
                        <div id="chartIglesiasTipo"></div>
                    @else
                        <p class="text-muted">No hay datos de tipos de local disponibles</p>
                    @endif
                </div>
        </div>
    </div>

    <!-- Extensiones por Estado -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Iglesias por Estado</h5>
            </div>
            <div class="card-body text-center">
                @if($iglesiasPorEstado->count() > 0)
                    <div id="chartIglesiasEstado"></div>
                @else
                    <p class="text-muted">No hay datos de extensiones por estado disponibles</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Mapa de Venezuela -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ri ri-map-pin-line text-primary"></i>
                    Distribución Nacional de Extensiones
                </h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="resetMapView()" title="Centrar mapa en Venezuela">
                        <i class="ri ri-focus-3-line"></i> Centrar
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleFullscreen()" title="Ver mapa en pantalla completa">
                        <i class="ri ri-fullscreen-line"></i> Pantalla completa
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="mapaVenezuela" wire:ignore>
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando mapa...</span>
                        </div>
                        <p class="text-muted mt-3">Cargando mapa de distribución...</p>
                    </div>
                </div>

                <!-- Leyenda del mapa -->
                <div class="mapa-leyenda" id="mapaLeyenda" style="display: none;">
                    <h6><i class="ri ri-map-pin-line"></i> Leyenda</h6>
                    <div class="leyenda-item">
                        <img src="/img/logo.png" style="width: 12px; height: 12px; object-fit: contain;" alt="Logo">
                        <span style="margin-left: 8px;">Estados con extensiones establecidas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 5 Estados con más extensiones -->
    @if($estadosConIglesias && $estadosConIglesias->count() > 0)
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="ri ri-trophy-line text-warning"></i>
                    Top 5 Estados
                </h6>
            </div>
            <div class="card-body">
                <div class="top-estados-list">
                    <div class="list-group list-group-flush">
                        @foreach($estadosConIglesias->take(5) as $index => $estado)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                <span class="text-truncate" style="max-width: 120px;">{{ $estado['nombre'] }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success">{{ $estado['cantidad_iglesias'] }}</span>
                                <div class="ms-2" style="width: 12px; height: 12px; background-color: {{ $estado['color'] }}; border-radius: 2px;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($estadosConIglesias->count() > 5)
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <a href="{{ route('admin.iglesias.mapa-distribucion') }}" class="text-decoration-none">
                            <i class="ri ri-external-link-line"></i> Ver todos los estados
                        </a>
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Estadísticas resumidas -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="ri ri-bar-chart-line text-info"></i>
                    Resumen Nacional
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <h4 class="text-primary mb-1">{{ $estadosConIglesias->where('cantidad_iglesias', '>', 0)->count() }}</h4>
                        <small class="text-muted">Estados con extensiones</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4 class="text-success mb-1">{{ $totalIglesiasMapa }}</h4>
                        <small class="text-muted">Total de extensiones</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4 class="text-warning mb-1">{{ round($totalIglesiasMapa / max($estadosConIglesias->where('cantidad_iglesias', '>', 0)->count(), 1)) }}</h4>
                        <small class="text-muted">Promedio por estado</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4 class="text-info mb-1">{{ $estadosConIglesias->where('cantidad_iglesias', 0)->count() }}</h4>
                        <small class="text-muted">Estados sin extensiones</small>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="progress" style="height: 8px;">
                        @php
                            $estadosCon = $estadosConIglesias->where('cantidad_iglesias', '>', 0)->count();
                            $totalEstados = $estadosConIglesias->count();
                            $porcentajeCobertura = $totalEstados > 0 ? ($estadosCon / $totalEstados) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $porcentajeCobertura }}%"
                             title="{{ round($porcentajeCobertura) }}% de cobertura nacional">
                            {{ round($porcentajeCobertura) }}% cobertura
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Sección de Crecimiento -->
<div class="row">
    <div class="col-12">
        <h4 class="mb-3">
            <i class="ri ri-line-chart-line text-warning"></i>
            Crecimiento Anual
        </h4>
    </div>

    <!-- Crecimiento de Pastores -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Crecimiento de Pastores</h5>
                <small class="text-muted">{{ date('Y') - 1 }} vs {{ date('Y') }}</small>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6 text-center">
                        <h4 class="text-primary">{{ $crecimientoPastores['last_year'] }}</h4>
                        <small class="text-muted">{{ date('Y') - 1 }}</small>
                    </div>
                    <div class="col-6 text-center">
                        <h4 class="text-success">{{ $crecimientoPastores['current_year'] }}</h4>
                        <small class="text-muted">{{ date('Y') }}</small>
                    </div>
                </div>
                <div id="chartCrecimientoPastores"></div>
            </div>
        </div>
    </div>

    <!-- Crecimiento de Extensiones -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Crecimiento de Extensiones</h5>
                <small class="text-muted">{{ date('Y') - 1 }} vs {{ date('Y') }}</small>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6 text-center">
                        <h4 class="text-primary">{{ $crecimientoIglesias['last_year'] }}</h4>
                        <small class="text-muted">{{ date('Y') - 1 }}</small>
                    </div>
                    <div class="col-6 text-center">
                        <h4 class="text-success">{{ $crecimientoIglesias['current_year'] }}</h4>
                        <small class="text-muted">{{ date('Y') }}</small>
                    </div>
                </div>
                @if(isset($crecimientoIglesias['last_year']) && isset($crecimientoIglesias['current_year']))
                <div id="chartCrecimientoIglesias"></div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Scripts para los gráficos y Mapa -->
@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Inicializar variables de gráficos en el objeto window
    window.chartCrecimientoPastores = null;
    window.chartCrecimientoIglesias = null;

    // Esperar a que Livewire termine de cargar
    document.addEventListener('livewire:initialized', function() {
        console.log('Livewire inicializado, cargando gráficos...');
        cargarGraficos();
    });

    // Solo cargar cuando el DOM esté listo si Livewire no está disponible
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Livewire === 'undefined' || !Livewire.initialized) {
            console.log('DOMContentLoaded sin Livewire, cargando gráficos...');
            cargarGraficos();
        }
    });

    // Variable para prevenir ejecución múltiple
    let graficosCargados = false;

    function cargarGraficos() {
        // Prevenir ejecución múltiple
        if (graficosCargados) {
            console.log('Los gráficos ya fueron cargados, ignorando llamada duplicada');
            return;
        }
        graficosCargados = true;
        try {
            console.log('Iniciando carga de gráficos...');
            console.log('Total de datos de edades:', {{ $pastoresPorRangoEdad->count() }});
            console.log('Total de datos de grado ministerial:', {{ $pastoresPorGradoMinisterial->count() }});
            console.log('Total de datos de género:', {{ $pastoresPorGenero->count() }});
            console.log('Total de datos de estado civil:', {{ $pastoresPorEstadoCivil->count() }});
            console.log('Total de datos de extensiones por tipo:', {{ $iglesiasPorTipoLocal->count() }});
            console.log('Total de datos de extensiones por estado:', {{ $iglesiasPorEstado->count() }});

            // Verificar todos los contenedores de gráficos
            var contenedores = [
                'chartPastoresGenero',
                'chartPastoresEstadoCivil',
                'chartPastoresEdad',
                'chartPastoresGrado',
                'chartIglesiasTipo',
                'chartIglesiasEstado',
                'chartCrecimientoPastores',
                'chartCrecimientoIglesias'
            ];

            contenedores.forEach(function(id) {
                var elemento = document.querySelector('#' + id);
                console.log('Contenedor ' + id + ':', elemento ? 'ENCONTRADO' : 'NO ENCONTRADO');
            });
        // Gráfico de Pastores por Género
        @if($pastoresPorGenero->count() > 0)
        var optionsGenero = {
            series: [
                @foreach($pastoresPorGenero as $item)
                    {{ $item->total }},
                @endforeach
            ],
            chart: {
                type: 'donut',
                height: 350,
                animations: {
                    enabled: true,
                    speed: 1000,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    }
                },
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false
                    }
                }
            },
            labels: [
                @foreach($pastoresPorGenero as $item)
                    '{{ $item->genero }}',
                @endforeach
            ],
            colors: ['#1f77b4', '#ff7f0e', '#2ca02c'],
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '14px',
                fontFamily: 'Inter, sans-serif',
                markers: {
                    width: 12,
                    height: 12,
                    radius: 50
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '16px',
                                fontFamily: 'Inter, sans-serif',
                                color: '#666'
                            },
                            value: {
                                show: true,
                                fontSize: '18px',
                                fontFamily: 'Inter, sans-serif',
                                color: '#333',
                                formatter: function(val) {
                                    return val + ' (' + Math.round(val / {{ $totalPastores }} * 100) + '%)';
                                }
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '16px',
                                fontFamily: 'Inter, sans-serif',
                                color: '#333',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            tooltip: {
                enabled: true,
                y: {
                    formatter: function(val) {
                        return val + ' pastores (' + Math.round(val / {{ $totalPastores }} * 100) + '%)';
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chartGenero = new ApexCharts(document.querySelector("#chartPastoresGenero"), optionsGenero);
        chartGenero.render();

        // Añadir evento de clic
        chartGenero.addEventListener('dataPointSelection', function(event, chartContext, config) {
            var label = config.w.config.labels[config.dataPointIndex];
            var value = config.w.config.series[config.dataPointIndex];

            // Mostrar detalles en un modal o tooltip
            showDetailsModal('Pastores por Género', label + ': ' + value + ' pastores');
        });
        @endif

        // Gráfico de Pastores por Estado Civil
        @if($pastoresPorEstadoCivil->count() > 0)
        var optionsEstadoCivil = {
            series: [
                @foreach($pastoresPorEstadoCivil as $item)
                    {{ $item->total }},
                @endforeach
            ],
            chart: {
                type: 'pie',
                height: 300
            },
            labels: [
                @foreach($pastoresPorEstadoCivil as $item)
                    '{{ $item->estado_civil }}',
                @endforeach
            ],
            colors: ['#FF4560', '#008FFB', '#FEB019', '#775DD0'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' pastores';
                    }
                }
            }
        };

        var chartEstadoCivil = new ApexCharts(document.querySelector("#chartPastoresEstadoCivil"), optionsEstadoCivil);
        chartEstadoCivil.render();
        @endif



        // Gráfico de Pastores por Rango de Edad
        @if($pastoresPorRangoEdad->count() > 0)
        console.log('Datos de edades:', {!! $pastoresPorRangoEdad->toJson() !!});

        var contenedorEdad = document.querySelector("#chartPastoresEdad");
        console.log('Contenedor de edades encontrado:', contenedorEdad);

        if (contenedorEdad) {
            var optionsEdad = {
                series: [{
                    name: 'Pastores',
                    data: {!! $pastoresPorRangoEdad->pluck('total')->toJson() !!}
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    animations: {
                        enabled: true,
                        speed: 1000
                    },
                    toolbar: {
                        show: true,
                        tools: {
                            download: true
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: {!! $pastoresPorRangoEdad->pluck('rango_edad')->toJson() !!}
                },
                colors: ['#008FFB'],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + ' pastores';
                        }
                    }
                }
            };

            var chartEdad = new ApexCharts(contenedorEdad, optionsEdad);
            chartEdad.render();
            console.log('Gráfico de edades renderizado exitosamente');
        } else {
            console.error('No se encontró el contenedor #chartPastoresEdad');
        }
        @else
        console.log('No hay datos de edades disponibles');
        @endif

        // Gráfico de Pastores por Grado Ministerial
        @if($pastoresPorGradoMinisterial->count() > 0)
        console.log('Datos de grado ministerial:', [
            @foreach($pastoresPorGradoMinisterial as $item)
                { grado: '{{ $item->nivel_ministerial }}', total: {{ $item->total }} },
            @endforeach
        ]);

        var optionsGrado = {
            series: [
                @foreach($pastoresPorGradoMinisterial as $item)
                    {{ $item->total }},
                @endforeach
            ],
            chart: {
                type: 'donut',
                height: 300,
                animations: {
                    enabled: true,
                    speed: 1000
                },
                toolbar: {
                    show: true,
                    tools: {
                        download: true
                    }
                }
            },
            labels: [
                @foreach($pastoresPorGradoMinisterial as $item)
                    '{{ $item->nivel_ministerial }}',
                @endforeach
            ],
            colors: ['#FF4560', '#008FFB', '#00E396', '#FEB019', '#775DD0'],
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' pastores';
                    }
                }
            }
        };

        if (document.querySelector("#chartPastoresGrado")) {
            var chartGrado = new ApexCharts(document.querySelector("#chartPastoresGrado"), optionsGrado);
            chartGrado.render();
            console.log('Gráfico de grado ministerial renderizado exitosamente');
        } else {
            console.error('No se encontró el contenedor #chartPastoresGrado');
        }
        @else
        console.log('No hay datos de grado ministerial disponibles');
        @endif

        // Gráfico de Extensiones por Tipo de Local
        @if($iglesiasPorTipoLocal->count() > 0)
        var optionsIglesiasTipo = {
            series: [
                @foreach($iglesiasPorTipoLocal as $item)
                    {{ $item->iglesias_count }},
                @endforeach
            ],
            chart: {
                type: 'donut',
                height: 300
            },
            labels: [
                @foreach($iglesiasPorTipoLocal as $item)
                    '{{ $item->nombre }}',
                @endforeach
            ],
            colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560'],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' extensiones';
                    }
                }
            }
        };

        var chartIglesiasTipo = new ApexCharts(document.querySelector("#chartIglesiasTipo"), optionsIglesiasTipo);
        chartIglesiasTipo.render();
        @endif

        // Gráfico de Extensiones por Estado
        @if($iglesiasPorEstado->count() > 0)
        var optionsIglesiasEstado = {
            series: [{
                data: [
                    @foreach($iglesiasPorEstado as $item)
                        {{ $item->iglesias_count }},
                    @endforeach
                ]
            }],
            chart: {
                type: 'bar',
                height: 300
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: [
                    @foreach($iglesiasPorEstado as $item)
                        '{{ $item->nombre }}',
                    @endforeach
                ]
            },
            colors: ['#FF4560'],
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' extensiones';
                    }
                }
            }
        };

        var chartIglesiasEstado = new ApexCharts(document.querySelector("#chartIglesiasEstado"), optionsIglesiasEstado);
        chartIglesiasEstado.render();
        @endif

        // Gráfico de Crecimiento de Pastores
        @if(isset($crecimientoPastores['last_year']) && isset($crecimientoPastores['current_year']))
        var optionsCrecimientoPastores = {
            series: [{
                name: 'Pastores',
                data: [{{ $crecimientoPastores['last_year'] }}, {{ $crecimientoPastores['current_year'] }}]
            }],
            chart: {
                type: 'line',
                height: 200,
                animations: {
                    enabled: true,
                    speed: 1000
                },
                toolbar: {
                    show: true,
                    tools: {
                        download: true
                    }
                }
            },
            xaxis: {
                categories: ['{{ $selectedYear - 1 }}', '{{ $selectedYear }}'],
                title: {
                    text: 'Año'
                }
            },
            yaxis: {
                title: {
                    text: 'Número de Pastores'
                }
            },
            colors: ['#008FFB'],
            markers: {
                size: 6,
                colors: ['#008FFB'],
                strokeColors: '#fff',
                strokeWidth: 2
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            grid: {
                borderColor: '#f1f1f1'
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' pastores';
                    }
                }
            }
        };

        if (document.querySelector("#chartCrecimientoPastores")) {
            try {
                if (window.chartCrecimientoPastores && typeof window.chartCrecimientoPastores.destroy === 'function') {
                    window.chartCrecimientoPastores.destroy();
                    window.chartCrecimientoPastores = null;
                }
            } catch (error) {
                console.warn('Error al destruir gráfico de pastores:', error);
            }
            window.chartCrecimientoPastores = new ApexCharts(document.querySelector("#chartCrecimientoPastores"), optionsCrecimientoPastores);
            window.chartCrecimientoPastores.render();
        }
        @endif

        // Gráfico de Crecimiento de Extensiones
        @if(isset($crecimientoIglesias['last_year']) && isset($crecimientoIglesias['current_year']))
        var optionsCrecimientoIglesias = {
            series: [{
                name: 'Extensiones',
                data: [{{ $crecimientoIglesias['last_year'] }}, {{ $crecimientoIglesias['current_year'] }}]
            }],
            chart: {
                type: 'line',
                height: 200,
                animations: {
                    enabled: true,
                    speed: 1000
                },
                toolbar: {
                    show: true,
                    tools: {
                        download: true
                    }
                }
            },
            xaxis: {
                categories: ['{{ $selectedYear - 1 }}', '{{ $selectedYear }}'],
                title: {
                    text: 'Año'
                }
            },
            yaxis: {
                title: {
                    text: 'Número de Extensiones'
                }
            },
            colors: ['#00E396'],
            markers: {
                size: 6,
                colors: ['#00E396'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: {
                    size: 8
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            grid: {
                borderColor: '#f1f1f1'
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' extensiones';
                    }
                }
            }
        };

        if (document.querySelector("#chartCrecimientoIglesias")) {
            try {
                if (window.chartCrecimientoIglesias && typeof window.chartCrecimientoIglesias.destroy === 'function') {
                    window.chartCrecimientoIglesias.destroy();
                    window.chartCrecimientoIglesias = null;
                }
            } catch (error) {
                console.warn('Error al destruir gráfico de iglesias:', error);
            }
            window.chartCrecimientoIglesias = new ApexCharts(document.querySelector("#chartCrecimientoIglesias"), optionsCrecimientoIglesias);
            window.chartCrecimientoIglesias.render();
        }
        @endif

        console.log('Todos los gráficos han sido cargados exitosamente');

        // Ocultar el loader después de cargar los gráficos
        if (document.querySelector('.loading-overlay')) {
            document.querySelector('.loading-overlay').style.display = 'none';
            console.log('Loader ocultado exitosamente');
        }
    } catch (error) {
        console.error('Error al cargar gráficos:', error);
        // Ocultar el loader incluso si hay un error
        if (document.querySelector('.loading-overlay')) {
            document.querySelector('.loading-overlay').style.display = 'none';
            console.log('Loader ocultado debido a error');
        }
    }

    // Función para ocultar el loader en caso de error
    function ocultarLoader() {
        if (document.querySelector('.loading-overlay')) {
            document.querySelector('.loading-overlay').style.display = 'none';
        }
    }

    // Ocultar el loader después de 3 segundos como medida de seguridad
    setTimeout(ocultarLoader, 3000);

    // Eliminado el listener de DOMContentLoaded para evitar duplicación de gráficos
    // document.addEventListener('DOMContentLoaded', function() {
    //     console.log('DOM completamente cargado');
    //     setTimeout(ocultarLoader, 1000); // Ocultar 1 segundo después de que el DOM esté listo
    // });

    // Funciones de utilidad
    function exportChart(chartId) {
        var chart = ApexCharts.getChartByID(chartId);
        if (chart) {
            chart.dataURI().then(({ imgURI, blob }) => {
                var link = document.createElement('a');
                link.download = chartId + '_' + new Date().getTime() + '.png';
                link.href = imgURI;
                link.click();
            });
        }
    }

    function exportData(type) {
        // Función para exportar datos
        var data = {};
        var filename = '';

        switch(type) {
            case 'members':
                data = {
                    total_members: {{ $miembrosActivos }},
                    total_churches: {{ $totalIglesias }},
                    average_per_church: {{ $totalIglesias > 0 ? round($miembrosActivos / $totalIglesias) : 0 }}
                };
                filename = 'miembros_data_' + new Date().getTime() + '.json';
                break;
            case 'fields':
                data = {
                    total_fields: {{ $camposBlancos }},
                    year: {{ $selectedYear }}
                };
                filename = 'campos_blancos_data_' + new Date().getTime() + '.json';
                break;
        }

        var blob = new Blob([JSON.stringify(data, null, 2)], {type: 'application/json'});
        var link = document.createElement('a');
        link.download = filename;
        link.href = URL.createObjectURL(blob);
        link.click();
    }

    function showDetailsModal(title, content) {
        // Crear modal dinámico si no existe
        if (!document.getElementById('detailsModal')) {
            var modalHtml = `
                <div class="modal fade" id="detailsModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="modal-content-text"></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        var modal = new bootstrap.Modal(document.getElementById('detailsModal'));
        document.querySelector('#detailsModal .modal-title').textContent = title;
        document.querySelector('#detailsModal .modal-content-text').textContent = content;
        modal.show();
    }

    function showMembersDetails() {
        var content = `Total de miembros activos: {{ number_format($miembrosActivos) }}\n` +
                     `Total de iglesias: {{ number_format($totalIglesias) }}\n` +
                     `Promedio por iglesia: {{ $totalIglesias > 0 ? round($miembrosActivos / $totalIglesias) : 0 }} miembros`;
        showDetailsModal('Detalles de Miembros', content);
    }

    function showFieldsDetails() {
        var content = `Total de campos blancos: {{ number_format($camposBlancos) }}\n` +
                     `Año actual: {{ $selectedYear }}\n` +
                     `Estos representan oportunidades misioneras para nuevas obras.`;
        showDetailsModal('Detalles de Campos Blancos', content);
    }

    // Función para actualizar gráficos cuando cambia el año
    Livewire.on('yearUpdated', function() {
        // Recargar la página para actualizar todos los gráficos
        location.reload();
    });

    // Listener para ocultar loader cuando Livewire termine de cargar
    Livewire.hook('message.processed', (message, component) => {
        console.log('Livewire terminó de procesar, ocultando loader');
        if (document.querySelector('.loading-overlay')) {
            document.querySelector('.loading-overlay').style.display = 'none';
        }
    });

    // Listener para cuando el componente esté completamente cargado
    Livewire.on('component-loaded', function() {
        console.log('Componente cargado, ocultando loader');
        if (document.querySelector('.loading-overlay')) {
            document.querySelector('.loading-overlay').style.display = 'none';
        }
    });

    // Listener para cuando el componente esté completamente cargado
    Livewire.on('component-loaded', function() {
        console.log('Componente cargado, ocultando loader');
        if (document.querySelector('.loading-overlay')) {
            document.querySelector('.loading-overlay').style.display = 'none';
        }
    });

    // Listener para mostrar modal de estado
    Livewire.on('mostrarModalEstado', function(data) {
        mostrarModalEstado(data);
    });

    // Función para mostrar modal de estado
    function mostrarModalEstado(data) {
        // Crear modal si no existe
        if (!document.getElementById('estadoModal')) {
            const modalHtml = `
                <div class="modal fade" id="estadoModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="ri ri-map-pin-line text-primary"></i>
                                    Extensiones en <span id="estadoNombre"></span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <span class="badge bg-primary fs-6">
                                        <span id="totalIglesias"></span> iglesia(s)
                                    </span>
                                </div>
                                <div id="iglesiasList" class="table-responsive"></div>
                                <div id="mensajeMas" class="alert alert-info mt-3" style="display: none;">
                                    <i class="ri ri-information-line"></i>
                                    Hay más extensiones en este estado.
                                    <a href="{{ route('admin.iglesias.index') }}" class="alert-link">
                                        Ver todas las extensiones
                                    </a>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="ri ri-close-line"></i> Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        // Actualizar contenido
        document.getElementById('estadoNombre').textContent = data.estado;
        document.getElementById('totalIglesias').textContent = data.total_iglesias;

        // Construir lista de extensiones
        let iglesiasHtml = '';
        if (data.iglesias && data.iglesias.length > 0) {
            iglesiasHtml = '<table class="table table-sm table-hover"><thead class="table-light"><tr><th>Nombre</th><th>Pastor</th><th>Ciudad</th><th>Miembros</th></tr></thead><tbody>';
            data.iglesias.forEach(extensión => {
                iglesiasHtml += `
                    <tr>
                        <td><strong>${iglesia.nombre}</strong></td>
                        <td>${iglesia.pastor}</td>
                        <td>${iglesia.ciudad}</td>
                        <td><span class="badge bg-success">${iglesia.miembros_activos || 0}</span></td>
                    </tr>
                `;
            });
            iglesiasHtml += '</tbody></table>';
        } else {
            iglesiasHtml = '<div class="text-center p-4"><i class="ri ri-building-line ri-24px text-muted mb-2"></i><p class="text-muted">No hay extensiones activas en este estado.</p></div>';
        }

        document.getElementById('iglesiasList').innerHTML = iglesiasHtml;

        // Mostrar mensaje de más extensiones si aplica
        if (data.tiene_mas) {
            document.getElementById('mensajeMas').style.display = 'block';
        } else {
            document.getElementById('mensajeMas').style.display = 'none';
        }

        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('estadoModal'));
        modal.show();
    }

    // Animaciones al hacer scroll
    function animateOnScroll() {
        var cards = document.querySelectorAll('.hover-card');
        cards.forEach(function(card) {
            var cardTop = card.getBoundingClientRect().top;
            var cardBottom = card.getBoundingClientRect().bottom;

            if (cardTop < window.innerHeight && cardBottom > 0) {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }
        });
    }

    // Inicializar animaciones
    window.addEventListener('scroll', animateOnScroll);
    window.addEventListener('load', animateOnScroll);

    // Establecer opacidad inicial para animación
    document.querySelectorAll('.hover-card').forEach(function(card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });

    // Inicializar mapa de distribución
    inicializarMapaDistribucion();
}

// Funciones del mapa
globalThis.inicializarMapaDistribucion = function() {
    // Verificar que Leaflet esté disponible
    if (typeof L === 'undefined') {
        console.error('Leaflet JS no está cargado');
        document.getElementById('mapaVenezuela').innerHTML = '<div class="text-center p-4"><i class="ri ri-error-warning-line ri-48px text-danger mb-3"></i><h6 class="text-danger">Error al cargar el mapa</h6><p class="text-muted small">Leaflet JS no está disponible. Por favor, verifica la conexión a internet.</p></div>';
        return;
    }

    try {
        // Limpiar el contenedor si ya tiene un mapa
        const mapContainer = document.getElementById('mapaVenezuela');
        if (mapContainer._leaflet_id) {
            mapContainer.outerHTML = mapContainer.outerHTML; // Reset node
        }

        // Crear mapa
        const map = L.map('mapaVenezuela', {
            center: [7.4238, -66.5897], // Centro de Venezuela [lat, lng] (Ajustado un poco más arriba)
            zoom: 6, // Zoom inicial más cercano
            minZoom: 4,
            maxZoom: 18,
            zoomControl: true
        });

        // Capa base de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Variables globales para el mapa
        globalThis.mapaVenezuela = map;
        globalThis.markersLayer = L.layerGroup().addTo(map); // Para marcadores de estado

        // Usamos MarkerClusterGroup en lugar de un layerGroup normal para agrupar marcadores muy cercanos
        globalThis.detailedMarkersLayer = L.markerClusterGroup({
            maxClusterRadius: 30, // Radio de agrupacion
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        }).addTo(map);

        // Mostrar leyenda
        setTimeout(function() {
            const leyenda = document.getElementById('mapaLeyenda');
            if (leyenda) {
                leyenda.style.display = 'block';
                setTimeout(function() {
                    leyenda.classList.add('show');
                }, 50);
            }
        }, 500);

        cargarGeoJSONAlMapa(map);

    } catch (error) {
        console.error('Error al inicializar el mapa:', error);
        document.getElementById('mapaVenezuela').innerHTML = '<div class="text-center p-4"><i class="ri ri-error-warning-line ri-48px text-danger mb-3"></i><h6 class="text-danger">Error al cargar el mapa</h6><p class="text-muted small">Ocurrió un error inesperado.</p></div>';
    }
}

globalThis.cargarGeoJSONAlMapa = function(map) {
    const estadosData = @json($estadosConIglesias);

    if (!estadosData || estadosData.length === 0) {
        console.warn('No hay datos de estados disponibles');
        return;
    }

    fetch('/geojson/venezuela-states.json')
        .then(response => {
            if (!response.ok) throw new Error('No se pudo cargar el GeoJSON');
            return response.json();
        })
        .then(geojson => {
            // Enriquecer el GeoJSON
            geojson.features.forEach(feature => {
                const estadoData = estadosData.find(e =>
                    e.nombre.toLowerCase().includes(feature.properties.name.toLowerCase()) ||
                    feature.properties.name.toLowerCase().includes(e.nombre.toLowerCase())
                );

                if (estadoData) {
                    feature.properties.cantidad_iglesias = estadoData.cantidad_iglesias;
                    feature.properties.ejemplo_iglesia = estadoData.ejemplo_iglesia || 'No disponible';
                    feature.properties.id_estado = estadoData.id;
                } else {
                    feature.properties.cantidad_iglesias = 0;
                }
            });

            // Icono personalizado para estado
            const stateIcon = L.icon({
                iconUrl: '/img/logo.png',
                iconSize: [24, 24],
                iconAnchor: [12, 12],
                popupAnchor: [0, -12]
            });

            // Agregar marcadores centrales por estado
            geojson.features.filter(f => f.properties.cantidad_iglesias > 0).forEach(feature => {
                let coords = feature.geometry.coordinates[0];
                let x = 0, y = 0;
                coords.forEach(coord => { x += coord[0]; y += coord[1]; });
                x /= coords.length;
                y /= coords.length;

                const marker = L.marker([y, x], { icon: stateIcon }).addTo(globalThis.markersLayer);

                const estado = feature.properties.name;
                const cantidad = feature.properties.cantidad_iglesias;
                const ejemplo = feature.properties.ejemplo_iglesia;
                const idEstado = feature.properties.id_estado;

                const popupContent = `
                    <div class="estado-popup" style="text-align: center; min-width: 150px;">
                        <h6><strong>${estado}</strong></h6>
                        <p class="mb-2">🏛️ <strong>${cantidad}</strong> extensión${cantidad !== 1 ? 's' : ''}</p>
                        ${cantidad > 0 ? `<small class="text-muted">📍 ${ejemplo}</small>` : ''}
                        <div class="mt-2">
                            <button class="btn btn-sm btn-primary w-100" onclick="cargarDetallesEstado(${idEstado}, '${estado}', ${y}, ${x})">
                                <i class="ri ri-zoom-in-line"></i> Ver detallado
                            </button>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);

                // Agregar el acercamiento automático (flyTo) cuando se hace click en el icono, antes de abrir "Ver detallado"
                marker.on('click', function() {
                    globalThis.mapaVenezuela.flyTo([y, x], 5, {
                        animate: true,
                        duration: 1
                    });
                });
            });
        })
        .catch(error => {
            console.error('Error al cargar GeoJSON:', error);
        });
}

// Función que se llama desde el popup para hacer zoom y pedir las iglesias
globalThis.cargarDetallesEstado = function(idEstado, nombreEstado, lat, lng) {
    // Cerrar popups abiertos
    globalThis.mapaVenezuela.closePopup();

    // Zoom suave hacia el estado
    globalThis.mapaVenezuela.flyTo([lat, lng], 8, {
        animate: true,
        duration: 1.5
    });

    // Mostrar spinner o limpiar capa de detalles
    globalThis.detailedMarkersLayer.clearLayers();
    globalThis.markersLayer.setOpacity ? globalThis.markersLayer.setOpacity(0) : globalThis.mapaVenezuela.removeLayer(globalThis.markersLayer);

    // Ya NO abrimos el modal, solo pedimos las coordenadas para el mapa
    // Livewire.dispatch('verDetallesEstado', { estado: nombreEstado });

    // Pedir las coordenadas específicas (nueva funcion)
    @this.call('cargarExtensionesPorEstado', idEstado);
}

// Escuchar el evento de respuesta del backend con las iglesias
window.addEventListener('mostrarExtensionesEnMapa', function(e) {
    const data = e.detail[0]; // En Livewire 3 los parametros vienen en array
    const extensiones = data.extensiones;

    // Limpiar capa
    globalThis.detailedMarkersLayer.clearLayers();

    // Icono para iglesias específicas
    const churchIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    let bounds = L.latLngBounds();

    extensiones.forEach(ext => {
        if(ext.latitud && ext.longitud) {
            const marker = L.marker([ext.latitud, ext.longitud], { icon: churchIcon }).addTo(globalThis.detailedMarkersLayer);

            marker.bindPopup(`
                <div class="estado-popup">
                    <h6><strong>${ext.nombre}</strong></h6>
                    <p class="mb-1"><i class="ri-user-star-line text-primary"></i> ${ext.pastor}</p>
                    <p class="mb-0 text-muted small"><i class="ri-map-pin-line"></i> ${ext.ciudad}</p>
                    <p class="mb-0 text-muted small" style="white-space: pre-wrap;">${ext.direccion}</p>
                </div>
            `);

            bounds.extend([ext.latitud, ext.longitud]);
        }
    });

    // Ajustar zoom para que se vean todas las extensiones de este estado
    if (bounds.isValid()) {
        setTimeout(() => {
            // maxZoom: 16 permite acercarse mucho más a los marcadores si están agrupados
            globalThis.mapaVenezuela.fitBounds(bounds, { padding: [50, 50], maxZoom: 14, animate: true });
        }, 1000); // Esperar que termine el flyTo inicial
    }
});

// Boton de restablecer
globalThis.resetMapView = function() {
    if (globalThis.mapaVenezuela) {
        // Restaurar marcadores generales y quitar los detallados
        globalThis.detailedMarkersLayer.clearLayers();
        if(!globalThis.mapaVenezuela.hasLayer(globalThis.markersLayer)){
            globalThis.mapaVenezuela.addLayer(globalThis.markersLayer);
        }

        globalThis.mapaVenezuela.flyTo([6.4238, -66.5897], 5.5, {
            animate: true,
            duration: 1.5
        });
    }
}

globalThis.toggleFullscreen = function() {
    const mapContainer = document.getElementById('mapaVenezuela');
    if (mapContainer.requestFullscreen) {
        mapContainer.requestFullscreen();
    } else if (mapContainer.webkitRequestFullscreen) {
        mapContainer.webkitRequestFullscreen();
    } else if (mapContainer.msRequestFullscreen) {
        mapContainer.msRequestFullscreen();
    } else if (mapContainer.mozRequestFullScreen) {
        mapContainer.mozRequestFullScreen();
    }
}

// Ejecutar la carga de gráficos cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            if (!graficosCargados) cargarGraficos();
        }, 100);
    });
} else {
    setTimeout(function() {
        if (!graficosCargados) cargarGraficos();
    }, 100);
}
</script>
@endpush

</div>
