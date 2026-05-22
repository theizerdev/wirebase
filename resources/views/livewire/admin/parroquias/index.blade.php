<div>
    @section('title', 'Parroquias')

    @push('styles')
    <style>
        .parroquia-hero { background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); color:#fff; border-radius:.75rem; padding:1.4rem 1.6rem; }
        .parroquia-hero h2 { color:#fff; margin:0; }
        .parroquia-hero p { opacity:.9; margin:0; }

        .stat-card { border:1px solid rgba(0,0,0,.06); border-radius:.65rem; padding:.9rem 1rem;
                     transition:all .2s; display:flex; align-items:center; gap:.85rem; height:100%; background:#fff; }
        .stat-card:hover { box-shadow:0 6px 18px rgba(0,0,0,.07); transform:translateY(-1px); }
        .stat-card .stat-icon { width:44px; height:44px; border-radius:11px; flex:0 0 44px;
                                display:flex; align-items:center; justify-content:center; font-size:1.15rem; }
        .stat-card .stat-value { font-size:1.35rem; font-weight:600; line-height:1; }
        .stat-card .stat-label { font-size:.72rem; color:var(--bs-secondary-color);
                                 text-transform:uppercase; letter-spacing:.4px; font-weight:600; }

        .parroquia-row { transition:background .12s; }
        .parroquia-row:hover { background:#f8f9ff; }
    </style>
    @endpush

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Parroquias</li>
            </ol>
        </nav>

        {{-- Hero + acciones --}}
        <div class="parroquia-hero mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="fw-semibold"><i class="ri ri-community-line me-2"></i>Parroquias</h2>
                <p class="mt-1">Gestión de parroquias del sistema</p>
            </div>
            @can('create parroquias')
                <a href="{{ route('admin.parroquias.create') }}" class="btn btn-light btn-sm">
                    <i class="ri ri-add-line me-1"></i>Nueva Parroquia
                </a>
            @endcan
        </div>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#cffafe;color:#0891b2;"><i class="ri ri-community-line"></i></div>
                    <div>
                        <div class="stat-label">Total parroquias</div>
                        <div class="stat-value">{{ $totalParroquias }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="ri ri-check-line"></i></div>
                    <div>
                        <div class="stat-label">Parroquias activas</div>
                        <div class="stat-value">{{ $parroquiasActivas }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="ri ri-close-line"></i></div>
                    <div>
                        <div class="stat-label">Parroquias inactivas</div>
                        <div class="stat-value">{{ $parroquiasInactivas }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#ede9fe;color:#7c3aed;"><i class="ri ri-map-pin-line"></i></div>
                    <div>
                        <div class="stat-label">Municipios</div>
                        <div class="stat-value">{{ $totalParroquias > 0 ? $parroquias->unique('municipio_id')->count() : 0 }}</div>
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
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Municipio</label>
                        <select class="form-select form-select-sm" wire:model.live="filterMunicipio">
                            <option value="">Todos</option>
                            @foreach(\App\Models\Municipio::with('estado')->where('activo', true)->get() as $municipio)
                                <option value="{{ $municipio->id }}">{{ $municipio->nombre }} ({{ $municipio->estado->nombre }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Estado</label>
                        <select class="form-select form-select-sm" wire:model.live="filterActivo">
                            <option value="">Todos</option>
                            <option value="1">Activas</option>
                            <option value="0">Inactivas</option>
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
                <h6 class="mb-0"><i class="ri ri-community-line me-2 text-primary"></i>Listado de parroquias</h6>
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
                                <th wire:click="sortBy('municipio_id')" style="cursor: pointer;">
                                    Municipio @if($sortBy === 'municipio_id') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('activo')" style="cursor: pointer;">
                                    Estado @if($sortBy === 'activo') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($parroquias as $parroquia)
                                <tr class="parroquia-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">{{ substr($parroquia->nombre, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $parroquia->nombre }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($parroquia->codigo)
                                            <span class="badge bg-light text-dark">{{ $parroquia->codigo }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($parroquia->municipio)
                                            <span class="badge bg-info-subtle text-info-emphasis">{{ $parroquia->municipio->nombre }}</span>
                                            <small class="text-muted">({{ $parroquia->municipio->estado->nombre }})</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   id="statusSwitch{{ $parroquia->id }}"
                                                   {{ $parroquia->activo ? 'checked' : '' }}
                                                   @can('edit parroquias') wire:click="toggleStatus({{ $parroquia->id }})" @endcan>
                                            <label class="form-check-label" for="statusSwitch{{ $parroquia->id }}">
                                                {{ $parroquia->activo ? 'Activa' : 'Inactiva' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ri ri-more-2-line"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @can('edit parroquias')
                                                <a class="dropdown-item" href="{{ route('admin.parroquias.edit', $parroquia) }}">
                                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                                </a>
                                                @endcan
                                                @can('delete parroquias')
                                                <button type="button" class="dropdown-item text-danger"
                                                        wire:click="deleteParroquia({{ $parroquia->id }})"
                                                        wire:confirm="¿Estás seguro de eliminar esta parroquia?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="ri ri-community-line" style="font-size:1.5rem;opacity:.3;"></i>
                                        <p class="mb-0 mt-1 small">No se encontraron parroquias</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $parroquias->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>