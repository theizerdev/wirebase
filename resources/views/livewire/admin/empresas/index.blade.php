<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
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
                            <h4 class="mb-1">{{ $totalEmpresas }}</h4>
                            <p class="mb-0">Total Empresas</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-building-line ri-24px"></i>
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
                            <h4 class="mb-1">{{ $empresasActivas }}</h4>
                            <p class="mb-0">Empresas Activas</p>
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
                            <h4 class="mb-1">{{ $empresasInactivas }}</h4>
                            <p class="mb-0">Empresas Inactivas</p>
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
                            <h5 class="card-title mb-1">Lista de Empresas</h5>
                            <p class="mb-0">Administra las empresas registradas en el sistema</p>
                        </div>
                        @can('create empresas')
                        <div>
                            <a href="{{ route('admin.empresas.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nueva Empresa
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
                            <input type="text" class="form-control" placeholder="Razón social, documento..."
                                   wire:model.live.debounce.300ms="search">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="filters.status">
                                <option value="">Todos</option>
                                <option value="active">Activas</option>
                                <option value="inactive">Inactivas</option>
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

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-label-secondary" wire:click="resetFilters">
                                <i class="ri ri-eraser-line"></i> Limpiar filtros
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                    Nombre @if($sortBy == 'nombre') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('documento')" style="cursor: pointer;">
                                    Documento @if($sortBy == 'documento') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('email')" style="cursor: pointer;">
                                    Email @if($sortBy == 'email') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('telefono')" style="cursor: pointer;">
                                    Teléfono @if($sortBy == 'telefono') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('is_active')" style="cursor: pointer;">
                                    Estado @if($sortBy == 'is_active') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($empresas as $empresa)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($empresa->logo_path)
                                                <img src="{{ asset($empresa->logo_path) }}" alt="{{ $empresa->razon_social }}" class="rounded me-2" width="32" height="32">
                                            @else
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded bg-label-primary">{{ substr($empresa->razon_social, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $empresa->razon_social }}</h6>
                                                <small class="text-muted">{{ $empresa->razon_social }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $empresa->tipo_documento }} </strong> {{ $empresa->documento }}
                                    </td>
                                    <td>{{ $empresa->email }}</td>
                                    <td>{{ $empresa->telefono }}</td>
                                    <td>
                                        @if($empresa->status )
                                            <span class=" text-success"><i class="ri ri-checkbox-circle-line ri-24px"></i> Activado</span>
                                        @else
                                            <span class=" text-danger"><i class="ri ri-close-circle-line ri-24px"></i> Inactivo</span>
                                        @endif
                                    </td>


                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $empresa->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="ri ri-more-2-fill ri-24px"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $empresa->id }}">
                                                @can('view empresas')
                                                <a class="dropdown-item" href="{{ route('admin.empresas.show', $empresa) }}">
                                                    <i class="ri ri-eye-line me-1"></i> Ver
                                                </a>
                                                @endcan
                                                @can('edit empresas')
                                                <a class="dropdown-item" href="{{ route('admin.empresas.edit', $empresa) }}">
                                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                                </a>
                                                @endcan
                                                @can('delete empresas')
                                                <button class="dropdown-item text-danger" wire:click="delete({{ $empresa->id }})" wire:confirm="¿Estás seguro de eliminar esta empresa?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron empresas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Mostrando {{ $empresas->firstItem() }} a {{ $empresas->lastItem() }} de {{ $empresas->total() }} resultados
                        </div>
                        <div>
                            {{ $empresas->links('livewire.admin.pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
