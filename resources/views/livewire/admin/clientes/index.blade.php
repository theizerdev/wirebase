<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Estadísticas -->
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $totalClientes }}</h4>
                            <p class="mb-0">Total Clientes</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-user-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $clientesActivos }}</h4>
                            <p class="mb-0">Clientes Activos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-user-follow-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $clientesInactivos }}</h4>
                            <p class="mb-0">Clientes Inactivos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ri ri-user-unfollow-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $clientesEliminados }}</h4>
                            <p class="mb-0">Clientes Eliminados</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ri ri-delete-bin-2-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <ul class="nav nav-pills" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ !$showDeleted ? 'active' : '' }}" 
                                    id="clientes-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#clientes" 
                                    type="button" 
                                    role="tab" 
                                    wire:click="setShowDeleted(false)">
                                Clientes Activos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $showDeleted ? 'active' : '' }}" 
                                    id="eliminados-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#eliminados" 
                                    type="button" 
                                    role="tab" 
                                    wire:click="setShowDeleted(true)">
                                Clientes Eliminados
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                {{ $showDeleted ? 'Clientes Eliminados' : 'Directorio de Clientes' }}
                            </h5>
                            <p class="mb-0">
                                {{ $showDeleted ? 'Lista de clientes eliminados que pueden ser restaurados' : 'Administra los clientes registrados en el sistema' }}
                            </p>
                        </div>
                        @can('create clientes')
                        <div>
                            @unless($showDeleted)
                            <a href="{{ route('admin.clientes.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo Cliente
                            </a>
                            @endunless
                        </div>
                        @endcan
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Nombre, documento, teléfono..."
                                   wire:model.live.debounce.300ms="search">
                        </div>

                        @unless($showDeleted)
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="activo">
                                <option value="">Todos los estados</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Empresa</label>
                            <select class="form-select" wire:model.live="empresa_id">
                                <option value="">Todas las empresas</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->razon_social }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endunless

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
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sort('nombre')" style="cursor: pointer;">
                                    Cliente
                                    @if(!$showDeleted && $sortBy === 'nombre')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sort('documento')" style="cursor: pointer;">
                                    Documento
                                    @if(!$showDeleted && $sortBy === 'documento')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sort('telefono')" style="cursor: pointer;">
                                    Contacto
                                    @if(!$showDeleted && $sortBy === 'telefono')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Empresa</th>
                                @unless($showDeleted)
                                <th wire:click="sort('activo')" style="cursor: pointer;">
                                    Estado
                                    @if($sortBy === 'activo')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                @endunless
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clientes as $cliente)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('admin.clientes.show', $cliente->id) }}" class="fw-bold text-truncate">
                                            {{ $cliente->nombre }} {{ $cliente->apellido }}
                                        </a>
                                        <small class="text-truncate text-muted">{{ $cliente->email }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary">{{ $cliente->tipo_documento }}</span>
                                    <span class="fw-bold">{{ $cliente->documento }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><i class="ri ri-phone-line me-1 text-primary"></i> {{ $cliente->telefono }}</span>
                                        @if($cliente->telefono_alternativo)
                                            <small class="text-muted">{{ $cliente->telefono_alternativo }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $cliente->empresa->razon_social ?? 'N/A' }}</td>
                                @unless($showDeleted)
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               id="statusSwitch{{ $cliente->id }}"
                                               {{ $cliente->activo ? 'checked' : '' }}
                                               @can('edit clientes') wire:click="toggleStatus({{ $cliente->id }})" @endcan>
                                        <label class="form-check-label" for="statusSwitch{{ $cliente->id }}">
                                            {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                                        </label>
                                    </div>
                                </td>
                                @endunless
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.clientes.show', $cliente->id) }}">
                                                <i class="ri ri-eye-line me-1"></i> Ver Detalle
                                            </a>
                                            @unless($showDeleted)
                                            @can('edit clientes')
                                            <a class="dropdown-item" href="{{ route('admin.clientes.edit', $cliente->id) }}">
                                                <i class="ri ri-pencil-line me-1"></i> Editar
                                            </a>
                                            @endcan
                                            @can('delete clientes')
                                            <button type="button" class="dropdown-item text-danger"
                                                    wire:click="delete({{ $cliente->id }})"
                                                    wire:confirm="¿Estás seguro de que deseas eliminar este cliente?">
                                                <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                            </button>
                                            @endcan
                                            @else
                                            @can('delete clientes')
                                            <button type="button" class="dropdown-item text-success"
                                                    wire:click="restore({{ $cliente->id }})"
                                                    wire:confirm="¿Estás seguro de que deseas restaurar este cliente?">
                                                <i class="ri ri-refresh-line me-1"></i> Restaurar
                                            </button>
                                            @endcan
                                            @endunless
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $showDeleted ? 6 : 7 }}" class="text-center">
                                    @if($showDeleted)
                                        No se encontraron clientes eliminados que coincidan con los filtros
                                    @else
                                        No se encontraron clientes que coincidan con los filtros
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                   {{ $clientes->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>