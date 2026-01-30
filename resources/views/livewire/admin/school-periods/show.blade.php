<div>
    <div class="card mb-4">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ri ri-calendar-line me-2"></i>Información del Período Escolar</h5>
            <div>
                <a href="{{ route('admin.school-periods.edit', $schoolPeriod) }}" class="btn btn-primary btn-sm">
                    <i class="ri ri-pencil-line"></i> Editar
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Nombre</label>
                        <p class="fs-5">{{ $schoolPeriod->name }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Fecha de Inicio</label>
                        <p class="fs-5">{{ $schoolPeriod->start_date->format('d/m/Y') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Fecha de Fin</label>
                        <p class="fs-5">{{ $schoolPeriod->end_date->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Estado</label>
                        <p>
                            @if($schoolPeriod->is_active)
                                <span class="badge bg-label-success">Activo</span>
                            @else
                                <span class="badge bg-label-secondary">Inactivo</span>
                            @endif

                            @if($schoolPeriod->is_current)
                                <span class="badge bg-label-primary ms-1">Actual</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Descripción</label>
                        <p class="fs-5">{{ $schoolPeriod->description ?? 'No se ha proporcionado una descripción.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h6 class="mb-0"><i class="ri ri-bar-chart-line me-2"></i>Estadísticas</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Duración:</span>
                        <span class="fw-bold">{{ $schoolPeriod->start_date->diffInDays($schoolPeriod->end_date) }} días</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Días transcurridos:</span>
                        <span class="fw-bold">
                            @if(now()->between($schoolPeriod->start_date, $schoolPeriod->end_date))
                                {{ $schoolPeriod->start_date->diffInDays(now()) }} días
                            @elseif(now()->isAfter($schoolPeriod->end_date))
                                {{ $schoolPeriod->start_date->diffInDays($schoolPeriod->end_date) }} días
                            @else
                                0 días
                            @endif
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Progreso:</span>
                        <span class="fw-bold">
                            @if(now()->between($schoolPeriod->start_date, $schoolPeriod->end_date))
                                {{ round((now()->diffInDays($schoolPeriod->start_date) / $schoolPeriod->start_date->diffInDays($schoolPeriod->end_date)) * 100, 2) }}%
                            @elseif(now()->isAfter($schoolPeriod->end_date))
                                100%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h6 class="mb-0"><i class="ri ri-settings-3-line me-2"></i>Acciones</h6>
                </div>
                <div class="card-body">
                    @if(!$schoolPeriod->is_current)
                        <button class="btn btn-outline-primary w-100 mb-2" wire:click="$dispatch('alert', {type: 'info', message: 'Funcionalidad pendiente de implementar'})">
                            <i class="ri ri-calendar-event-line me-1"></i> Configurar Periodos
                        </button>
                    @endif

                    <button class="btn btn-outline-info w-100" wire:click="$dispatch('alert', {type: 'info', message: 'Funcionalidad pendiente de implementar'})">
                        <i class="ri ri-file-text-line me-1"></i> Generar Reporte
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-start">
        <a href="{{ route('admin.school-periods.index') }}" class="btn btn-label-secondary">
            <i class="ri ri-arrow-left-line"></i> Volver al listado
        </a>
    </div>
</div>
