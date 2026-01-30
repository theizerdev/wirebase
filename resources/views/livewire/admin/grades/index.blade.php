<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-file-list-3-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Total Calificaciones</h6>
                            <h4 class="mb-0">{{ $this->stats['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-check-double-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Calificadas</h6>
                            <h4 class="mb-0">{{ $this->stats['calificadas'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card border-start border-warning border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ri ri-time-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Pendientes</h6>
                            <h4 class="mb-0">{{ $this->stats['pendientes'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-bar-chart-box-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Promedio General</h6>
                            <h4 class="mb-0">{{ number_format($this->stats['promedio_general'] ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Gestión de Calificaciones</h5>
            <small class="text-muted">Listado de todas las calificaciones registradas en el sistema</small>
        </div>

        <!-- Filters -->
        <div class="card-header border-bottom">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Buscar Estudiante</label>
                    <input type="text" id="search" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nombre o código...">
                </div>
                <div class="col-md-3">
                    <label for="subject_id" class="form-label">Materia</label>
                    <select id="subject_id" class="form-select" wire:model.live="subject_id">
                        <option value="">Todas</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="evaluation_period_id" class="form-label">Lapso</label>
                    <select id="evaluation_period_id" class="form-select" wire:model.live="evaluation_period_id">
                        <option value="">Todos</option>
                        @foreach($evaluationPeriods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Estado</label>
                    <select id="status" class="form-select" wire:model.live="status">
                        <option value="">Todos</option>
                        <option value="pending">Pendiente</option>
                        <option value="graded">Calificado</option>
                        <option value="absent">Ausente</option>
                        <option value="exempt">Exonerado</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button wire:click="clearFilters" class="btn btn-secondary w-100">
                        <i class="ri ri-eraser-line me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card-datatable table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Estudiante</th>
                        <th>Materia</th>
                        <th>Evaluación</th>
                        <th>Lapso</th>
                        <th wire:click="sortBy('score')" style="cursor: pointer;">
                            Nota @if($sortBy === 'score') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-s-line"></i> @endif
                        </th>
                        <th>Estado</th>
                        <th>Calificado por</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grades as $grade)
                        <tr>
                            <td>{{ $grade->student->codigo ?? '-' }}</td>
                            <td>{{ $grade->student->nombres }} {{ $grade->student->apellidos }}</td>
                            <td>{{ $grade->evaluation->subject->name ?? '-' }}</td>
                            <td>{{ $grade->evaluation->name ?? '-' }}</td>
                            <td><span class="badge bg-label-info">{{ $grade->evaluation->evaluationPeriod->name ?? '-' }}</span></td>
                            <td>
                                @if($grade->status === 'graded' && $grade->score !== null)
                                    @php
                                        $maxScore = $grade->evaluation->max_score ?? 20;
                                        $isApproved = $grade->score >= ($maxScore / 2);
                                    @endphp
                                    <span class="badge bg-{{ $isApproved ? 'success' : 'danger' }} fs-6">
                                        {{ number_format($grade->score, 2) }}
                                    </span>
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
                                    $statusLabels = [
                                        'pending' => 'Pendiente',
                                        'graded' => 'Calificado',
                                        'absent' => 'Ausente',
                                        'exempt' => 'Exonerado'
                                    ];
                                @endphp
                                <span class="badge bg-label-{{ $statusColors[$grade->status] ?? 'secondary' }}">
                                    {{ $statusLabels[$grade->status] ?? $grade->status }}
                                </span>
                            </td>
                            <td>{{ $grade->gradedBy->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="ri ri-file-list-3-line ri-48px text-muted mb-2"></i>
                                <p class="text-muted mb-0">No se encontraron calificaciones</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
                <select class="form-select form-select-sm" wire:model.live="perPage" style="width: auto;">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div>
                {{ $grades->links('livewire.pagination') }}
            </div>
        </div>
    </div>
</div>
