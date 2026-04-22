<div>
    <!-- Alertas -->
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
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $totalConceptos }}</h4>
                            <p class="mb-0">Total Conceptos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-price-tag-3-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $conceptosActivos }}</h4>
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
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $conceptosInactivos }}</h4>
                            <p class="mb-0">Inactivos</p>
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
                    <h5 class="card-title mb-1">Conceptos de Pago</h5>
                    <p class="mb-0">Gestión de conceptos de facturación</p>
                </div>
                @can('create conceptos pago')
                <a href="{{ route('admin.conceptos-pago.create') }}" class="btn btn-primary">
                    <i class="ri ri-add-line"></i> Nuevo Concepto
                </a>
                @endcan
            </div>
        </div>
        <div class="card-header border-bottom">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Nombre o descripción...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select wire:model.live="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mostrar</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button wire:click="clearFilters" class="btn btn-label-secondary">
                        <i class="ri ri-eraser-line"></i> Limpiar
                    </button>
                    <button wire:click="export" class="btn btn-label-success">
                        <i class="mdi mdi-file-excel"></i> Exportar
                    </button>
                </div>
            </div>
        </div>
            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                <i class="ri ri-price-tag-3-line me-1"></i>Nombre
                                @if($sortBy === 'nombre')
                                    <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('descripcion')" style="cursor: pointer;">
                                <i class="ri ri-file-text-line me-1"></i>Descripción
                                @if($sortBy === 'descripcion')
                                    <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('activo')" style="cursor: pointer;">
                                <i class="ri ri-toggle-line me-1"></i>Estado
                                @if($sortBy === 'activo')
                                    <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                @endif
                            </th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                <tbody>
                    @forelse($conceptos as $concepto)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-primary">{{ substr($concepto->nombre, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $concepto->nombre }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $concepto->descripcion ?? '-' }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           wire:click="toggleStatus({{ $concepto->id }})"
                                           {{ $concepto->activo ? 'checked' : '' }}
                                           id="switch{{ $concepto->id }}">
                                    <label class="form-check-label" for="switch{{ $concepto->id }}">
                                        <span class="badge bg-label-{{ $concepto->activo ? 'success' : 'secondary' }}">
                                            {{ $concepto->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @can('edit conceptos pago')
                                    <a href="{{ route('admin.conceptos-pago.edit', $concepto) }}"
                                       class="btn btn-sm btn-icon btn-text-secondary rounded-pill"
                                       title="Editar">
                                        <i class="ri ri-edit-line ri ri-20px"></i>
                                    </a>
                                    @endcan
                                    @can('delete conceptos pago')
                                    <button wire:click="delete({{ $concepto->id }})"
                                            wire:confirm="¿Eliminar el concepto {{ $concepto->nombre }}?"
                                            class="btn btn-sm btn-icon btn-text-danger rounded-pill"
                                            title="Eliminar">
                                        <i class="ri ri-delete-bin-7-line ri ri-20px"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="ri ri-price-tag-3-line ri ri-48px text-muted mb-2"></i>
                                <h6 class="text-muted">No hay conceptos de pago</h6>
                                <p class="text-muted mb-0">Crea el primer concepto de pago</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

            <div class="card-footer">
                {{ $conceptos->links('livewire.pagination')}}
            </div>
        
            </div>
        </div>
    </div>
</div>
