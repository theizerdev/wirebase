<div>
    @section('title', 'Estados')

    @push('styles')
    <style>
        .estado-hero { background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); color:#fff; border-radius:.75rem; padding:1.4rem 1.6rem; }
        .estado-hero h2 { color:#fff; margin:0; }
        .estado-hero p { opacity:.9; margin:0; }

        .stat-card { border:1px solid rgba(0,0,0,.06); border-radius:.65rem; padding:.9rem 1rem;
                     transition:all .2s; display:flex; align-items:center; gap:.85rem; height:100%; background:#fff; }
        .stat-card:hover { box-shadow:0 6px 18px rgba(0,0,0,.07); transform:translateY(-1px); }
        .stat-card .stat-icon { width:44px; height:44px; border-radius:11px; flex:0 0 44px;
                                display:flex; align-items:center; justify-content:center; font-size:1.15rem; }
        .stat-card .stat-value { font-size:1.35rem; font-weight:600; line-height:1; }
        .stat-card .stat-label { font-size:.72rem; color:var(--bs-secondary-color);
                                 text-transform:uppercase; letter-spacing:.4px; font-weight:600; }

        .estado-row { transition:background .12s; }
        .estado-row:hover { background:#f8f9ff; }
    </style>
    @endpush

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Estados</li>
            </ol>
        </nav>

        {{-- Hero + acciones --}}
        <div class="estado-hero mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="fw-semibold"><i class="ri ri-map-pin-2-line me-2"></i>Estados</h2>
                <p class="mt-1">Gestión de estados o provincias del sistema</p>
            </div>
            @can('create estados')
                <a href="{{ route('admin.estados.create') }}" class="btn btn-light btn-sm">
                    <i class="ri ri-add-line me-1"></i>Nuevo Estado
                </a>
            @endcan
        </div>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#ede9fe;color:#7c3aed;"><i class="ri ri-map-pin-2-line"></i></div>
                    <div>
                        <div class="stat-label">Total estados</div>
                        <div class="stat-value">{{ $totalEstados }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="ri ri-check-line"></i></div>
                    <div>
                        <div class="stat-label">Estados activos</div>
                        <div class="stat-value">{{ $estadosActivos }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="ri ri-close-line"></i></div>
                    <div>
                        <div class="stat-label">Estados inactivos</div>
                        <div class="stat-value">{{ $estadosInactivos }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#cffafe;color:#0891b2;"><i class="ri ri-building-2-line"></i></div>
                    <div>
                        <div class="stat-label">Con ciudades</div>
                        <div class="stat-value">{{ $estadosConCiudades }}</div>
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
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="Nombre, código...">
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
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-label-secondary" wire:click="clearFilters">
                                <i class="ri ri-eraser-line"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="mb-0"><i class="ri ri-map-pin-2-line me-2 text-primary"></i>Listado de estados</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                    Nombre @if($sortBy === 'nombre') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('codigo')" style="cursor: pointer;">
                                    Código @if($sortBy === 'codigo') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                               
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($estados as $estado)
                                <tr class="estado-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">{{ substr($estado->nombre, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $estado->nombre }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($estado->iso_3166_2)
                                            <span class="badge bg-light text-dark">{{ $estado->iso_3166_2 }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ri ri-more-2-line"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @can('edit estados')
                                                <a class="dropdown-item" href="{{ route('admin.estados.edit', $estado) }}">
                                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                                </a>
                                                @endcan
                                                @can('delete estados')
                                                <button type="button" class="dropdown-item text-danger"
                                                        wire:click="deleteEstado({{ $estado->id }})"
                                                        wire:confirm="¿Estás seguro de eliminar este estado?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="ri ri-map-pin-2-line" style="font-size:1.5rem;opacity:.3;"></i>
                                        <p class="mb-0 mt-1 small">No se encontraron estados</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $estados->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
