<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Estadísticas -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $totalMotos }}</h4>
                            <p class="mb-0">Total Modelos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-motorbike-line ri-24px"></i>
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
                            <h4 class="mb-1">{{ $motosActivas }}</h4>
                            <p class="mb-0">Modelos Activos</p>
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
                            <h4 class="mb-1">{{ $motosInactivas }}</h4>
                            <p class="mb-0">Modelos Inactivos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ri ri-close-circle-line ri-24px"></i>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Catálogo de Motocicletas</h5>
                            <p class="mb-0">Administra los modelos de motos disponibles</p>
                        </div>
                        @can('create motos')
                        <div>
                            <a href="{{ route('admin.motos.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo Modelo
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Marca, modelo, año..."
                                   wire:model.live.debounce.300ms="search">
                        </div>

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
                                <th wire:click="sort('marca')" style="cursor: pointer;">
                                    Marca
                                    @if($sortBy === 'marca')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sort('modelo')" style="cursor: pointer;">
                                    Modelo
                                    @if($sortBy === 'modelo')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sort('anio')" style="cursor: pointer;">
                                    Año
                                    @if($sortBy === 'anio')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sort('precio_venta_base')" style="cursor: pointer;">
                                    Precio Base
                                    @if($sortBy === 'precio_venta_base')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Empresa</th>
                                <th wire:click="sort('activo')" style="cursor: pointer;">
                                    Estado
                                    @if($sortBy === 'activo')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($motos as $moto)
                            <tr>
                                <td>{{ $moto->marca }}</td>
                                <td>{{ $moto->modelo }}</td>
                                <td>{{ $moto->anio }}</td>
                                <td>{{ number_format($moto->precio_venta_base, 2) }}</td>
                                <td>{{ $moto->empresa->razon_social ?? 'N/A' }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               id="statusSwitch{{ $moto->id }}"
                                               {{ $moto->activo ? 'checked' : '' }}
                                               @can('edit motos') wire:click="toggleStatus({{ $moto->id }})" @endcan>
                                        <label class="form-check-label" for="statusSwitch{{ $moto->id }}">
                                            {{ $moto->activo ? 'Activo' : 'Inactivo' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @can('view motos')
                                            <a class="dropdown-item" href="{{ route('admin.motos.details', $moto->id) }}">
                                                <i class="ri ri-eye-line me-1"></i>Ver Detalles
                                            </a>
                                            @endcan
                                            @can('view motos')
                                            <a class="dropdown-item" href="{{ route('admin.motos.unidades.index', $moto->id) }}">
                                                <i class="ri ri-motorbike-line me-1"></i> Gestionar Unidades
                                            </a>
                                            @endcan
                                            @can('edit motos')
                                            <a class="dropdown-item" href="{{ route('admin.motos.edit', $moto->id) }}">
                                                <i class="ri ri-pencil-line me-1"></i> Editar
                                            </a>
                                            @endcan
                                            @can('delete motos')
                                            <button type="button" class="dropdown-item text-danger"
                                                    wire:click="delete({{ $moto->id }})"
                                                    wire:confirm="¿Estás seguro de que deseas eliminar este modelo?">
                                                <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No se encontraron modelos que coincidan con los filtros</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                   {{ $motos->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
