<div>
    <div class="row">
        <!-- Stats -->
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $totalSorteos }}</h4>
                            <p class="mb-0">Total Sorteos</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-gift-line ri-24px"></i>
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
                            <h4 class="mb-1">{{ $totalGanadores }}</h4>
                            <p class="mb-0">Contratos Ganadores</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-trophy-line ri-24px"></i>
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
                            <h4 class="mb-1">{{ $contratosElegibles }}</h4>
                            <p class="mb-0">Elegibles Restantes</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ri ri-file-list-3-line ri-24px"></i>
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
                            <h4 class="mb-1">{{ $ultimoSorteo ? $ultimoSorteo->fecha_sorteo->format('d/m/Y') : 'N/A' }}</h4>
                            <p class="mb-0">Último Sorteo</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-calendar-check-line ri-24px"></i>
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
                            <h5 class="card-title mb-1">Gestión de Sorteos</h5>
                            <p class="mb-0">Historial de sorteos realizados y contratos ganadores</p>
                        </div>
                        <div>
                            <a href="{{ route('sorteo.app') }}" target="_blank" class="btn btn-primary">
                                <i class="ri ri-gift-line me-1"></i> Iniciar Sorteo
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Contrato, cliente, nombre sorteo..."
                                   wire:model.live.debounce.300ms="search">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Empresa</label>
                            <select class="form-select" wire:model.live="empresa_id">
                                <option value="">Todas las empresas</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->razon_social }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-label-secondary" wire:click="clearFilters">
                                <i class="ri ri-eraser-line"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sort('id')" style="cursor: pointer;">
                                    #
                                    @if($sortBy === 'id')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Contrato Ganador</th>
                                <th>Cliente</th>
                                <th>Empresa</th>
                                <th wire:click="sort('fecha_sorteo')" style="cursor: pointer;">
                                    Fecha Sorteo
                                    @if($sortBy === 'fecha_sorteo')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sort('total_contratos_elegibles')" style="cursor: pointer;">
                                    Elegibles
                                    @if($sortBy === 'total_contratos_elegibles')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sorteos as $sorteo)
                            <tr>
                                <td>{{ $sorteo->id }}</td>
                                <td>
                                    <span class="fw-bold text-primary">#{{ $sorteo->numero_contrato_ganador }}</span>
                                    <br>
                                    <small class="text-muted font-monospace">{{ Str::limit($sorteo->hash_validacion, 16) }}</small>
                                </td>
                                <td>
                                    @if($sorteo->ganador && $sorteo->ganador->contrato && $sorteo->ganador->contrato->cliente)
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $sorteo->ganador->contrato->cliente->nombre }} {{ $sorteo->ganador->contrato->cliente->apellido }}</span>
                                            <small class="text-muted">{{ $sorteo->ganador->contrato->cliente->documento ?? '' }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $sorteo->empresa->razon_social ?? 'N/A' }}</td>
                                <td>
                                    {{ $sorteo->fecha_sorteo->format('d/m/Y') }}
                                    <br>
                                    <small class="text-muted">{{ $sorteo->fecha_sorteo->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-label-info">{{ $sorteo->total_contratos_elegibles }}</span>
                                </td>
                                <td>
                                    @if($sorteo->estado === 'completado')
                                        <span class="badge bg-label-success">Completado</span>
                                    @else
                                        <span class="badge bg-label-danger">Anulado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.sorteo.detalle', $sorteo->id) }}">
                                                <i class="ri ri-eye-line me-1"></i> Ver Detalle
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="ri ri-gift-line ri-24px text-muted d-block mb-2"></i>
                                    No se han realizado sorteos aún.
                                    <br>
                                    <a href="{{ route('sorteo.app') }}" target="_blank" class="btn btn-sm btn-primary mt-2">
                                        <i class="ri ri-gift-line me-1"></i> Realizar Primer Sorteo
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                    {{ $sorteos->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
