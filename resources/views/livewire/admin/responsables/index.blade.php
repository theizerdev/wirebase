<div>
    @section('title', 'Responsables')

    @push('styles')
    <style>
        .responsable-hero { background: linear-gradient(135deg, #10B981 0%, #3B82F6 100%); color:#fff; border-radius:.75rem; padding:1.4rem 1.6rem; }
        .responsable-hero h2 { color:#fff; margin:0; }
        .responsable-hero p { opacity:.9; margin:0; }

        .stat-card { border:1px solid rgba(0,0,0,.06); border-radius:.65rem; padding:.9rem 1rem;
                     transition:all .2s; display:flex; align-items:center; gap:.85rem; height:100%; background:#fff; }
        .stat-card:hover { box-shadow:0 6px 18px rgba(0,0,0,.07); transform:translateY(-1px); }
        .stat-card .stat-icon { width:44px; height:44px; border-radius:11px; flex:0 0 44px;
                                display:flex; align-items:center; justify-content:center; font-size:1.15rem; }
        .stat-card .stat-value { font-size:1.35rem; font-weight:600; line-height:1; }
        .stat-card .stat-label { font-size:.72rem; color:var(--bs-secondary-color);
                                 text-transform:uppercase; letter-spacing:.4px; font-weight:600; }

        .responsable-row { transition:background .12s; }
        .responsable-row:hover { background:#f8f9ff; }
    </style>
    @endpush

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Responsables</li>
            </ol>
        </nav>

        {{-- Hero + acciones --}}
        <div class="responsable-hero mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="fw-semibold"><i class="ri ri-user-line me-2"></i>Responsables</h2>
                <p class="mt-1">Gestión de responsables del sistema</p>
            </div>
            @can('create responsables')
                <a href="{{ route('admin.responsables.create') }}" class="btn btn-light btn-sm">
                    <i class="ri ri-add-line me-1"></i>Nuevo Responsable
                </a>
            @endcan
        </div>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dbeafe;color:#2563eb;"><i class="ri ri-user-line"></i></div>
                    <div>
                        <div class="stat-label">Total responsables</div>
                        <div class="stat-value">{{ $responsables->total() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="ri ri-map-pin-line"></i></div>
                    <div>
                        <div class="stat-label">Con ubicación</div>
                        <div class="stat-value">{{ $responsables->where('estado_id', '!=', null)->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="ri ri-building-line"></i></div>
                    <div>
                        <div class="stat-label">Con empresa</div>
                        <div class="stat-value">{{ $responsables->where('empresa_id', '!=', null)->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros compactos --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold"><i class="ri ri-search-line me-1"></i>Buscar</label>
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="Nombre, cédula...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Mostrar</label>
                        <select class="form-select form-select-sm" wire:model.live="perPage">
                            <option value="10">10 por página</option>
                            <option value="25">25 por página</option>
                            <option value="50">50 por página</option>
                            <option value="100">100 por página</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-label-secondary" wire:click="clearFilters">
                            <i class="ri ri-eraser-line"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="mb-0"><i class="ri ri-user-line me-2 text-primary"></i>Listado de responsables</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Nombre</th>
                                <th>Cédula</th>
                                <th>Estado</th>
                                <th>Municipio</th>
                                <th>Parroquia</th>
                                <th>Empresa</th>
                                <th>Sucursal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($responsables as $responsable)
                                <tr class="responsable-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">{{ substr($responsable->nombre_completo, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $responsable->nombre_completo }}</h6>
                                                <small class="text-muted">{{ $responsable->telefono ? $responsable->telefono : 'Sin teléfono' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $responsable->cedula }}</span>
                                    </td>
                                    <td>
                                        {{ $responsable->estado ? $responsable->estado->nombre : 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $responsable->municipio ? $responsable->municipio->nombre : 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $responsable->parroquia ? $responsable->parroquia->nombre : 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $responsable->empresa ? $responsable->empresa->razon_social : 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $responsable->sucursal ? $responsable->sucursal->nombre : 'N/A' }}
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ri ri-more-2-line"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @can('view responsables')
                                                <a class="dropdown-item" href="{{ route('admin.responsables.show', $responsable) }}">
                                                    <i class="ri ri-eye-line me-1"></i> Ver
                                                </a>
                                                @endcan
                                                @can('edit responsables')
                                                <a class="dropdown-item" href="{{ route('admin.responsables.edit', $responsable) }}">
                                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                                </a>
                                                @endcan
                                                @can('delete responsables')
                                                <button type="button" class="dropdown-item text-danger"
                                                        wire:click="delete({{ $responsable->id }})"
                                                        wire:confirm="¿Estás seguro de eliminar este responsable?">
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
                                        <p class="mb-0 mt-1 small">No se encontraron responsables</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $responsables->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
