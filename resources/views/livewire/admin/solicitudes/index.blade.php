<div>
    @section('title', 'Solicitudes')

    @push('styles')
    <style>
        .solicitud-hero { background: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%); color:#fff; border-radius:.75rem; padding:1.4rem 1.6rem; }
        .solicitud-hero h2 { color:#fff; margin:0; }
        .solicitud-hero p { opacity:.9; margin:0; }

        .stat-card { border:1px solid rgba(0,0,0,.06); border-radius:.65rem; padding:.9rem 1rem;
                     transition:all .2s; display:flex; align-items:center; gap:.85rem; height:100%; background:#fff; }
        .stat-card:hover { box-shadow:0 6px 18px rgba(0,0,0,.07); transform:translateY(-1px); }
        .stat-card .stat-icon { width:44px; height:44px; border-radius:11px; flex:0 0 44px;
                                display:flex; align-items:center; justify-content:center; font-size:1.15rem; }
        .stat-card .stat-value { font-size:1.35rem; font-weight:600; line-height:1; }
        .stat-card .stat-label { font-size:.72rem; color:var(--bs-secondary-color);
                                 text-transform:uppercase; letter-spacing:.4px; font-weight:600; }

        .solicitud-row { transition:background .12s; }
        .solicitud-row:hover { background:#f8f9ff; }
    </style>
    @endpush

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Solicitudes</li>
            </ol>
        </nav>

        {{-- Hero + acciones --}}
        <div class="solicitud-hero mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="fw-semibold"><i class="ri ri-file-list-3-line me-2"></i>Solicitudes</h2>
                <p class="mt-1">Gestión de solicitudes de modificación de datos de pastores</p>
            </div>
            <a href="{{ route('admin.solicitudes.dashboard') }}" class="btn btn-light btn-sm">
                <i class="ri ri-dashboard-line me-1"></i>Dashboard
            </a>
        </div>

        {{-- Filtros compactos --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold"><i class="ri ri-search-line me-1"></i>Buscar Pastor</label>
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="Nombre, apellido o cédula...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Estado</label>
                        <select class="form-select form-select-sm" wire:model.live="filterEstado">
                            <option value="">Todos los estados</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="rechazado">Rechazado</option>
                            <option value="expirado">Expirado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Desde</label>
                        <input type="date" class="form-control form-control-sm" wire:model.live="filterFechaInicio">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Hasta</label>
                        <input type="date" class="form-control form-control-sm" wire:model.live="filterFechaFin">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-label-secondary w-100" wire:click="$set('search', ''); $set('filterEstado', ''); $set('filterFechaInicio', ''); $set('filterFechaFin', '')">
                            <i class="ri ri-eraser-line"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="mb-0"><i class="ri ri-file-list-3-line me-2 text-primary"></i>Listado de solicitudes</h6>
            </div>
            <div class="card-body">
                @if($solicitudes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th width="80">ID</th>
                                <th>Pastor</th>
                                <th>Cédula</th>
                                <th>Presbítero</th>
                                <th width="120">Estado</th>
                                <th width="150">Fecha</th>
                                <th width="120" class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudes as $solicitud)
                            <tr class="solicitud-row">
                                <td><strong>#{{ $solicitud->id }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded bg-label-primary">{{ substr($solicitud->pastor->nombres ?? 'N', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $solicitud->pastor->nombre_completo ?? 'N/A' }}</h6>
                                            <small class="text-muted">{{ $solicitud->pastor->zona ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $solicitud->pastor->documento ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $solicitud->presbitero->name ?? 'Sin asignar' }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($solicitud->estado) {
                                            'pendiente' => 'warning',
                                            'aprobado' => 'success',
                                            'rechazado' => 'danger',
                                            'expirado' => 'secondary',
                                            default => 'info'
                                        };
                                        $estadoText = match($solicitud->estado) {
                                            'pendiente' => 'Pendiente',
                                            'aprobado' => 'Aprobado',
                                            'rechazado' => 'Rechazado',
                                            'expirado' => 'Expirado',
                                            default => ucfirst($solicitud->estado)
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ $estadoText }}</span>
                                </td>
                                <td>
                                    <small>{{ $solicitud->created_at->format('d/m/Y H:i') }}</small><br>
                                    <small class="text-muted">{{ $solicitud->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.solicitudes.show', $solicitud->id) }}" 
                                       class="btn btn-sm btn-soft-primary"
                                       title="Ver detalle">
                                        <i class="ri ri-eye-line"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="p-3">
                    {{ $solicitudes->links('vendor.livewire.bootstrap') }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="ri ri-inbox-line fs-1 text-muted"></i>
                    <p class="text-muted mt-3">No se encontraron solicitudes</p>
                    @if($search || $filterEstado)
                    <button class="btn btn-link" 
                            wire:click="$set('search', ''); $set('filterEstado', ''); $set('filterFechaInicio', ''); $set('filterFechaFin', '')">
                        Limpiar filtros
                    </button>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
