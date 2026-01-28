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
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Materias</h5>
                    <h2>{{ $this->stats['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Materias Activas</h5>
                    <h2>{{ $this->stats['activas'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Materias Inactivas</h5>
                    <h2>{{ $this->stats['inactivas'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Acciones</h5>
                    <div class="d-grid gap-2">
                        @can('create subjects')
                            <a href="{{ route('admin.subjects.create') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus"></i> Nueva Materia
                            </a>
                        @endcan
                        <button wire:click="export" class="btn btn-light btn-sm">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="search" class="form-label">Búsqueda</label>
                    <input type="text" id="search" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Buscar materia...">
                </div>
                <div class="col-md-3">
                    <label for="program_id" class="form-label">Programa</label>
                    <select id="program_id" class="form-select" wire:model.live="program_id">
                        <option value="">Todos los programas</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="educational_level_id" class="form-label">Nivel Educativo</label>
                    <select id="educational_level_id" class="form-select" wire:model.live="educational_level_id">
                        <option value="">Todos los niveles</option>
                        @foreach($educationalLevels as $level)
                            <option value="{{ $level->id }}">{{ $level->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="is_active" class="form-label">Estado</label>
                    <select id="is_active" class="form-select" wire:model.live="is_active">
                        <option value="">Todos</option>
                        <option value="1">Activas</option>
                        <option value="0">Inactivas</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <button wire:click="clearFilters" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('code')" style="cursor: pointer;">
                                Código
                                @if($sortBy === 'code')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('name')" style="cursor: pointer;">
                                Nombre
                                @if($sortBy === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Programa</th>
                            <th>Nivel Educativo</th>
                            <th wire:click="sortBy('credits')" style="cursor: pointer;">
                                Créditos
                                @if($sortBy === 'credits')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('hours_per_week')" style="cursor: pointer;">
                                Horas/Semana
                                @if($sortBy === 'hours_per_week')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
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
                                    <span class="badge bg-{{ $subject->is_active ? 'success' : 'danger' }}">
                                        {{ $subject->is_active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $subject->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-cog"></i> Acciones
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $subject->id }}">
                                            @can('view subjects')
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.subjects.show', $subject->id) }}">
                                                        <i class="fas fa-eye text-info"></i> Ver Detalles
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('edit subjects')
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.subjects.edit', $subject->id) }}">
                                                        <i class="fas fa-edit text-warning"></i> Editar
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('assign teachers')
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.subjects.assign-teachers', $subject->id) }}">
                                                        <i class="fas fa-chalkboard-teacher text-primary"></i> Asignar Profesores
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('edit subjects')
                                                <li>
                                                    <button wire:click="toggleStatus({{ $subject->id }})" class="dropdown-item">
                                                        <i class="fas fa-{{ $subject->is_active ? 'times text-danger' : 'check text-success' }}"></i>
                                                        {{ $subject->is_active ? 'Desactivar' : 'Activar' }}
                                                    </button>
                                                </li>
                                            @endcan
                                            <li><hr class="dropdown-divider"></li>
                                            @can('delete subjects')
                                                <li>
                                                    <button wire:click="delete({{ $subject->id }})" class="dropdown-item text-danger" onclick="confirm('¿Estás seguro de eliminar esta materia?') || event.stopImmediatePropagation()">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No se encontraron materias</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <select class="form-select" wire:model.live="perPage" style="width: auto;">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
                <div>
                    {{ $subjects->links() }}
                </div>
            </div>
        </div>
    </div>
</div>