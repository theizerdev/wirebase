<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri ri-check-line me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ \App\Models\Empleado::count() }}</h4>
                            <p class="mb-0">Total Empleados</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-briefcase-3-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ \App\Models\Empleado::where('activo', true)->count() }}</h4>
                            <p class="mb-0">Activos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-checkbox-circle-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ \App\Models\Empleado::where('activo', false)->count() }}</h4>
                            <p class="mb-0">Inactivos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-secondary">
                                <i class="ri ri-close-circle-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">Empleados</h5>
                    <p class="mb-0">Gestión de empleados para nómina</p>
                </div>
                @can('create empleados')
                <a href="{{ route('admin.empleados.create') }}" class="btn btn-primary">
                    <i class="ri ri-add-line"></i> Nuevo Empleado
                </a>
                @endcan
            </div>
        </div>
        <div class="card-header border-bottom">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" class="form-control" placeholder="Nombre, documento..."
                           wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" wire:model.live="activo">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mostrar</label>
                    <select class="form-select" wire:model.live="perPage">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="button" class="btn btn-label-secondary" wire:click="clearFilters">
                        <i class="ri ri-eraser-line"></i> Limpiar
                    </button>
                    <button type="button" class="btn btn-label-success" wire:click="export">
                        <i class="mdi mdi-file-excel"></i> Exportar
                    </button>
                    @can('create empleados')
                    <div class="d-flex align-items-center gap-2">
                        <input type="file" class="form-control" wire:model="importFile" accept=".csv,.txt,.xlsx">
                        <button type="button" class="btn btn-label-primary" wire:click="import"><i class="ri ri-upload-2-line me-1"></i> Importar</button>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                            Nombre
                            @if($sortBy === 'nombre')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                            @endif
                        </th>
                        <th>Documento</th>
                        <th>Puesto</th>
                        <th class="text-end">Salario</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($empleados as $emp)
                    <tr>
                        <td>{{ $emp->nombre }} {{ $emp->apellido }}</td>
                        <td>{{ $emp->documento }}</td>
                        <td>{{ $emp->puesto }}</td>
                        <td class="text-end">${{ number_format($emp->salario_base, 2) }}</td>
                        <td>{{ ucfirst($emp->metodo_pago) }}</td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       wire:click="toggleActivo({{ $emp->id }})"
                                       {{ $emp->activo ? 'checked' : '' }}
                                       id="switch{{ $emp->id }}">
                                <label class="form-check-label" for="switch{{ $emp->id }}">
                                    <span class="badge bg-label-{{ $emp->activo ? 'success' : 'secondary' }}">
                                        {{ $emp->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ri ri-more-2-line"></i>
                                </button>
                                <div class="dropdown-menu">
                                    @can('edit empleados')
                                    <a class="dropdown-item" href="{{ route('admin.empleados.edit', $emp) }}">
                                        <i class="ri ri-pencil-line me-1"></i> Editar
                                    </a>
                                    @endcan
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No se encontraron empleados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
           {{ $empleados->links('livewire.pagination') }}
        </div>
    </div>
</div>
