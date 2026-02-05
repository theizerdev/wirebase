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
        <div class="col-sm-6 col-xl-4">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="ri ri-book-2-line ri-24px text-primary"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Total Materias</h6>
                            <h3 class="mb-0">{{ $this->stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="ri ri-check-line ri-24px text-success"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Materias Activas</h6>
                            <h3 class="mb-0">{{ $this->stats['activas'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-start border-danger border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="ri ri-close-line ri-24px text-danger"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Materias Inactivas</h6>
                            <h3 class="mb-0">{{ $this->stats['inactivas'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card">
        <!-- Card Header with Title and Action Button -->
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-1">Listado de Materias</h5>
                <p class="text-muted mb-0 small">Gestión de materias del sistema académico</p>
            </div>
            <div class="d-flex gap-2">
                <button wire:click="export" class="btn btn-outline-primary">
                    <i class="ri ri-download-line me-1"></i> Exportar
                </button>
                @can('create subjects')
                    <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
                        <i class="ri ri-add-line me-1"></i> Nueva Materia
                    </a>
                @endcan
            </div>
        </div>

        <!-- Filters Header -->
        <div class="card-header border-bottom">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Búsqueda</label>
                    <input type="text" id="search" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Buscar materia...">
                </div>
                <div class="col-md-2">
                    <label for="program_id" class="form-label">Programa</label>
                    <select id="program_id" class="form-select" wire:model.live="program_id">
                        <option value="">Todos los programas</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="educational_level_id" class="form-label">Nivel Educativo</label>
                    <select id="educational_level_id" class="form-select" wire:model.live="educational_level_id">
                        <option value="">Todos los niveles</option>
                        @foreach($educationalLevels as $level)
                            <option value="{{ $level->id }}">{{ $level->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="is_active" class="form-label">Estado</label>
                    <select id="is_active" class="form-select" wire:model.live="is_active">
                        <option value="">Todos</option>
                        <option value="1">Activas</option>
                        <option value="0">Inactivas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="perPage" class="form-label">Mostrar</label>
                    <select id="perPage" class="form-select" wire:model.live="perPage">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100" title="Limpiar filtros">
                        <i class="ri ri-eraser-line"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card-datatable table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th wire:click="sortBy('code')" style="cursor: pointer;">
                            Código
                            @if($sortBy === 'code')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-s-line"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('name')" style="cursor: pointer;">
                            Nombre
                            @if($sortBy === 'name')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-s-line"></i>
                            @endif
                        </th>
                        <th>Programa</th>
                        <th>Nivel Educativo</th>
                        <th wire:click="sortBy('credits')" style="cursor: pointer;">
                            Créditos
                            @if($sortBy === 'credits')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-s-line"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('hours_per_week')" style="cursor: pointer;">
                            Horas/Semana
                            @if($sortBy === 'hours_per_week')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-s-line"></i>
                            @endif
                        </th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                        <tr>
                            <td>{{ $subject->code }}</td>
                            <td>{{ $subject->name }}</td>
                            <td>{{ $subject->programa->nombre ?? '-' }}</td>
                            <td>{{ $subject->educationalLevel->nombre ?? '-' }}</td>
                            <td>{{ $subject->credits }}</td>
                            <td>{{ $subject->hours_per_week }}</td>
                            <td>
                                <span class="badge bg-label-{{ $subject->is_active ? 'success' : 'danger' }}">
                                    {{ $subject->is_active ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn p-0 dropdown-toggle hide-arrow" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri ri-more-2-line"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @can('view subjects')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.subjects.show', $subject->id) }}">
                                                    <i class="ri ri-eye-line me-2"></i> Ver Detalles
                                                </a>
                                            </li>
                                        @endcan
                                        @can('edit subjects')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.subjects.edit', $subject->id) }}">
                                                    <i class="ri ri-pencil-line me-2"></i> Editar
                                                </a>
                                            </li>
                                        @endcan
                                        @can('assign teachers')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.subjects.assign-teachers', $subject->id) }}">
                                                    <i class="ri ri-user-add-line me-2"></i> Asignar Profesores
                                                </a>
                                            </li>
                                        @endcan
                                        @can('edit subjects')
                                            <li>
                                                <button wire:click="toggleStatus({{ $subject->id }})" class="dropdown-item">
                                                    @if($subject->is_active)
                                                        <i class="ri ri-close-line me-2 text-danger"></i> Desactivar
                                                    @else
                                                        <i class="ri ri-check-line me-2 text-success"></i> Activar
                                                    @endif
                                                </button>
                                            </li>
                                        @endcan
                                        @can('delete subjects')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button wire:click="delete({{ $subject->id }})" class="dropdown-item text-danger" onclick="confirm('¿Estás seguro de eliminar esta materia?') || event.stopImmediatePropagation()">
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
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ri ri-inbox-line ri-24px mb-2 d-block"></i>
                                    No se encontraron materias
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="card-footer">
            @include('livewire.pagination', ['paginator' => $subjects])
        </div>
    </div>
</div>
