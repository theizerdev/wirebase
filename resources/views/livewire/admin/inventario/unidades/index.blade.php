<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 text-primary">Inventario General de Unidades</h4>
                    <p class="mb-0">Gestión de todas las unidades físicas de motocicletas</p>
                </div>
                @can('create moto unidades')
                <a href="{{ route('admin.inventario.unidades.create') }}" class="btn btn-primary">
                    <i class="ri ri-add-line me-1"></i> Nueva Unidad
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Estadísticas -->
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $totalUnidades }}</h4>
                            <p class="mb-0">Total Unidades</p>
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

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $disponibles }}</h4>
                            <p class="mb-0">Disponibles</p>
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

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $reservadas }}</h4>
                            <p class="mb-0">Reservadas</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ri ri-time-line ri-24px"></i>
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
                            <h4 class="mb-1">{{ $vendidas }}</h4>
                            <p class="mb-0">Vendidas</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-shopping-cart-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <!-- Filtros -->
        <div class="card-header border-bottom">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" class="form-control" placeholder="VIN, Motor, Placa, Color..."
                           wire:model.live.debounce.300ms="search">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Modelo (Moto)</label>
                    <select class="form-select" wire:model.live="moto_id">
                        <option value="">Todos los modelos</option>
                        @foreach($motos as $moto)
                            <option value="{{ $moto->id }}">{{ $moto->titulo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" wire:model.live="estado">
                        <option value="">Todos los estados</option>
                        <option value="disponible">Disponible</option>
                        <option value="reservado">Reservado</option>
                        <option value="vendido">Vendido</option>
                        <option value="mantenimiento">Mantenimiento</option>
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
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div class="col-md-9 d-flex align-items-end justify-content-end gap-2">
                    <button type="button" class="btn btn-label-secondary" wire:click="clearFilters">
                        <i class="ri ri-eraser-line"></i> Limpiar Filtros
                    </button>
                    <button type="button" class="btn btn-label-success" wire:click="export">
                        <i class="mdi mdi-file-excel me-1"></i> Exportar
                    </button>
                </div>
            </div>
        </div>

        <div class="card-datatable table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Modelo</th>
                        <th wire:click="sort('vin')" style="cursor: pointer;">
                            VIN / Chasis
                            @if($sortBy === 'vin')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                            @endif
                        </th>
                        <th>Color / Placa</th>
                        <th wire:click="sort('precio_venta')" style="cursor: pointer;">
                            Precio Venta
                            @if($sortBy === 'precio_venta')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                            @endif
                        </th>
                        <th wire:click="sort('estado')" style="cursor: pointer;">
                            Estado
                            @if($sortBy === 'estado')
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                            @endif
                        </th>
                        <th>Empresa / Sucursal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unidades as $unidad)
                    <tr>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-primary">{{ $unidad->moto->titulo ?? 'N/A' }}</span>
                                <small class="text-muted">{{ $unidad->moto->anio ?? '' }}</small>
                            </div>
                        </td>
                        <td class="fw-bold">{{ $unidad->vin }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span>{{ $unidad->color_especifico }}</span>
                                <small class="text-muted">{{ $unidad->placa ?? 'S/P' }}</small>
                            </div>
                        </td>
                        <td>${{ number_format($unidad->precio_venta, 2) }}</td>
                        <td>
                            @php
                                $badges = [
                                    'disponible' => 'bg-label-success',
                                    'reservado' => 'bg-label-warning',
                                    'vendido' => 'bg-label-info',
                                    'mantenimiento' => 'bg-label-danger'
                                ];
                            @endphp
                            <span class="badge {{ $badges[$unidad->estado] ?? 'bg-label-primary' }}">
                                {{ ucfirst($unidad->estado) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span>{{ $unidad->empresa->razon_social ?? 'N/A' }}</span>
                                <small class="text-muted">{{ $unidad->sucursal->nombre ?? 'N/A' }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ri ri-more-2-line"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('admin.inventario.unidades.show', $unidad->id) }}">
                                        <i class="ri ri-eye-line me-1"></i> Ver Detalle
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.inventario.unidades.history', $unidad->id) }}">
                                        <i class="ri ri-time-line me-1"></i> Historial
                                    </a>
                                    @if($unidad->estado == 'disponible')
                                    @can('delete moto unidades')
                                    <button type="button" class="dropdown-item text-danger"
                                            wire:click="delete({{ $unidad->id }})"
                                            wire:confirm="¿Estás seguro de eliminar esta unidad?">
                                        <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                    </button>
                                    @endcan
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-center">
                                <i class="ri ri-inbox-line fs-1 text-muted"></i>
                                <p class="mt-2">No se encontraron unidades.</p>
                                @can('create moto unidades')
                                <a href="{{ route('admin.inventario.unidades.create') }}" class="btn btn-sm btn-primary">
                                    Registrar Unidad
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $unidades->links('livewire.pagination') }}
        </div>
    </div>
</div>
