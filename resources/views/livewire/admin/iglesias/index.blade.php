<div>
    @section('title', 'Extensiones')

    @push('styles')
    <style>
        .iglesia-hero { background: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%); color:#fff; border-radius:.75rem; padding:1.4rem 1.6rem; }
        .iglesia-hero h2 { color:#fff; margin:0; }
        .iglesia-hero p { opacity:.9; margin:0; }

        .stat-card { border:1px solid rgba(0,0,0,.06); border-radius:.65rem; padding:.9rem 1rem;
                     transition:all .2s; display:flex; align-items:center; gap:.85rem; height:100%; background:#fff; }
        .stat-card:hover { box-shadow:0 6px 18px rgba(0,0,0,.07); transform:translateY(-1px); }
        .stat-card .stat-icon { width:44px; height:44px; border-radius:11px; flex:0 0 44px;
                                display:flex; align-items:center; justify-content:center; font-size:1.15rem; }
        .stat-card .stat-value { font-size:1.35rem; font-weight:600; line-height:1; }
        .stat-card .stat-label { font-size:.72rem; color:var(--bs-secondary-color);
                                 text-transform:uppercase; letter-spacing:.4px; font-weight:600; }

        .iglesia-row { transition:background .12s; }
        .iglesia-row:hover { background:#f8f9ff; }
    </style>
    @endpush

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Extensiones</li>
            </ol>
        </nav>

        {{-- Hero + acciones --}}
        <div class="iglesia-hero mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="fw-semibold"><i class="ri ri-building-line me-2"></i>Extensiones</h2>
                <p class="mt-1">Gestión de extensiones del sistema</p>
            </div>
            @can('create iglesias')
                <a href="{{ route('admin.iglesias.create') }}" class="btn btn-light btn-sm">
                    <i class="ri ri-add-line me-1"></i>Nueva Extensión
                </a>
            @endcan
        </div>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#ede9fe;color:#7c3aed;"><i class="ri ri-building-line"></i></div>
                    <div>
                        <div class="stat-label">Total extensiones</div>
                        <div class="stat-value">{{ $totalIglesias }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="ri ri-check-line"></i></div>
                    <div>
                        <div class="stat-label">Extensiones activas</div>
                        <div class="stat-value">{{ $iglesiasActivas }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="ri ri-close-line"></i></div>
                    <div>
                        <div class="stat-label">Extensiones inactivas</div>
                        <div class="stat-value">{{ $iglesiasInactivas }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#cffafe;color:#0891b2;"><i class="ri ri-map-pin-line"></i></div>
                    <div>
                        <div class="stat-label">Zonas</div>
                        <div class="stat-value">{{ $totalZonas }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros compactos --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold"><i class="ri ri-search-line me-1"></i>Buscar</label>
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="Nombre, dirección...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Estado</label>
                        <select class="form-select form-select-sm" wire:model.live="status">
                            <option value="">Todos</option>
                            <option value="activa">Activas</option>
                            <option value="inactiva">Inactivas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Zona</label>
                        <select class="form-select form-select-sm" wire:model.live="zona">
                            <option value="">Todas</option>
                            @foreach($zonas as $z)
                                <option value="{{ $z }}">{{ $z }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Mostrar</label>
                        <select class="form-select form-select-sm" wire:model.live="perPage">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-label-secondary" wire:click="clearFilters">
                                <i class="ri ri-eraser-line"></i> Limpiar
                            </button>
                            @can('export iglesias')
                            <button type="button" class="btn btn-sm btn-label-success" wire:click="export">
                                <i class="ri ri-file-excel-line"></i> Exportar
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones de vista --}}
        <div class="d-flex flex-wrap gap-3 mb-4">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary btn-sm {{ $viewMode === 'grid' ? 'active' : '' }}" wire:click="toggleViewMode">
                    <i class="ri ri-grid-line me-1"></i> Cuadrícula
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm {{ $viewMode === 'list' ? 'active' : '' }}" wire:click="toggleViewMode">
                    <i class="ri ri-list-unordered me-1"></i> Lista
                </button>
            </div>
        </div>

        {{-- Contenido según vista --}}
        @if($viewMode === 'list')
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="mb-0"><i class="ri ri-building-line me-2 text-primary"></i>Listado de extensiones</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                        Nombre @if($sortBy === 'nombre') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                    </th>
                                    <th>Dirección</th>
                                    <th>Pastor</th>
                                    <th>Teléfono</th>
                                    <th wire:click="sortBy('zona')" style="cursor: pointer;">
                                        Zona @if($sortBy === 'zona') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                    </th>
                                    <th wire:click="sortBy('activa')" style="cursor: pointer;">
                                        Estado @if($sortBy === 'activa') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                    </th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($iglesias as $iglesia)
                                    <tr class="iglesia-row">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded bg-label-primary"><i class="ri ri-building-line"></i></span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $iglesia->nombre }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ Str::limit($iglesia->direccion, 50) }}</td>
                                        <td>
                                            @if($iglesia->pastor)
                                                {{ $iglesia->pastor->nombres }} {{ $iglesia->pastor->apellidos }}
                                            @else
                                                <span class="text-muted">Sin asignar</span>
                                            @endif
                                        </td>
                                        <td>{{ $iglesia->telefono }}</td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info-emphasis">{{ $iglesia->zona ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                       id="statusSwitch{{ $iglesia->id }}"
                                                       {{ $iglesia->activa ? 'checked' : '' }}
                                                       disabled>
                                                <label class="form-check-label" for="statusSwitch{{ $iglesia->id }}">
                                                    {{ $iglesia->activa ? 'Activa' : 'Inactiva' }}
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="ri ri-more-2-line"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('show iglesias')
                                                    <a href="{{ route('admin.iglesias.show', $iglesia->id) }}" class="dropdown-item">
                                                        <i class="ri ri-eye-line me-1"></i> Ver Detalles
                                                    </a>
                                                    <a href="{{ route('admin.iglesias.inventario.index', $iglesia->id) }}" class="dropdown-item">
                                                        <i class="ri ri-archive-line me-1"></i> Ver Inventario
                                                    </a>
                                                    <a href="{{ route('admin.iglesias.finanzas.index', $iglesia->id) }}" class="dropdown-item">
                                                        <i class="ri ri-money-dollar-circle-line me-1"></i> Ver Finanzas
                                                    </a>
                                                    @endcan
                                                    @can('edit iglesias')
                                                    <a href="{{ route('admin.iglesias.edit', $iglesia->id) }}" class="dropdown-item">
                                                        <i class="ri ri-edit-line me-1"></i> Editar
                                                    </a>
                                                    @endcan
                                                    @can('delete iglesias')
                                                    <button type="button" class="dropdown-item text-danger"
                                                            wire:click="deleteIglesia({{ $iglesia->id }})"
                                                            wire:confirm="¿Estás seguro de eliminar esta extensión?">
                                                        <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                    </button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="ri ri-building-line" style="font-size:1.5rem;opacity:.3;"></i>
                                            <p class="mb-0 mt-1 small">No se encontraron extensiones</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $iglesias->links('livewire.pagination') }}
                    </div>
                </div>
            </div>
        @else
            <div class="row g-4">
                @forelse($iglesias as $data)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="position-relative mb-3">
                                    <div class="avatar avatar-lg mx-auto mb-3">
                                        <span class="avatar-initial rounded-circle bg-primary border border-2 border-primary" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                                            <i class="ri ri-building-line"></i>
                                        </span>
                                    </div>
                                    @if ($data->activa)
                                        <span class="badge bg-success position-absolute top-0 end-0 mt-2">Activa</span>
                                    @else
                                        <span class="badge bg-secondary position-absolute top-0 end-0 mt-2">Inactiva</span>
                                    @endif
                                </div>
                                
                                <h6 class="mb-1 text-truncate" title="{{ $data->nombre }}">
                                    {{ $data->nombre }}
                                </h6>
                                
                                <div class="text-start small mb-3" style="min-height: 100px;">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="ri ri-map-pin-line text-muted me-2 small" style="min-width: 20px;"></i>
                                        <span class="text-truncate" title="{{ $data->direccion }}">{{ Str::limit($data->direccion, 40) }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="ri ri-user-line text-muted me-2 small" style="min-width: 20px;"></i>
                                        <span class="text-truncate" title="{{ $data->pastor ? $data->pastor->nombres . ' ' . $data->pastor->apellidos : 'Sin pastor' }}">
                                            {{ $data->pastor ? $data->pastor->nombres . ' ' . $data->pastor->apellidos : 'Sin pastor' }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="ri ri-phone-line text-muted me-2 small" style="min-width: 20px;"></i>
                                        <span class="text-truncate" title="{{ $data->telefono }}">{{ $data->telefono }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="ri ri-community-line text-muted me-2 small" style="min-width: 20px;"></i>
                                        <span class="text-truncate" title="{{ $data->zona }}">{{ $data->zona ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2 justify-content-center">
                                    @can('edit iglesias')
                                    <a href="{{ route('admin.iglesias.edit', $data->id) }}"
                                        class="btn btn-sm btn-outline-primary flex-fill" title="Editar">
                                        <i class="ri ri-pencil-line"></i>
                                    </a>
                                    @endcan
                                    @can('show iglesias')
                                    <a href="{{ route('admin.iglesias.show', $data->id) }}"
                                        class="btn btn-sm btn-outline-info flex-fill" title="Ver Detalles">
                                        <i class="ri ri-eye-line"></i>
                                    </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="ri ri-building-line" style="font-size:3rem;opacity:.3;"></i>
                            <h5 class="mt-3 text-muted">No se encontraron extensiones</h5>
                            <p class="mb-0">Intenta cambiar los filtros o crea nuevas extensiones</p>
                        </div>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-4">
                {{ $iglesias->links('livewire.pagination') }}
            </div>
        @endif
    </div>
</div>
