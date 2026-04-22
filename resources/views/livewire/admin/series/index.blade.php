<div>
    <div class="row mb-3">
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $series->total() }}</h4>
                            <p class="mb-0">Total Series</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-file-text-line ri-24px"></i>
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
                            <h4 class="mb-1">{{ $series->where('activo', true)->count() }}</h4>
                            <p class="mb-0">Series Activas</p>
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
                            <h4 class="mb-1">{{ $series->where('activo', false)->count() }}</h4>
                            <p class="mb-0">Series Inactivas</p>
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
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Series de Documentos</h5>
                <small class="text-muted">Gestión de numeración de documentos</small>
            </div>
            @can('create series')
            <a href="{{ route('admin.series.create') }}" class="btn btn-primary">
                <i class="ri ri-add-line me-1"></i> Nueva Serie
            </a>
            @endcan
        </div>

        <div class="card-header border-bottom">
            @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <i class="ri ri-check-line me-2"></i>{{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
            <!-- Filtros -->
            <div class="row g-3 mb-2">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Buscar por serie...">
                </div>
        
        
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select wire:model.live="tipo_documento" class="form-select">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Mostrar</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2 justify-content-end">
                    <button type="button" class="btn btn-label-secondary" wire:click="clearFilters">
                        <i class="ri ri-eraser-line"></i> Limpiar
                    </button>
                </div>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th><i class="ri ri-file-text-line me-1"></i>Tipo</th>
                            <th><i class="ri ri-hashtag me-1"></i>Serie</th>
                            <th><i class="ri ri-number-1 me-1"></i>Correlativo</th>
                            <th><i class="ri ri-building-line me-1"></i>Empresa</th>
                            <th><i class="ri ri-community-line me-1"></i>Sucursal</th>
                            <th><i class="ri ri-toggle-line me-1"></i>Estado</th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($series as $serie)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @php
                                        $iconos = [
                                            'factura' => 'ri ri-file-text-line text-primary',
                                            'boleta' => 'ri ri-receipt-line text-info',
                                            'nota_credito' => 'ri ri-file-reduce-line text-warning',
                                            'recibo' => 'ri ri-file-copy-line text-success'
                                        ];
                                    @endphp
                                    <i class="{{ $iconos[$serie->tipo_documento] ?? 'ri ri-file-line' }} me-2"></i>
                                    <span>{{ $tipos[$serie->tipo_documento] ?? $serie->tipo_documento }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-dark fs-6">{{ $serie->serie }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ str_pad($serie->correlativo_actual, $serie->longitud_correlativo, '0', STR_PAD_LEFT) }}</span>
                                    <small class="text-muted">Longitud: {{ $serie->longitud_correlativo }}</small>
                                </div>
                            </td>
                            <td>{{ $serie->empresa->nombre ?? '-' }}</td>
                            <td>{{ $serie->sucursal->nombre ?? '-' }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           wire:click="toggleActivo({{ $serie->id }})"
                                           {{ $serie->activo ? 'checked' : '' }}
                                           id="switch{{ $serie->id }}">
                                    <label class="form-check-label" for="switch{{ $serie->id }}">
                                        <span class="badge bg-label-{{ $serie->activo ? 'success' : 'secondary' }}">
                                            {{ $serie->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @can('edit series')
                                    <a href="{{ route('admin.series.edit', $serie) }}"
                                       class="btn btn-sm btn-icon btn-text-secondary rounded-pill"
                                       title="Editar">
                                        <i class="ri ri-edit-line ri ri-20px"></i>
                                    </a>
                                    @endcan
                                    @can('delete series')
                                    <button wire:click="delete({{ $serie->id }})"
                                            wire:confirm="¿Eliminar la serie {{ $serie->serie }}?"
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
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ri ri-file-list-3-line ri ri-48px text-muted mb-2"></i>
                                    <h6 class="text-muted">No hay series registradas</h6>
                                    <p class="text-muted mb-0">Crea tu primera serie para comenzar</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="card-footer">
                   {{ $series->links('livewire.pagination')}}
                </div>
        </div>
    </div>

    <!-- Alertas -->

</div>
