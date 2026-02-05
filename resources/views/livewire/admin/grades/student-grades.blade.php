<div>
    <!-- Student Info Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header border-bottom d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="ri ri-user-line me-2"></i>Notas del Estudiante
            </h5>
            <div>
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i> Volver
                </a>
                <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                    <i class="ri ri-printer-line me-1"></i> Imprimir
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    @if($student->foto)
                        <img src="{{ asset('storage/' . $student->foto) }}" class="rounded-circle shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="bg-label-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                            <i class="ri ri-user-line ri-2x text-primary"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><span class="fw-semibold text-muted">Código:</span> {{ $student->codigo }}</p>
                            <p class="mb-1"><span class="fw-semibold text-muted">Nombre:</span> {{ $student->nombres }} {{ $student->apellidos }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><span class="fw-semibold text-muted">Nivel:</span> {{ $student->nivelEducativo->nombre ?? '-' }}</p>
                            <p class="mb-1"><span class="fw-semibold text-muted">Turno:</span> {{ $student->turno->nombre ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><span class="fw-semibold text-muted">Grado:</span> {{ $student->grado ?? '-' }}</p>
                            <p class="mb-1"><span class="fw-semibold text-muted">Sección:</span> {{ $student->seccion ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4 g-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                            <i class="ri ri-file-list-3-line ri-lg text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Evaluaciones</h6>
                            <h3 class="mb-0">{{ $this->stats['total_evaluations'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                            <i class="ri ri-check-double-line ri-lg text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Calificadas</h6>
                            <h3 class="mb-0">{{ $this->stats['graded'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-start border-warning border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                            <i class="ri ri-close-line ri-lg text-warning"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Pendientes</h6>
                            <h3 class="mb-0">{{ $this->stats['pending'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                            <i class="ri ri-bar-chart-box-line ri-lg text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Promedio</h6>
                            <h3 class="mb-0">{{ $this->stats['average'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header border-bottom">
            <h6 class="mb-0">
                <i class="ri ri-filter-3-line me-2"></i>Filtros
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="evaluation_period_id" class="form-label">Lapso</label>
                    <select id="evaluation_period_id" class="form-select" wire:model.live="evaluation_period_id">
                        <option value="">Todos los lapsos</option>
                        @foreach($evaluationPeriods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="subject_id" class="form-label">Materia</label>
                    <select id="subject_id" class="form-select" wire:model.live="subject_id">
                        <option value="">Todas las materias</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades Table Card -->
    <div class="card shadow-sm">
        <div class="card-header border-bottom">
            <h5 class="mb-0">
                <i class="ri ri-file-list-3-line me-2"></i>Calificaciones
            </h5>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Materia</th>
                        <th>Evaluación</th>
                        <th>Tipo</th>
                        <th>Lapso</th>
                        <th>Fecha</th>
                        <th>Nota</th>
                        <th>Máxima</th>
                        <th>%</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->grades as $grade)
                        <tr>
                            <td>{{ $grade->evaluation->subject->name ?? '-' }}</td>
                            <td>{{ $grade->evaluation->name ?? '-' }}</td>
                            <td><span class="badge bg-label-secondary">{{ $grade->evaluation->evaluationType->name ?? '-' }}</span></td>
                            <td><span class="badge bg-label-info">{{ $grade->evaluation->evaluationPeriod->name ?? '-' }}</span></td>
                            <td>{{ $grade->evaluation->evaluation_date->format('d/m/Y') }}</td>
                            <td>
                                @if($grade->status === 'graded' && $grade->score !== null)
                                    @php
                                        $maxScore = $grade->evaluation->max_score ?? 20;
                                        $isApproved = $grade->score >= ($maxScore / 2);
                                    @endphp
                                    <span class="badge bg-label-{{ $isApproved ? 'success' : 'danger' }} fs-6">
                                        {{ number_format($grade->score, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ number_format($grade->evaluation->max_score ?? 20, 2) }}</td>
                            <td>
                                @if($grade->percentage !== null)
                                    {{ number_format($grade->percentage, 1) }}%
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'graded' => 'success',
                                        'absent' => 'secondary',
                                        'exempt' => 'info'
                                    ];
                                @endphp
                                <span class="badge bg-label-{{ $statusColors[$grade->status] ?? 'secondary' }}">
                                    {{ $grade->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ri ri-file-list-3-line ri-2x mb-2"></i>
                                    <p class="mb-0">No se encontraron calificaciones</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
