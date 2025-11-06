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

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Países</h6>
                            <h2 class="mb-0">{{ $totalPaises }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="ri ri-earth-line text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Países Activos</h6>
                            <h2 class="mb-0">{{ $paisesActivos }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="ri ri-checkbox-circle-line text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-danger border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Países Inactivos</h6>
                            <h2 class="mb-0">{{ $paisesInactivos }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="ri ri-close-circle-line text-danger" style="font-size: 1.5rem;"></i>
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
                            <h5 class="card-title mb-1">Lista de Países</h5>
                            <p class="mb-0">Administra los países del sistema</p>
                        </div>
                        @can('create paises')
                        <div>
                            <a href="{{ route('admin.paises.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo País
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Buscar</label>
                            <div class="input-group">
                                <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Buscar país...">
                                <span class="input-group-text"><i class="ri ri-search-line"></i></span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="activo">
                                <option value="">Todos</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="col-md-2">
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
                                <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                    Nombre @if($sortBy === 'nombre') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('codigo_iso2')" style="cursor: pointer;">
                                    Código ISO @if($sortBy === 'codigo_iso2') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('moneda_principal')" style="cursor: pointer;">
                                    Moneda @if($sortBy === 'moneda_principal') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('continente')" style="cursor: pointer;">
                                    Continente @if($sortBy === 'continente') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('activo')" style="cursor: pointer;">
                                    Estado @if($sortBy === 'activo') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paises as $pais)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">{{ substr($pais->nombre, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $pais->nombre }}</h6>
                                                <small class="text-muted">{{ $pais->codigo_iso2 }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $pais->codigo_iso2 }} / {{ $pais->codigo_iso3 }}</span>
                                    </td>
                                    <td>{{ $pais->moneda_principal }}</td>
                                    <td>{{ $pais->continente }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   id="statusSwitch{{ $pais->id }}"
                                                   {{ $pais->activo ? 'checked' : '' }}
                                                   @can('edit paises') wire:click="toggleStatus({{ $pais->id }})" @endcan>
                                            <label class="form-check-label" for="statusSwitch{{ $pais->id }}">
                                                {{ $pais->activo ? 'Activo' : 'Inactivo' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ri ri-more-2-line"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @can('edit paises')
                                                <a class="dropdown-item" href="{{ route('admin.paises.edit', $pais) }}">
                                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                                </a>
                                                @endcan
                                                @can('delete paises')
                                                <button type="button" class="dropdown-item text-danger"
                                                        wire:click="deletePais({{ $pais->id }})"
                                                        wire:confirm="¿Estás seguro de eliminar este país?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <i class="ri ri-folder-open-line ri-3x text-muted mb-3"></i>
                                        <p class="text-muted">No se encontraron países</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                   {{ $paises->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
