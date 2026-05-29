<div>
    @section('title', 'Solicitud #' . $solicitud->id)

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.solicitudes.index') }}">Solicitudes</a></li>
                <li class="breadcrumb-item active">#{{ $solicitud->id }}</li>
            </ol>
        </nav>

        {{-- Flash message --}}
        @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri ri-check-line me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row g-4">
            {{-- Información del Pastor --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom bg-transparent">
                        <h5 class="card-title mb-1"><i class="ri ri-user-line me-2 text-primary"></i>Datos del Pastor</h5>
                        <p class="mb-0 text-muted small">Información personal y eclesiástica</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Nombre Completo:</label>
                                <p class="mb-0">{{ $solicitud->pastor->nombre_completo ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Cédula:</label>
                                <p class="mb-0"><span class="badge bg-light text-dark">{{ $solicitud->pastor->documento ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Teléfono:</label>
                                <p class="mb-0"><span class="badge bg-light text-dark">{{ $solicitud->pastor->telefono_tlf ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Zona/Distrito:</label>
                                <p class="mb-0">{{ $solicitud->pastor->zona ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Grado Ministerial:</label>
                                <p class="mb-0">{{ $solicitud->pastor->nivel_ministerial ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Estado Civil:</label>
                                <p class="mb-0">{{ $solicitud->pastor->estado_civil ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Timeline de Auditoría --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom bg-transparent">
                        <h5 class="card-title mb-1"><i class="ri ri-history-line me-2 text-primary"></i>Historial de Acciones</h5>
                        <p class="mb-0 text-muted small">Registro completo de actividades</p>
                    </div>
                    <div class="card-body">
                        @if(count($auditoria) > 0)
                        <div class="timeline">
                            @foreach($auditoria as $item)
                            <div class="timeline-item pb-3">
                                <div class="d-flex">
                                    <div class="timeline-indicator avatar avatar-xs rounded-circle bg-{{ 
                                        $item->accion === 'creada' ? 'info' : 
                                        ($item->accion === 'aprobada' ? 'success' : 
                                        ($item->accion === 'rechazada' ? 'danger' : 'secondary'))
                                    }}">
                                        <i class="ri ri-{{ 
                                            $item->accion === 'creada' ? 'add-line' : 
                                            ($item->accion === 'vista' ? 'eye-line' :
                                            ($item->accion === 'aprobada' ? 'check-line' : 
                                            ($item->accion === 'rechazada' ? 'close-line' : 'time-line')))
                                        }}"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ ucfirst($item->accion) }}</h6>
                                                @if($item->usuario_nombre)
                                                <p class="text-muted small mb-1">
                                                    Por: <strong>{{ $item->usuario_nombre }}</strong>
                                                    @if($item->usuario_rol)
                                                    <span class="badge bg-label-secondary ms-1">{{ $item->usuario_rol }}</span>
                                                    @endif
                                                </p>
                                                @endif
                                                @if($item->metadata && is_array($item->metadata))
                                                    @if(isset($item->metadata['comentario']))
                                                    <div class="alert alert-light border mt-2 mb-1 p-2 small">
                                                        <i class="ri ri-chat-1-line me-1"></i>{{ $item->metadata['comentario'] }}
                                                    </div>
                                                    @endif
                                                    @if(isset($item->metadata['motivo']))
                                                    <div class="alert alert-danger border mt-2 mb-1 p-2 small">
                                                        <i class="ri ri-error-warning-line me-1"></i><strong>Motivo:</strong> {{ $item->metadata['motivo'] }}
                                                    </div>
                                                    @endif
                                                @endif
                                                @if($item->ip_address)
                                                <p class="text-muted small mb-0">
                                                    <i class="ri ri-map-pin-line me-1"></i>IP: {{ $item->ip_address }}
                                                </p>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="ri ri-inbox-line fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No hay registros de auditoría</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Panel lateral con acciones --}}
            <div class="col-lg-4">
                {{-- Estado de la Solicitud --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom bg-transparent">
                        <h5 class="card-title mb-1"><i class="ri ri-information-line me-2 text-primary"></i>Estado</h5>
                    </div>
                    <div class="card-body text-center">
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
                        <div class="avatar avatar-lg mb-3">
                            <span class="avatar-initial rounded-circle bg-label-{{ $badgeClass }}">
                                <i class="ri ri-{{ $solicitud->estado === 'aprobado' ? 'check-line' : ($solicitud->estado === 'rechazado' ? 'close-line' : 'time-line') }} fs-2"></i>
                            </span>
                        </div>
                        <h4 class="mb-1"><span class="badge bg-{{ $badgeClass }}">{{ $estadoText }}</span></h4>
                        <p class="text-muted small mb-0">Creada: {{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-muted small">{{ $solicitud->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                {{-- Presbítero Asignado --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom bg-transparent">
                        <h5 class="card-title mb-1"><i class="ri ri-shield-user-line me-2 text-primary"></i>Presbítero</h5>
                    </div>
                    <div class="card-body">
                        @if($solicitud->presbitero)
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($solicitud->presbitero->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $solicitud->presbitero->name }}</h6>
                                <small class="text-muted">{{ $solicitud->presbitero->email }}</small>
                            </div>
                        </div>
                        @else
                        <p class="text-muted mb-0">Sin presbítero asignado</p>
                        @endif
                    </div>
                </div>

                {{-- Acciones --}}
                @if($solicitud->estado === 'pendiente')
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom bg-transparent">
                        <h5 class="card-title mb-1"><i class="ri ri-settings-3-line me-2 text-primary"></i>Acciones</h5>
                    </div>
                    <div class="card-body">
                        {{-- Botón Aprobar --}}
                        <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#aprobarModal">
                            <i class="ri ri-check-line me-1"></i>Aprobar Solicitud
                        </button>
                        
                        {{-- Botón Rechazar --}}
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rechazarModal">
                            <i class="ri ri-close-line me-1"></i>Rechazar Solicitud
                        </button>
                    </div>
                </div>
                @endif

                {{-- Volver --}}
                <div class="mt-3">
                    <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-label-secondary w-100">
                        <i class="ri ri-arrow-left-line me-1"></i>Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Aprobar --}}
    <div class="modal fade" id="aprobarModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri ri-check-line me-2 text-success"></i>Aprobar Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de aprobar esta solicitud?</p>
                    <div class="mb-3">
                        <label class="form-label">Comentario (opcional)</label>
                        <textarea class="form-control" wire:model="comentario" rows="3" placeholder="Agregar un comentario..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" wire:click="aprobar" data-bs-dismiss="modal">
                        <i class="ri ri-check-line me-1"></i>Confirmar Aprobación
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Rechazar --}}
    <div class="modal fade" id="rechazarModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri ri-close-line me-2 text-danger"></i>Rechazar Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ri ri-error-warning-line me-2"></i>
                        <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo del rechazo <span class="text-danger">*</span></label>
                        <textarea class="form-control" wire:model="motivoRechazo" rows="4" placeholder="Explique el motivo del rechazo..." required></textarea>
                        @error('motivoRechazo')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" wire:click="rechazar" data-bs-dismiss="modal">
                        <i class="ri ri-close-line me-1"></i>Confirmar Rechazo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
