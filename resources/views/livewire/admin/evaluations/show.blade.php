<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">{{ $evaluation->name }}</h4>
            <p class="text-muted mb-0">Detalles de la evaluación</p>
        </div>
        <div class="d-flex gap-2">
            @can('create grades')
                <a href="{{ route('admin.grades.register', $evaluation->id) }}" class="btn btn-primary">
                    <i class="ri ri-clipboard-check-line me-1"></i> Registrar Notas
                </a>
            @endcan
            @can('edit evaluations')
                <a href="{{ route('admin.evaluations.edit', $evaluation->id) }}" class="btn btn-label-secondary">
                    <i class="ri ri-pencil-line me-1"></i> Editar
                </a>
            @endcan
            <a href="{{ route('admin.evaluations.index') }}" class="btn btn-secondary">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5>Total Estudiantes</h5>
                    <h2>{{ $this->stats['total_students'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5>Calificados</h5>
                    <h2>{{ $this->stats['graded'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h5>Pendientes</h5>
                    <h2>{{ $this->stats['pending'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5>Promedio</h5>
                    <h2>{{ number_format($this->stats['average'] ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Información de la Evaluación</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-4"><strong>Nombre:</strong></div>
                        <div class="col-sm-8">{{ $evaluation->name }}</div>

                        <div class="col-sm-4"><strong>Materia:</strong></div>
                        <div class="col-sm-8">{{ $evaluation->subject->name ?? '-' }}</div>

                        <div class="col-sm-4"><strong>Profesor:</strong></div>
                        <div class="col-sm-8">{{ $evaluation->teacher->user->name ?? '-' }}</div>

                        <div class="col-sm-4"><strong>Lapso:</strong></div>
                        <div class="col-sm-8"><span class="badge bg-label-info">{{ $evaluation->evaluationPeriod->name ?? '-' }}</span></div>

                        <div class="col-sm-4"><strong>Tipo:</strong></div>
                        <div class="col-sm-8"><span class="badge bg-label-secondary">{{ $evaluation->evaluationType->name ?? '-' }}</span></div>

                        <div class="col-sm-4"><strong>Fecha:</strong></div>
                        <div class="col-sm-8">{{ $evaluation->evaluation_date->format('d/m/Y') }}</div>

                        <div class="col-sm-4"><strong>Nota Máxima:</strong></div>
                        <div class="col-sm-8">{{ number_format($evaluation->max_score, 2) }}</div>

                        <div class="col-sm-4"><strong>Peso:</strong></div>
                        <div class="col-sm-8">{{ number_format($evaluation->weight, 2) }}%</div>

                        <div class="col-sm-4"><strong>Estado:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-label-{{ $evaluation->is_active ? 'success' : 'danger' }}">
                                {{ $evaluation->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                            <span class="badge bg-label-{{ $evaluation->is_published ? 'success' : 'warning' }}">
                                {{ $evaluation->is_published ? 'Publicada' : 'No Publicada' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Estadísticas de Notas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-6"><strong>Nota Máxima Obtenida:</strong></div>
                        <div class="col-sm-6"><span class="badge bg-label-success fs-6">{{ number_format($this->stats['max'] ?? 0, 2) }}</span></div>

                        <div class="col-sm-6"><strong>Nota Mínima Obtenida:</strong></div>
                        <div class="col-sm-6"><span class="badge bg-label-danger fs-6">{{ number_format($this->stats['min'] ?? 0, 2) }}</span></div>

                        <div class="col-sm-6"><strong>Promedio:</strong></div>
                        <div class="col-sm-6"><span class="badge bg-label-primary fs-6">{{ number_format($this->stats['average'] ?? 0, 2) }}</span></div>

                        <div class="col-sm-6"><strong>Ausentes:</strong></div>
                        <div class="col-sm-6"><span class="badge bg-label-secondary fs-6">{{ $this->stats['absent'] }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Calificaciones</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Estudiante</th>
                            <th>Nota</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evaluation->grades as $grade)
                            <tr>
                                <td>{{ $grade->student->codigo ?? '-' }}</td>
                                <td>{{ $grade->student->nombres }} {{ $grade->student->apellidos }}</td>
                                <td>
                                    @if($grade->status === 'graded')
                                        <span class="badge bg-label-{{ $grade->score >= ($evaluation->max_score / 2) ? 'success' : 'danger' }} fs-6">
                                            {{ number_format($grade->score, 2) }}
                                        </span>
                                    @else
                                        -
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
                                <td>{{ Str::limit($grade->observations, 50) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hay calificaciones registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
