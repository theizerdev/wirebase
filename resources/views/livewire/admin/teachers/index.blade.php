<div>
    <div class="container-fluid">
        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Gestión de Profesores</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            @foreach($this->getBreadcrumb() as $route => $title)
                                <li class="breadcrumb-item">
                                    @if($loop->last)
                                        <span>{{ $title }}</span>
                                    @else
                                        <a href="{{ route($route) }}">{{ $title }}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Total Profesores</p>
                                <h4 class="mb-0">{{ $this->stats['total'] }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i class="bx bx-user font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Activos</p>
                                <h4 class="mb-0">{{ $this->stats['activos'] }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title">
                                        <i class="bx bx-user-check font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Inactivos</p>
                                <h4 class="mb-0">{{ $this->stats['inactivos'] }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-danger">
                                    <span class="avatar-title">
                                        <i class="bx bx-user-x font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters and Actions --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="search-box">
                                    <div class="position-relative">
                                        <input type="text" class="form-control" 
                                               wire:model.debounce.300ms="search" 
                                               placeholder="Buscar profesor...">
                                        <i class="bx bx-search-alt search-icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" wire:model="specialization">
                                    <option value="">Todas las especializaciones</option>
                                    <option value="Matemáticas">Matemáticas</option>
                                    <option value="Ciencias">Ciencias</option>
                                    <option value="Lenguaje">Lenguaje</option>
                                    <option value="Historia">Historia</option>
                                    <option value="Educación Física">Educación Física</option>
                                    <option value="Arte">Arte</option>
                                    <option value="Música">Música</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" wire:model="is_active">
                                    <option value="">Todos los estados</option>
                                    <option value="1">Activos</option>
                                    <option value="0">Inactivos</option>
                                </select>
                            </div>
                            <div class="col-md-3 text-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary" 
                                            wire:click="clearFilters">
                                        <i class="bx bx-reset"></i> Limpiar
                                    </button>
                                    <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                                        <i class="bx bx-plus"></i> Nuevo Profesor
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Teachers Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-centered table-nowrap align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 70px;">#</th>
                                        <th>
                                            <a href="#" wire:click.prevent="sortBy('employee_code')">
                                            Código
                                            @if($sortBy === 'employee_code')
                                                <i class="bx {{ $sortDirection === 'asc' ? 'bx-sort-up' : 'bx-sort-down' }}"></i>
                                            @endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="#" wire:click.prevent="sortBy('user.name')">
                                            Nombre
                                            @if($sortBy === 'user.name')
                                                <i class="bx {{ $sortDirection === 'asc' ? 'bx-sort-up' : 'bx-sort-down' }}"></i>
                                            @endif
                                            </a>
                                        </th>
                                        <th>Email</th>
                                        <th>Especialización</th>
                                        <th>Título</th>
                                        <th>Años Exp.</th>
                                        <th>Estado</th>
                                        <th>
                                            <a href="#" wire:click.prevent="sortBy('hire_date')">
                                            Fecha Contratación
                                            @if($sortBy === 'hire_date')
                                                <i class="bx {{ $sortDirection === 'asc' ? 'bx-sort-up' : 'bx-sort-down' }}"></i>
                                            @endif
                                            </a>
                                        </th>
                                        <th width="120">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teachers as $teacher)
                                        <tr>
                                            <td>{{ $loop->iteration + ($teachers->currentPage() - 1) * $teachers->perPage() }}</td>
                                            <td>{{ $teacher->employee_code }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title rounded-circle bg-primary text-white">
                                                            {{ strtoupper(substr($teacher->user->name ?? 'SN', 0, 2)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h5 class="font-size-14 mb-1">{{ $teacher->user->name ?? 'Sin nombre' }}</h5>
                                                        <p class="text-muted mb-0">{{ $teacher->user->email ?? '' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $teacher->user->email ?? '' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $teacher->specialization }}</span>
                                            </td>
                                            <td>{{ $teacher->degree }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $teacher->years_experience }} años</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $teacher->is_active ? 'success' : 'danger' }}">
                                                    {{ $teacher->is_active ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td>{{ $teacher->hire_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="bx bx-cog"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('admin.teachers.show', $teacher) }}">
                                                            <i class="bx bx-show"></i> Ver Detalles
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('admin.teachers.edit', $teacher) }}">
                                                            <i class="bx bx-edit"></i> Editar
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <button class="dropdown-item" 
                                                                wire:click="toggleStatus({{ $teacher->id }})"
                                                                onclick="confirm('¿Está seguro de cambiar el estado?') || event.stopImmediatePropagation()">
                                                            <i class="bx bx-{{ $teacher->is_active ? 'user-x' : 'user-check' }}"></i>
                                                            {{ $teacher->is_active ? 'Desactivar' : 'Activar' }}
                                                        </button>
                                                        <button class="dropdown-item text-danger" 
                                                                wire:click="delete({{ $teacher->id }})"
                                                                onclick="confirm('¿Está seguro de eliminar este profesor?') || event.stopImmediatePropagation()">
                                                            <i class="bx bx-trash"></i> Eliminar
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bx bx-user display-4"></i>
                                                    <p>No se encontraron profesores</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="row mt-4">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info">
                                    Mostrando {{ $teachers->firstItem() }} a {{ $teachers->lastItem() }} de {{ $teachers->total() }} registros
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate">
                                    {{ $teachers->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    Livewire.on('teacherDeleted', function() {
        toastr.success('Profesor eliminado correctamente');
    });
    
    Livewire.on('statusUpdated', function() {
        toastr.success('Estado actualizado correctamente');
    });
</script>
@endpush