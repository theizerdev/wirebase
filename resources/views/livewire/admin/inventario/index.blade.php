<div>
    <style>
        .inventario-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #e8e8e8;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 16px;
        }
        
        .stat-label {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 4px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
        }
        
        .compact-filters {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .compact-filters .form-control,
        .compact-filters .form-select {
            font-size: 13px;
            padding: 8px 12px;
        }
        
        .compact-filters .form-label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
            color: #6c757d;
        }
        
        .modern-table th {
            font-weight: 600;
            font-size: 13px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e8e8e8;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s;
        }
        
        .modern-table th:hover {
            color: #667eea;
            background: #f8f9fa;
        }
        
        .modern-table td {
            vertical-align: middle;
            font-size: 14px;
        }
        
        .modern-table tbody tr:hover {
            background: #f8f9fa;
        }
    </style>

    {{-- Hero Section --}}
    <div class="inventario-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h2 class="fw-semibold mb-2">
                <i class="ri ri-archive-line me-2"></i>Inventario General
            </h2>
            <p class="mb-0 opacity-75">Gestión centralizada de bienes y activos de todas las extensiones</p>
        </div>
        <div class="d-flex gap-2">
            @can('access iglesias')
                <button type="button" 
                        class="btn btn-light btn-sm"
                        wire:click="exportCsv"
                        title="Exportar a CSV">
                    <i class="ri ri-download-line me-1"></i>Exportar
                </button>
                <a href="{{ route('admin.inventario.create') }}" class="btn btn-light btn-sm">
                    <i class="ri ri-add-line me-1"></i>Nuevo Ítem
                </a>
            @endcan
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:#ede9fe;color:#7c3aed;">
                        <i class="ri ri-archive-line"></i>
                    </div>
                    <div>
                        <div class="stat-label">Total de Ítems</div>
                        <div class="stat-value">{{ number_format($stats['total_items']) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:#d1fae5;color:#059669;">
                        <i class="ri ri-money-dollar-circle-line"></i>
                    </div>
                    <div>
                        <div class="stat-label">Valor Total</div>
                        <div class="stat-value">${{ number_format($stats['total_valor'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:#dbeafe;color:#2563eb;">
                        <i class="ri ri-building-line"></i>
                    </div>
                    <div>
                        <div class="stat-label">Extensiones</div>
                        <div class="stat-value">{{ $stats['total_iglesias'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;">
                        <i class="ri ri-price-tag-3-line"></i>
                    </div>
                    <div>
                        <div class="stat-label">Categorías</div>
                        <div class="stat-value">{{ $stats['categorias_unicas'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Compact Filters --}}
    <div class="compact-filters">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Buscar</label>
                <input type="text" 
                       class="form-control" 
                       placeholder="Nombre, categoría..."
                       wire:model.live.debounce.300ms="search">
            </div>

            <div class="col-md-2">
                <label class="form-label">Extensión</label>
                <select class="form-select" wire:model.live="iglesiaFilter">
                    <option value="">Todas</option>
                    @foreach($iglesias as $iglesia)
                        <option value="{{ $iglesia->id }}">{{ $iglesia->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Categoría</label>
                <select class="form-select" wire:model.live="category">
                    <option value="">Todas</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Condición</label>
                <select class="form-select" wire:model.live="condition">
                    <option value="">Todas</option>
                    @foreach($conditions as $cond)
                        <option value="{{ $cond }}">{{ $cond }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label">Mostrar</label>
                <select class="form-select" wire:model.live="perPage">
                    <option value="12">12</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="button" 
                        class="btn btn-outline-secondary btn-sm w-100" 
                        wire:click="clearFilters"
                        title="Limpiar filtros">
                    <i class="ri ri-eraser-line me-1"></i> Limpiar
                </button>
            </div>
        </div>
    </div>

    {{-- Modern Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-datatable table-responsive">
            <table class="table modern-table mb-0">
                <thead>
                    <tr>
                        <th wire:click="sortBy('nombre')" class="ps-4">
                            Nombre 
                            @if($sortBy === 'nombre') 
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> 
                            @endif
                        </th>
                        <th>Extensión</th>
                        <th wire:click="sortBy('categoria')">
                            Categoría 
                            @if($sortBy === 'categoria') 
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> 
                            @endif
                        </th>
                        <th>Cantidad</th>
                        <th>Valor</th>
                        <th>Condición</th>
                        <th wire:click="sortBy('fecha_adquisicion')">
                            Fecha Adq. 
                            @if($sortBy === 'fecha_adquisicion') 
                                <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> 
                            @endif
                        </th>
                        <th class="pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="ri ri-archive-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $item->nombre }}</h6>
                                        @if($item->descripcion)
                                            <small class="text-muted">{{ Str::limit($item->descripcion, 50) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($item->iglesia)
                                    <a href="{{ route('admin.iglesias.show', $item->iglesia_id) }}" class="text-decoration-none">
                                        <span class="badge bg-label-secondary">{{ $item->iglesia->nombre }}</span>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->categoria)
                                    <span class="badge bg-label-info">{{ $item->categoria }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $item->cantidad }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-success">${{ number_format($item->valor ?? 0, 2) }}</span>
                            </td>
                            <td>
                                @if($item->condicion)
                                    <span class="badge bg-label-warning">{{ $item->condicion }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->fecha_adquisicion)
                                    <span class="text-muted">{{ \Carbon\Carbon::parse($item->fecha_adquisicion)->format('d/m/Y') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="pe-4">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ri ri-more-2-line"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @can('access iglesias')
                                            <a href="{{ route('admin.inventario.edit', $item->id) }}" class="dropdown-item">
                                                <i class="ri ri-edit-line me-1"></i> Editar
                                            </a>
                                            <a href="{{ route('admin.inventario.show', $item->id) }}" class="dropdown-item">
                                                <i class="ri ri-eye-line me-1"></i> Ver Detalle
                                            </a>
                                            <button type="button" class="dropdown-item text-danger"
                                                    wire:click="deleteItem({{ $item->id }})"
                                                    wire:confirm="¿Estás seguro de eliminar este ítem?">
                                                <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="avatar avatar-lg mb-3">
                                    <span class="avatar-initial rounded-circle bg-label-secondary">
                                        <i class="ri ri-folder-open-line ri-3x"></i>
                                    </span>
                                </div>
                                <h6 class="text-muted">No se encontraron ítems en el inventario</h6>
                                <p class="text-muted mb-0">Comienza agregando tu primer ítem</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white border-top-0">
            {{ $items->links('livewire.pagination') }}
        </div>
    </div>
</div>
