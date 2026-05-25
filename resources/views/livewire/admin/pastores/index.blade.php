<div>
    @section('title', 'Pastores')

    @push('styles')
    <style>
        .pastor-hero { background: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%); color:#fff; border-radius:.75rem; padding:1.4rem 1.6rem; }
        .pastor-hero h2 { color:#fff; margin:0; }
        .pastor-hero p { opacity:.9; margin:0; }

        .stat-card { border:1px solid rgba(0,0,0,.06); border-radius:.65rem; padding:.9rem 1rem;
                     transition:all .2s; display:flex; align-items:center; gap:.85rem; height:100%; background:#fff; }
        .stat-card:hover { box-shadow:0 6px 18px rgba(0,0,0,.07); transform:translateY(-1px); }
        .stat-card .stat-icon { width:44px; height:44px; border-radius:11px; flex:0 0 44px;
                                display:flex; align-items:center; justify-content:center; font-size:1.15rem; }
        .stat-card .stat-value { font-size:1.35rem; font-weight:600; line-height:1; }
        .stat-card .stat-label { font-size:.72rem; color:var(--bs-secondary-color);
                                 text-transform:uppercase; letter-spacing:.4px; font-weight:600; }

        .pastor-row { transition:background .12s; }
        .pastor-row:hover { background:#f8f9ff; }
    </style>
    @endpush

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Pastores</li>
            </ol>
        </nav>

        {{-- Hero + acciones --}}
        <div class="pastor-hero mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="fw-semibold"><i class="ri ri-user-star-line me-2"></i>Pastores</h2>
                <p class="mt-1">Gestión de pastores del sistema</p>
            </div>
            @can('create pastores')
                <a href="{{ route('admin.pastores.create') }}" class="btn btn-light btn-sm">
                    <i class="ri ri-add-line me-1"></i>Nuevo Pastor
                </a>
            @endcan
        </div>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#ede9fe;color:#7c3aed;"><i class="ri ri-user-star-line"></i></div>
                    <div>
                        <div class="stat-label">Total pastores</div>
                        <div class="stat-value">{{ $totalPastores }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="ri ri-check-line"></i></div>
                    <div>
                        <div class="stat-label">Pastores activos</div>
                        <div class="stat-value">{{ $pastoresActivos }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="ri ri-close-line"></i></div>
                    <div>
                        <div class="stat-label">Pastores inactivos</div>
                        <div class="stat-value">{{ $pastoresInactivos }}</div>
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
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="Nombre, documento, código...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Estado</label>
                        <select class="form-select form-select-sm" wire:model.live="status">
                            <option value="">Todos</option>
                            <option value="activo">Activos</option>
                            <option value="inactivo">Inactivos</option>
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
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-label-secondary" wire:click="clearFilters">
                                <i class="ri ri-eraser-line"></i> Limpiar
                            </button>
                            @can('export pastores')
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
            
            @if($zona !== '')
            <button type="button" class="btn btn-primary btn-sm" wire:click="descargarPlanillasZona" wire:loading.attr="disabled" wire:target="descargarPlanillasZona">
                <span wire:loading.remove wire:target="descargarPlanillasZona">
                    <i class="ri ri-download-cloud-line me-1"></i> Planillas Zona {{ $zona }}
                </span>
                <span wire:loading wire:target="descargarPlanillasZona">
                    <i class="ri ri-loader-4-line ri-spin me-1"></i> Generando...
                </span>
            </button>
            @endif
        </div>

        {{-- Contenido según vista --}}
        @if($viewMode === 'list')
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="mb-0"><i class="ri ri-user-star-line me-2 text-primary"></i>Listado de pastores</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th wire:click="sortBy('codigo')" style="cursor: pointer;">
                                        Código @if($sortBy === 'codigo') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                    </th>
                                    <th wire:click="sortBy('nombres')" style="cursor: pointer;">
                                        Nombres @if($sortBy === 'nombres') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                    </th>
                                    <th wire:click="sortBy('apellidos')" style="cursor: pointer;">
                                        Apellidos @if($sortBy === 'apellidos') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                    </th>
                                    <th wire:click="sortBy('documento')" style="cursor: pointer;">
                                        Documento @if($sortBy === 'documento') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                    </th>
                                    <th wire:click="sortBy('nivel_ministerial')" style="cursor: pointer;">
                                        Nivel Ministerial @if($sortBy === 'nivel_ministerial') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                    </th>
                                    <th wire:click="sortBy('zona')" style="cursor: pointer;">
                                        Zona @if($sortBy === 'zona') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                    </th>
                                    <th wire:click="sortBy('status')" style="cursor: pointer;">
                                        Estado @if($sortBy === 'status') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                    </th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pastores as $pastor)
                                    <tr class="pastor-row">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded bg-label-primary">{{ substr($pastor->nombres, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $pastor->codigo ?: '-' }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $pastor->nombres . ' ' . $pastor->apellidos }}</h6>
                                            </div>
                                        </td>
                                        <td>{{ $pastor->documento }}</td>
                                        <td>{{ $pastor->nivel_ministerial }}</td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info-emphasis">{{ $pastor->zona }}</span>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                       id="statusSwitch{{ $pastor->id }}"
                                                       {{ $pastor->status ? 'checked' : '' }}
                                                       @can('edit pastores') wire:click="toggleStatus({{ $pastor->id }})" @endcan>
                                                <label class="form-check-label" for="statusSwitch{{ $pastor->id }}">
                                                    {{ $pastor->status ? 'Activo' : 'Inactivo' }}
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="ri ri-more-2-line"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('view pastores')
                                                    <a href="{{ route('admin.pastores.show', $pastor->id) }}" class="dropdown-item">
                                                        <i class="ri ri-eye-line me-1"></i> Ver
                                                    </a>
                                                    @endcan
                                                    @can('view pastores')
                                                    <a href="{{ route('admin.pastores.planilla', $pastor->id) }}" class="dropdown-item" target="_blank">
                                                        <i class="ri ri-file-list-line me-1"></i> Planilla
                                                    </a>
                                                    @endcan
                                                    @can('edit pastores')
                                                    <a href="{{ route('admin.pastores.edit', $pastor->id) }}" class="dropdown-item">
                                                        <i class="ri ri-edit-line me-1"></i> Editar
                                                    </a>
                                                    @endcan
                                                    @can('delete pastores')
                                                    <button type="button" class="dropdown-item text-danger"
                                                            wire:click="deletePastor({{ $pastor->id }})"
                                                            wire:confirm="¿Estás seguro de eliminar este pastor?">
                                                        <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                    </button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="ri ri-user-line" style="font-size:1.5rem;opacity:.3;"></i>
                                            <p class="mb-0 mt-1 small">No se encontraron pastores</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $pastores->links('livewire.pagination') }}
                    </div>
                </div>
            </div>
        @else
            <div class="row g-4">
                @forelse($pastores as $data)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="position-relative mb-3">
                                    <div class="avatar avatar-lg mx-auto mb-3">
                                        @if ($data->foto != null)
                                            <img class="rounded-circle img-fluid border border-2 border-primary"
                                                src="/pastores/{{ str_replace(' ', '',$data->foto) }}"
                                                alt="Foto de {{ $data->nombres }} {{ $data->apellidos }}" 
                                                style="width: 80px; height: 80px; object-fit: cover;" />
                                        @else
                                            <span class="avatar-initial rounded-circle bg-primary border border-2 border-primary" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                                {{ strtoupper(substr($data->nombres, 0, 1)) . strtoupper(substr($data->apellidos, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    @if ($data->status)
                                        <span class="badge bg-success position-absolute top-0 end-0 mt-2">Activo</span>
                                    @else
                                        <span class="badge bg-secondary position-absolute top-0 end-0 mt-2">Inactivo</span>
                                    @endif
                                </div>
                                
                                <h6 class="mb-1 text-truncate" title="{{ $data->nombres . ' ' . $data->apellidos }}">
                                    {{ $data->nombres . ' ' . $data->apellidos }}
                                </h6>
                                
                                <div class="text-start small mb-3" style="min-height: 100px;">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="ri ri-id-card-line text-muted me-2 small" style="min-width: 20px;"></i>
                                        <span class="text-truncate" title="{{ $data->documento }}">{{ $data->documento }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="ri ri-briefcase-line text-muted me-2 small" style="min-width: 20px;"></i>
                                        <span class="text-truncate" title="{{ $data->nivel_ministerial }}">{{ $data->nivel_ministerial }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="ri ri-map-pin-line text-muted me-2 small" style="min-width: 20px;"></i>
                                        <span class="text-truncate" title="{{ $data->zona }}">{{ $data->zona }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="ri ri-community-line text-muted me-2 small" style="min-width: 20px;"></i>
                                        <span class="text-truncate" title="{{ $data->distrito }}">{{ $data->distrito }}</span>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2 justify-content-center">
                                    @can('edit pastores')
                                    <a href="{{ route('admin.pastores.edit', $data->id) }}"
                                        class="btn btn-sm btn-outline-primary flex-fill" title="Editar">
                                        <i class="ri ri-pencil-line"></i>
                                    </a>
                                    @endcan
                                    @can('show pastores')
                                    <a target="_blank" href="{{ route('admin.pastores.planilla', $data->id) }}"
                                        class="btn btn-sm btn-outline-info flex-fill" title="Ver Planilla">
                                        <i class="ri ri-file-text-line"></i>
                                    </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="ri ri-user-line" style="font-size:3rem;opacity:.3;"></i>
                            <h5 class="mt-3 text-muted">No se encontraron pastores</h5>
                            <p class="mb-0">Intenta cambiar los filtros o crea nuevos pastores</p>
                        </div>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-4">
                {{ $pastores->links('livewire.pagination') }}
            </div>
        @endif
    </div>
</div>