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
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-file-list-3-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Total Evaluaciones</h6>
                            <h3 class="mb-0">{{ $this->stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-check-double-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Publicadas</h6>
                            <h3 class="mb-0">{{ $this->stats['publicadas'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
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
                            <h3 class="mb-0">{{ $this->stats['pendientes'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center h-100">
                        @can('create evaluations')
                            <a href="{{ route('admin.evaluations.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line me-1"></i> Nueva Evaluación
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card">
        <!-- Card Header -->
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">Gestión de Evaluaciones</h5>
                    <p class="text-muted mb-0">Administra las evaluaciones del sistema</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card-header border-bottom">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="search" class="form-label">Búsqueda</label>
                    <input type="text" id="search" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Buscar...">
                </div>
                <div class="col-md-2">
                    <label for="subject_id" class="form-label">Materia</label>
                    <select id="subject_id" class="form-select" wire:model.live="subject_id">
                        <option value="">Todas</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="teacher_id" class="form-label">Profesor</label>
                    <select id="teacher_id" class="form-select" wire:model.live="teacher_id">
                        <option value="">Todos</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->user->name ?? $teacher->employee_code }}</option>
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
                    <label for="evaluation_type_id" class="form-label">Tipo</label>
                    <select id="evaluation_type_id" class="form-select" wire:model.live="evaluation_type_id">
                        <option value="">Todos</option>
                        @foreach($evaluationTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100">
                        <i class="ri ri-refresh-line me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card-datatable table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th wire:click="sortBy('name')" style="cursor: pointer;">
                            Evaluación @if($sortBy === 'name') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-s-line"></i> @endif
                        </th>
                        <th>Materia</th>
                        <th>Profesor</th>
                        <th>Lapso</th>
                        <th>Tipo</th>
                        <th wire:click="sortBy('evaluation_date')" style="cursor: pointer;">
                            Fecha @if($sortBy === 'evaluation_date') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-s-line"></i> @endif
                        </th>
                        <th>Nota Máx.</th>
                        <th>Peso</th>
                        <th>Publicada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $evaluation)
                        <tr>
                            <td>
                                <strong>{{ $evaluation->name }}</strong>
                                @if($evaluation->description)
                                    <br><small class="text-muted">{{ Str::limit($evaluation->description, 30) }}</small>
                                @endif
                            </td>
                            <td>{{ $evaluation->subject->name ?? '-' }}</td>
                            <td>{{ $evaluation->teacher->user->name ?? '-' }}</td>
                            <td><span class="badge bg-label-info">{{ $evaluation->evaluationPeriod->name ?? '-' }}</span></td>
                            <td><span class="badge bg-label-secondary">{{ $evaluation->evaluationType->name ?? '-' }}</span></td>
                            <td>{{ $evaluation->evaluation_date->format('d/m/Y') }}</td>
                            <td>{{ number_format($evaluation->max_score, 2) }}</td>
                            <td>{{ number_format($evaluation->weight, 2) }}%</td>
                            <td>
                                <span class="badge bg-label-{{ $evaluation->is_published ? 'success' : 'warning' }}">
                                    {{ $evaluation->is_published ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn p-0 dropdown-toggle hide-arrow" type="button" data-bs-toggle="dropdown">
                                        <i class="ri ri-more-2-line"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @can('view evaluations')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.evaluations.show', $evaluation->id) }}">
                                                    <i class="ri ri-eye-line me-2 text-info"></i> Ver Detalles
                                                </a>
                                            </li>
                                        @endcan
                                        @can('create grades')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.grades.register', $evaluation->id) }}">
                                                    <i class="ri ri-clipboard-check-line me-2 text-success"></i> Registrar Notas
                                                </a>
                                            </li>
                                        @endcan
                                        @can('edit evaluations')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.evaluations.edit', $evaluation->id) }}">
                                                    <i class="ri ri-pencil-line me-2 text-warning"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <button wire:click="togglePublished({{ $evaluation->id }})" class="dropdown-item">
                                                    <i class="ri ri-{{ $evaluation->is_published ? 'eye-off-line' : 'eye-line' }} me-2 text-primary"></i>
                                                    {{ $evaluation->is_published ? 'Despublicar' : 'Publicar' }} Notas
                                                </button>
                                            </li>
                                        @endcan
                                        <li><hr class="dropdown-divider"></li>
                                        @can('delete evaluations')
                                            <li>
                                                <button wire:click="delete({{ $evaluation->id }})" class="dropdown-item text-danger" onclick="confirm('¿Eliminar esta evaluación?') || event.stopImmediatePropagation()">
                                                    <i class="ri ri-delete-bin-line me-2"></i> Eliminar
                                                </button>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ri ri-file-list-3-line ri-48px text-muted mb-2"></i>
                                    <p class="mb-0 text-muted">No se encontraron evaluaciones</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <span class="me-2">Mostrar</span>
                <select class="form-select form-select-sm" wire:model.live="perPage" style="width: auto;">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="ms-2">registros</span>
            </div>
            <div>
                {{ $evaluations->links('livewire.pagination') }}
            </div>
        </div>
    </div>
</div>
