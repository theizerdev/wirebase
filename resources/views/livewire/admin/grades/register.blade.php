<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-2">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                            <i class="ri ri-group-line ri-lg text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total</h6>
                            <h4 class="mb-0 fw-bold">{{ $this->stats['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                            <i class="ri ri-check-double-line ri-lg text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Calificados</h6>
                            <h4 class="mb-0 fw-bold">{{ $this->stats['graded'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card border-start border-warning border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                            <i class="ri ri-time-line ri-lg text-warning"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Pendientes</h6>
                            <h4 class="mb-0 fw-bold">{{ $this->stats['pending'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card border-start border-secondary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-secondary bg-opacity-10 p-3 rounded me-3">
                            <i class="ri ri-user-unfollow-line ri-lg text-secondary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Ausentes</h6>
                            <h4 class="mb-0 fw-bold">{{ $this->stats['absent'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                            <i class="ri ri-bar-chart-box-line ri-lg text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Promedio</h6>
                            <h4 class="mb-0 fw-bold">{{ $this->stats['average'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card border-start border-dark border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-dark bg-opacity-10 p-3 rounded me-3">
                            <i class="ri ri-arrow-up-down-line ri-lg text-dark"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Máx/Mín</h6>
                            <h4 class="mb-0 fw-bold">{{ $this->stats['max'] }}/{{ $this->stats['min'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Evaluation Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center">
                <i class="ri ri-file-list-3-line ri-lg me-2 text-primary"></i>
                <div>
                    <h5 class="mb-0">Información de la Evaluación</h5>
                    <small class="text-muted">Detalles de la evaluación seleccionada</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded me-2">
                            <i class="ri ri-bookmark-line text-primary"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Evaluación</small>
                            <span class="fw-semibold">{{ $evaluation->name }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-2 rounded me-2">
                            <i class="ri ri-book-open-line text-info"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Materia</small>
                            <span class="fw-semibold">{{ $evaluation->subject->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-2 rounded me-2">
                            <i class="ri ri-calendar-line text-warning"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Lapso</small>
                            <span class="badge bg-label-info">{{ $evaluation->evaluationPeriod->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-secondary bg-opacity-10 p-2 rounded me-2">
                            <i class="ri ri-price-tag-3-line text-secondary"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Tipo</small>
                            <span class="badge bg-label-secondary">{{ $evaluation->evaluationType->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-2 rounded me-2">
                            <i class="ri ri-trophy-line text-success"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Nota Máxima</small>
                            <span class="badge bg-success fs-6">{{ number_format($evaluation->max_score, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades Table Card -->
    <div class="card shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ri ri-pencil-ruler-2-line ri-lg me-2 text-primary"></i>
                    <div>
                        <h5 class="mb-0">Registrar Calificaciones</h5>
                        <small class="text-muted">Ingrese las notas de cada estudiante</small>
                    </div>
                </div>
                <a href="{{ route('admin.evaluations.index') }}" class="btn btn-label-secondary">
                    <i class="ri ri-arrow-left-line me-1"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <form wire:submit="saveGrades">
                <div class="card-datatable table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Código</th>
                                <th width="25%">Estudiante</th>
                                <th width="15%">Nota</th>
                                <th width="20%">Estado</th>
                                <th width="25%">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $index = 1; @endphp
                            @forelse($grades as $studentId => $grade)
                                <tr>
                                    <td>{{ $index++ }}</td>
                                    <td><span class="badge bg-label-primary">{{ $grade['student_code'] }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2 bg-label-primary">
                                                <span class="avatar-initial rounded-circle">{{ substr($grade['student_name'], 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $grade['student_name'] }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <input 
                                            type="number" 
                                            class="form-control form-control-sm" 
                                            wire:model.lazy="grades.{{ $studentId }}.score"
                                            min="0" 
                                            max="{{ $evaluation->max_score }}" 
                                            step="0.01"
                                            placeholder="0.00"
                                            @if($grade['status'] === 'absent' || $grade['status'] === 'exempt') disabled @endif
                                        >
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm w-100">
                                            <button type="button" 
                                                class="btn btn-{{ $grade['status'] === 'pending' ? 'warning' : 'outline-warning' }}"
                                                wire:click="setStatus({{ $studentId }}, 'pending')"
                                                title="Pendiente">
                                                <i class="ri ri-time-line"></i>
                                            </button>
                                            <button type="button" 
                                                class="btn btn-{{ $grade['status'] === 'absent' ? 'secondary' : 'outline-secondary' }}"
                                                wire:click="setStatus({{ $studentId }}, 'absent')"
                                                title="Ausente">
                                                <i class="ri ri-user-unfollow-line"></i>
                                            </button>
                                            <button type="button" 
                                                class="btn btn-{{ $grade['status'] === 'exempt' ? 'info' : 'outline-info' }}"
                                                wire:click="setStatus({{ $studentId }}, 'exempt')"
                                                title="Exonerado">
                                                <i class="ri ri-user-follow-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-sm" 
                                            wire:model.lazy="grades.{{ $studentId }}.observations"
                                            placeholder="Observaciones..."
                                        >
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ri ri-user-search-line ri-3x text-muted mb-2"></i>
                                            <span class="text-muted">No hay estudiantes matriculados</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer border-top pt-3">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri ri-save-line me-1"></i> Guardar Calificaciones
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
