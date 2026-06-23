<div>
    @section('title', 'Asistencias y Reportes')

    @push('styles')
    <style>
        .asis-hero { 
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); 
            color: #fff; 
            border-radius: 0.75rem; 
            padding: 1.5rem 1.8rem; 
        }
        .asis-hero h2 { 
            color: #fff; 
            margin: 0; 
        }
        .asis-hero p { 
            opacity: 0.9; 
            margin: 0; 
        }
        .asis-row { 
            transition: background 0.15s; 
        }
        .asis-row:hover { 
            background: #fdfdff; 
        }
        .avatar-initial-bg {
            background-color: rgba(99, 102, 241, 0.1);
            color: #4f46e5;
            font-weight: 700;
        }

        /* Printable styles */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .layout-menu, 
            .layout-navbar, 
            .footer, 
            .btn, 
            .breadcrumb, 
            .asis-hero, 
            .card-body.filters-card-body, 
            .pagination-wrapper, 
            .d-print-none,
            th:last-child,
            td:last-child {
                display: none !important;
            }
            .container-fluid, 
            .content-wrapper, 
            .layout-page, 
            .layout-container, 
            .layout-wrapper, 
            .container-p-y {
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                background: transparent !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
                background: transparent !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .card-header {
                padding-bottom: 10px !important;
            }
            .table-responsive {
                overflow: visible !important;
            }
            .table {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 11px !important;
            }
            .table th, .table td {
                border: 1px solid #000000 !important;
                padding: 6px 8px !important;
                color: #000000 !important;
                background: transparent !important;
            }
            .table thead th {
                background-color: #f1f5f9 !important;
                font-weight: bold !important;
            }
            .badge {
                border: none !important;
                background: transparent !important;
                color: #000000 !important;
                padding: 0 !important;
                font-size: 11px !important;
            }
        }
    </style>
    @endpush

    <div class="container-p-y">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-3 d-print-none">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="ri ri-home-line me-1"></i>Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active">Asistencias</li>
            </ol>
        </nav>

        {{-- Hero Header --}}
        <div class="asis-hero mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3 d-print-none shadow-sm">
            <div>
                <h2 class="fw-semibold"><i class="ri ri-user-follow-line me-2"></i>Asistencias y Reportes</h2>
                <p class="mt-1">Monitoree las asistencias, filtre los registros y genere descargas o impresiones del historial.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" wire:click="exportarReporte" class="btn btn-light text-primary fw-semibold btn-sm">
                    <i class="ri ri-file-excel-line me-1"></i>Descargar Excel (CSV)
                </button>
                <button type="button" onclick="window.print()" class="btn btn-outline-light btn-sm">
                    <i class="ri ri-printer-line me-1"></i>Imprimir Reporte
                </button>
            </div>
        </div>

        {{-- Print Only Header --}}
        <div class="d-none d-print-block mb-4 text-center">
            <h2 class="fw-bold mb-1">REPORTE GENERAL DE ASISTENCIAS</h2>
            <h4 class="text-muted mb-2">CONVENCIÓN NACIONAL</h4>
            <div class="small text-muted">
                <span><strong>Generado el:</strong> {{ now()->format('d/m/Y h:i A') }}</span>
                @if($this->actividadId)
                    <span class="ms-3"><strong>Evento:</strong> {{ \App\Models\Actividad::find($this->actividadId)->nombre }}</span>
                @endif
                @if($this->metodo)
                    <span class="ms-3"><strong>Método:</strong> {{ $this->metodo }}</span>
                @endif
            </div>
            <hr style="border-top: 2px solid #000; margin-top: 15px;">
        </div>

        {{-- Metrics Row --}}
        <div class="row g-4 mb-4 d-print-none">
            <div class="col-sm-6 col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block text-uppercase fw-semibold mb-1">Total Asistencias</span>
                            <h3 class="card-title mb-0 fw-bold text-primary">{{ $totalGeneral }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-label-primary rounded-3">
                            <i class="ri ri-group-line fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block text-uppercase fw-semibold mb-1">Registros QR</span>
                            <h3 class="card-title mb-0 fw-bold text-info">{{ $totalQR }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-label-info rounded-3">
                            <i class="ri ri-qr-code-line fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block text-uppercase fw-semibold mb-1">Registros Manuales</span>
                            <h3 class="card-title mb-0 fw-bold text-warning">{{ $totalManual }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-label-warning rounded-3">
                            <i class="ri ri-keyboard-line fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="card border-0 shadow-sm mb-4 d-print-none">
            <div class="card-body filters-card-body p-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold"><i class="ri ri-search-line me-1"></i>Buscar Pastor</label>
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="Nombre, apellido o cédula...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Filtrar por Actividad</label>
                        <select class="form-select form-select-sm" wire:model.live="actividadId">
                            <option value="">-- Todas las Actividades --</option>
                            @foreach($actividades as $act)
                                <option value="{{ $act->id }}">{{ $act->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Método de Registro</label>
                        <select class="form-select form-select-sm" wire:model.live="metodo">
                            <option value="">-- Todos --</option>
                            <option value="QR">Código QR</option>
                            <option value="Manual">Entrada Manual</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Desde Fecha</label>
                        <input type="date" class="form-control form-control-sm" wire:model.live="fechaInicio">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Hasta Fecha</label>
                        <input type="date" class="form-control form-control-sm" wire:model.live="fechaFin">
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0 d-flex justify-content-between align-items-center d-print-none">
                <h5 class="mb-0 fw-semibold">
                    <i class="ri ri-file-list-line me-2 text-primary"></i>Listado General de Registros
                </h5>
                <div style="width: 150px;">
                    <select class="form-select form-select-sm" wire:model.live="perPage">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Pastor</th>
                                <th>Identificación</th>
                                <th>Actividad</th>
                                <th>Lugar</th>
                                <th class="text-center" wire:click="sortBy('metodo')" style="cursor: pointer;">
                                    Método @if($sortBy === 'metodo') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line small"></i> @endif
                                </th>
                                <th wire:click="sortBy('fecha_hora')" style="cursor: pointer;">
                                    Fecha y Hora @if($sortBy === 'fecha_hora') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line small"></i> @endif
                                </th>
                                <th class="text-end d-print-none">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asistencias as $asis)
                                <tr class="asis-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($asis->pastor && $asis->pastor->foto)
                                                <img src="{{ asset('pastores/' . str_replace(' ', '', $asis->pastor->foto)) }}" alt="Foto" class="rounded-circle me-2 d-print-none" style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="avatar avatar-xs me-2 d-print-none">
                                                    <span class="avatar-initial rounded-circle avatar-initial-bg">
                                                        {{ $asis->pastor ? substr($asis->pastor->nombres, 0, 1) : 'P' }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="fw-semibold text-heading d-block">
                                                    {{ $asis->pastor ? ($asis->pastor->nombres . ' ' . $asis->pastor->apellidos) : 'Pastor no asociado' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-dark">{{ $asis->pastor ? $asis->pastor->documento : 'N/A' }}</code>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-primary">
                                            {{ $asis->actividad ? $asis->actividad->nombre : 'Actividad no asociada' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $asis->actividad ? $asis->actividad->lugar : 'N/A' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-{{ $asis->metodo === 'QR' ? 'info' : 'warning' }} rounded-pill px-2">
                                            {{ $asis->metodo }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $asis->fecha_hora ? $asis->fecha_hora->format('d/m/Y h:i A') : ($asis->created_at ? $asis->created_at->format('d/m/Y h:i A') : 'N/A') }}
                                        </div>
                                        <small class="text-muted d-print-none">
                                            {{ $asis->fecha_hora ? $asis->fecha_hora->diffForHumans() : '' }}
                                        </small>
                                    </td>
                                    <td class="text-end d-print-none">
                                        <button type="button" class="btn btn-sm btn-icon btn-text-danger rounded-pill" 
                                                wire:click="eliminarAsistencia({{ $asis->id }})" 
                                                wire:confirm="¿Está seguro de eliminar esta asistencia?">
                                            <i class="ri ri-delete-bin-line fs-5"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="ri ri-inbox-line d-block mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                        <p class="mb-0">No se encontraron registros de asistencias para los filtros aplicados</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="mt-4 pagination-wrapper d-print-none">
                    {{ $asistencias->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
