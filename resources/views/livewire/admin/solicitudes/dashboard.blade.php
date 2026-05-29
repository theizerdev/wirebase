<div>
    @section('title', 'Dashboard de Solicitudes')

    @push('styles')
    <style>
        .solicitud-hero { background: linear-gradient(135deg, #8B5CF6 0%, #6a07acff 100%); color:#fff; border-radius:.75rem; padding:1.4rem 1.6rem; }
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
                <h2 class="fw-semibold"><i class="ri ri-file-list-3-line me-2"></i>Gestión de Solicitudes</h2>
                <p class="mt-1">Panel administrativo para gestión de solicitudes de modificación de datos</p>
            </div>
            <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-light btn-sm">
                <i class="ri ri-list-check me-1"></i>Ver Todas las Solicitudes
            </a>
        </div>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="ri ri-time-line"></i></div>
                    <div>
                        <div class="stat-label">Pendientes</div>
                        <div class="stat-value">{{ number_format($stats['pendientes'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="ri ri-check-line"></i></div>
                    <div>
                        <div class="stat-label">Aprobadas</div>
                        <div class="stat-value">{{ number_format($stats['aprobadas'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fee2e2;color:#dc2626;"><i class="ri ri-close-line"></i></div>
                    <div>
                        <div class="stat-label">Rechazadas</div>
                        <div class="stat-value">{{ number_format($stats['rechazadas'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dbeafe;color:#2563eb;"><i class="ri ri-speed-line"></i></div>
                    <div>
                        <div class="stat-label">Tiempo Promedio</div>
                        <div class="stat-value" style="font-size:1rem;">{{ $stats['tiempo_promedio_respuesta'] ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla de solicitudes recientes --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="ri ri-history-line me-2 text-primary"></i>Solicitudes Recientes</h6>
                <small class="text-muted">Últimas 10 solicitudes</small>
            </div>
            <div class="card-body">
                @if(count($solicitudesRecientes) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>ID</th>
                                <th>Pastor</th>
                                <th>Cédula</th>
                                <th>Presbítero Asignado</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
                                <th>Tiempo</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudesRecientes as $solicitud)
                                <tr class="solicitud-row">
                                    <td><strong>#{{ $solicitud['id'] }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">{{ substr($solicitud['pastor_nombre'], 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $solicitud['pastor_nombre'] }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $solicitud['pastor_cedula'] }}</span>
                                    </td>
                                    <td>{{ $solicitud['presbitero_nombre'] }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($solicitud['estado']) {
                                                'pendiente' => 'warning',
                                                'aprobado' => 'success',
                                                'rechazado' => 'danger',
                                                'expirado' => 'secondary',
                                                default => 'info'
                                            };
                                            $estadoText = match($solicitud['estado']) {
                                                'pendiente' => 'Pendiente',
                                                'aprobado' => 'Aprobado',
                                                'rechazado' => 'Rechazado',
                                                'expirado' => 'Expirado',
                                                default => ucfirst($solicitud['estado'])
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">{{ $estadoText }}</span>
                                    </td>
                                    <td>{{ $solicitud['fecha_creacion'] }}</td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="ri ri-time-line me-1"></i>
                                            {{ $solicitud['tiempo_transcurrido'] }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.solicitudes.show', $solicitud['id']) }}" 
                                           class="btn btn-sm btn-soft-primary">
                                            <i class="ri ri-eye-line"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-center">
                    <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-link">
                        Ver todas las solicitudes <i class="ri ri-arrow-right-line ms-1"></i>
                    </a>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="ri ri-inbox-line" style="font-size:1.5rem;opacity:.3;"></i>
                    <p class="mb-0 mt-2 small">No hay solicitudes recientes</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
