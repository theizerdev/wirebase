<div>
    @section('title', 'Casas de Alimentación')

    @push('styles')
    <style>
        .casa-hero { background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); color:#fff; border-radius:.75rem; padding:1.4rem 1.6rem; }
        .casa-hero h2 { color:#fff; margin:0; }
        .casa-hero p { opacity:.9; margin:0; }

        .stat-card { border:1px solid rgba(0,0,0,.06); border-radius:.65rem; padding:.9rem 1rem;
                     transition:all .2s; display:flex; align-items:center; gap:.85rem; height:100%; background:#fff; }
        .stat-card:hover { box-shadow:0 6px 18px rgba(0,0,0,.07); transform:translateY(-1px); }
        .stat-card .stat-icon { width:44px; height:44px; border-radius:11px; flex:0 0 44px;
                                display:flex; align-items:center; justify-content:center; font-size:1.15rem; }
        .stat-card .stat-value { font-size:1.35rem; font-weight:600; line-height:1; }
        .stat-card .stat-label { font-size:.72rem; color:var(--bs-secondary-color);
                                 text-transform:uppercase; letter-spacing:.4px; font-weight:600; }

        .casa-row { transition:background .12s; }
        .casa-row:hover { background:#f8f9ff; }
    </style>
    @endpush

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Casas de Alimentación</li>
            </ol>
        </nav>

        {{-- Hero + acciones --}}
        <div class="casa-hero mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="fw-semibold"><i class="ri ri-home-heart-line me-2"></i>Casas de Alimentación</h2>
                <p class="mt-1">Gestión de casas de alimentación del sistema</p>
            </div>
            @can('create casas_alimentacion')
                <a href="{{ route('admin.casas_alimentacion.create') }}" class="btn btn-light btn-sm">
                    <i class="ri ri-add-line me-1"></i>Nueva Casa
                </a>
            @endcan
        </div>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dbeafe;color:#2563eb;"><i class="ri ri-home-heart-line"></i></div>
                    <div>
                        <div class="stat-label">Total casas</div>
                        <div class="stat-value">{{ $totalCasas }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="ri ri-check-line"></i></div>
                    <div>
                        <div class="stat-label">Operativas</div>
                        <div class="stat-value">{{ $casasOperativas }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="ri ri-close-line"></i></div>
                    <div>
                        <div class="stat-label">Inoperativas</div>
                        <div class="stat-value">{{ $casasInoperativas }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#e0e7ff;color:#4f46e5;"><i class="ri ri-map-pin-line"></i></div>
                    <div>
                        <div class="stat-label">Zona base</div>
                        <div class="stat-value">{{ $casasZonaBase }}</div>
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
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="Código, sector, consejo, vocero, teléfono...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Estado</label>
                        <select class="form-select form-select-sm" wire:model.live="estadoId">
                            <option value="">Todos</option>
                            @foreach(\App\Models\Estado::orderBy('nombre')->get() as $estado)
                                <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Estado CDA</label>
                        <select class="form-select form-select-sm" wire:model.live="estadoCda">
                            <option value="">Todos</option>
                            <option value="Operativa">Operativas</option>
                            <option value="Inoperativa">Inoperativas</option>
                            <option value="Inactiva">Inactivas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Zona base</label>
                        <select class="form-select form-select-sm" wire:model.live="zonaBaseMisiones">
                            <option value="">Todos</option>
                            <option value="si">Sí</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Mostrar</label>
                        <select class="form-select form-select-sm" wire:model.live="perPage">
                            <option value="10">10 por página</option>
                            <option value="25">25 por página</option>
                            <option value="50">50 por página</option>
                            <option value="100">100 por página</option>
                        </select>
                    </div>
                    <div class="col-md-1">
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
                <h6 class="mb-0"><i class="ri ri-home-heart-line me-2 text-primary"></i>Listado de casas de alimentación</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th wire:click="sortBy('codigo')" style="cursor: pointer;">
                                    Código @if($sortBy === 'codigo') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Ubicación</th>
                                <th>Sector</th>
                                <th wire:click="sortBy('telefono_principal')" style="cursor: pointer;">
                                    Teléfono @if($sortBy === 'telefono_principal') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('estado_cda')" style="cursor: pointer;">
                                    Estado @if($sortBy === 'estado_cda') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('fecha')" style="cursor: pointer;">
                                    Fecha @if($sortBy === 'fecha') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($casas as $casa)
                                <tr class="casa-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">{{ substr($casa->codigo, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $casa->codigo }}</h6>
                                                <small class="text-muted">{{ $casa->vocero_alimentacion ?: 'Sin vocero' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="small text-muted">{{ $casa->estado?->nombre ?: 'Sin estado' }}</div>
                                            <div class="small">{{ $casa->municipio?->nombre ?: '' }} {{ $casa->parroquia?->nombre ?: '' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $casa->sector ?: 'Sin sector' }}</span>
                                    </td>
                                    <td>
                                        @if($casa->telefono_principal)
                                            <a href="tel:{{ $casa->telefono_principal }}" class="text-decoration-none">{{ $casa->telefono_principal }}</a>
                                        @else
                                            <span class="text-muted">Sin teléfono</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $estadoColor = match($casa->estado_cda) {
                                                'Operativa' => 'success',
                                                'Inoperativa' => 'danger',
                                                'Inactiva' => 'warning',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $estadoColor }}-subtle text-{{ $estadoColor }}">{{ $casa->estado_cda }}</span>
                                        @if($casa->zona_base_misiones)
                                            <span class="badge bg-info-subtle text-info ms-1">Base</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($casa->fecha)
                                            {{ $casa->fecha->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">Sin fecha</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ri ri-more-2-line"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('admin.casas_alimentacion.show', $casa) }}">
                                                    <i class="ri ri-eye-line me-1"></i> Ver detalles
                                                </a>
                                                @can('edit casas_alimentacion')
                                                <a class="dropdown-item" href="{{ route('admin.casas_alimentacion.edit', $casa) }}">
                                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                                </a>
                                                @endcan
                                                @can('delete casas_alimentacion')
                                                <button type="button" class="dropdown-item text-danger"
                                                        wire:click="deleteCasa({{ $casa->id }})"
                                                        wire:confirm="¿Estás seguro de eliminar esta casa de alimentación?">
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
                                        <i class="ri ri-home-heart-line" style="font-size:1.5rem;opacity:.3;"></i>
                                        <p class="mb-0 mt-1 small">No se encontraron casas de alimentación</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $casas->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
