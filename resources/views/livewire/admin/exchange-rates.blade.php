<div wire:poll.30s="$dispatch('refresh-rates')">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h4 class="mb-1">Tasas de Cambio BCV</h4>
                            <p class="mb-0 text-muted">Banco Central de Venezuela - Actualización automática</p>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="text-end me-3">
                                <small class="text-muted d-block">Última actualización: {{ $lastUpdate }}</small>
                                <small class="text-muted">Horarios: 10:00 AM y 2:00 PM</small>
                            </div>
                            <button wire:click="fetchNow" class="btn btn-primary">
                                <i class="ri ri-refresh-line me-1"></i>Actualizar Ahora
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri ri-check-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Métricas principales -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded-3">
                                <i class="ri ri-money-dollar-circle-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">{{ number_format($stats['usd_rate'] ?? 0, 4) }}</h4>
                        <p class="mb-0">Bolívares por USD</p>
                        <small class="text-muted">{{ $stats['last_fetch'] ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded-3">
                                <i class="ri ri-money-euro-circle-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">{{ number_format($stats['eur_rate'] ?? 0, 4) }}</h4>
                        <p class="mb-0">Bolívares por EUR</p>
                        <small class="text-muted">{{ $stats['last_fetch'] ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-info rounded-3">
                                <i class="ri ri-calendar-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">{{ $stats['date'] ?? 'N/A' }}</h4>
                        <p class="mb-0">Fecha de la Tasa</p>
                        <small class="text-muted">Verificada</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-warning rounded-3">
                                <i class="ri ri-database-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">{{ ucfirst($stats['source'] ?? 'N/A') }}</h4>
                        <p class="mb-0">Fuente de Datos</p>
                        <small class="text-muted">10:00 AM - 2:00 PM</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasa activa del día -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Tasa Activa del Día</h5>
                        <p class="mb-0 text-muted">Tasa oficial vigente</p>
                    </div>
                    <button wire:click="fetchNow" class="btn btn-sm btn-primary">
                        <i class="ri ri-refresh-line me-1"></i>Actualizar
                    </button>
                </div>
                <div class="card-body">
                    @if($todayRate)
                        <div class="row g-4">
                            <div class="col-lg-3 col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="ri ri-money-dollar-circle-line ri-24px"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <h6 class="mb-0">USD</h6>
                                            <small class="text-muted">Bolívares por USD</small>
                                        </div>
                                        <div class="user-progress">
                                            <h4 class="mb-0">{{ number_format($todayRate->usd_rate, 4) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="ri ri-money-euro-circle-line ri-24px"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <h6 class="mb-0">EUR</h6>
                                            <small class="text-muted">Bolívares por EUR</small>
                                        </div>
                                        <div class="user-progress">
                                            <h4 class="mb-0">{{ number_format($todayRate->eur_rate, 4) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-info">
                                            <i class="ri ri-time-line ri-24px"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <h6 class="mb-0">Hora</h6>
                                            <small class="text-muted">Última actualización</small>
                                        </div>
                                        <div class="user-progress">
                                            <h4 class="mb-0">{{ $todayRate->fetch_time->format('H:i') }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="ri ri-database-line ri-24px"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <h6 class="mb-0">Fuente</h6>
                                            <small class="text-muted">Origen de datos</small>
                                        </div>
                                        <div class="user-progress">
                                            <h4 class="mb-0">{{ strtoupper($todayRate->source) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri ri-exchange-dollar-line ri-3x text-muted mb-3"></i>
                            <h5 class="mb-2">No hay tasa registrada para hoy</h5>
                            <p class="text-muted mb-3">Las tasas se actualizan automáticamente a las 10:00 AM y 2:00 PM</p>
                            <button wire:click="fetchNow" class="btn btn-primary">
                                <i class="ri ri-refresh-line me-1"></i>Obtener Tasa Ahora
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>