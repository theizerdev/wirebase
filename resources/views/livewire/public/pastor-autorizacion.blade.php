<div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                {{-- Header con logo --}}
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="ri ri-shield-check-line text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="fw-bold mb-2">Autorización de Pastor</h2>
                    <p class="text-muted">Sistema de Gestión Eclesiástica</p>
                </div>

                @if($error)
                    <div class="alert alert-danger">
                        <i class="ri ri-error-warning-line me-2"></i>
                        {{ $error }}
                    </div>
                @endif

                @if($success && !$autorizado)
                    <div class="alert alert-success">
                        <i class="ri ri-checkbox-circle-line me-2"></i>
                        {{ $success }}
                    </div>
                @endif

                {{-- Card principal --}}
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4">
                        
                        @if($autorizado)
                            {{-- Vista después de aprobar --}}
                            <div class="text-center py-4">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 100px; height: 100px;">
                                    <i class="ri ri-check-double-line text-success" style="font-size: 3rem;"></i>
                                </div>
                                <h3 class="fw-bold text-success mb-3">¡Aprobación Exitosa!</h3>
                                <p class="text-muted mb-4">{{ $success }}</p>
                                
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="fw-semibold mb-3">Resumen de la Aprobación:</h6>
                                        <ul class="list-unstyled mb-0 text-start">
                                            <li class="mb-2">
                                                <i class="ri ri-user-line me-2 text-primary"></i>
                                                <strong>Pastor:</strong> {{ $pastor->nombre_completo }}
                                            </li>
                                            <li class="mb-2">
                                                <i class="ri ri-file-text-line me-2 text-primary"></i>
                                                <strong>Solicitud:</strong> Modificar datos personales y eclesiásticos
                                            </li>
                                            <li class="mb-2">
                                                <i class="ri ri-map-pin-line me-2 text-primary"></i>
                                                <strong>Zona:</strong> {{ $pastor->zona }}
                                            </li>
                                            <li>
                                                <i class="ri ri-time-line me-2 text-primary"></i>
                                                <strong>Aprobado el:</strong> {{ now()->format('d/m/Y H:i') }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                                        <i class="ri ri-home-line me-1"></i>
                                        Ir al Dashboard
                                    </a>
                                </div>
                            </div>

                        @else
                            {{-- Vista normal de autorización --}}
                            
                            {{-- Información del Pastor --}}
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-3">
                                        <i class="ri ri-user-star-line me-2 text-primary"></i>
                                        Datos del Pastor
                                    </h5>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="ri ri-user-line text-muted me-2 mt-1"></i>
                                                <div>
                                                    <small class="text-muted d-block">Nombre Completo</small>
                                                    <strong>{{ $pastor->nombre_completo }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="ri ri-id-card-line text-muted me-2 mt-1"></i>
                                                <div>
                                                    <small class="text-muted d-block">Código</small>
                                                    <strong>{{ $pastor->codigo ?? 'N/A' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="ri ri-map-pin-line text-muted me-2 mt-1"></i>
                                                <div>
                                                    <small class="text-muted d-block">Zona</small>
                                                    <strong>{{ $pastor->zona ?? 'N/A' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="ri ri-road-map-line text-muted me-2 mt-1"></i>
                                                <div>
                                                    <small class="text-muted d-block">Distrito</small>
                                                    <strong>{{ $pastor->distrito ?? 'N/A' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Información de la Solicitud --}}
                            <div class="card border-primary border-2 mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="ri ri-phone-line me-2"></i>
                                        Solicitud de Modificación
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <i class="ri ri-information-line me-2"></i>
                                        El pastor ha solicitado modificar o ingresar sus datos personales y eclesiásticos en el sistema.
                                    </div>
                                    
                                    <div class="alert alert-light border mb-3">
                                        <p class="mb-0 small">
                                            <i class="ri ri-information-line me-1 text-primary"></i>
                                            {{ $solicitud->descripcion_solicitud ?? 'El pastor desea modificar o ingresar sus datos personales y eclesiásticos en el sistema.' }}
                                        </p>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="text-muted small">
                                        <i class="ri ri-time-line me-1"></i>
                                        Solicitud creada: {{ $solicitud->created_at->format('d/m/Y H:i') }}
                                        <br>
                                        <i class="ri ri-hourglass-line me-1"></i>
                                        Expira: {{ $solicitud->token_expires_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Botones de Acción --}}
                            @if($accion === 'ver')
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success btn-lg" wire:click="aprobar">
                                        <i class="ri ri-check-line me-2"></i>
                                        Aprobar Solicitud
                                    </button>
                                    <button class="btn btn-outline-danger btn-lg" wire:click="$set('accion', 'rechazar')">
                                        <i class="ri ri-close-line me-2"></i>
                                        Rechazar Solicitud
                                    </button>
                                </div>

                            @elseif($accion === 'rechazar')
                                <form wire:submit.prevent="rechazar">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="ri ri-message-2-line me-1"></i>
                                            Motivo del Rechazo (Opcional)
                                        </label>
                                        <textarea class="form-control @error('motivoRechazo') is-invalid @enderror"
                                                  wire:model="motivoRechazo"
                                                  rows="4"
                                                  placeholder="Explique por qué rechaza esta solicitud..."></textarea>
                                        
                                        @error('motivoRechazo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        
                                        <small class="text-muted mt-1 d-block">
                                            Este motivo será registrado en el sistema.
                                        </small>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="ri ri-send-plane-line me-1"></i>
                                            Confirmar Rechazo
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-secondary"
                                                wire:click="$set('accion', 'ver')">
                                            <i class="ri ri-arrow-go-back-line me-1"></i>
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            @endif

                        @endif
                    </div>
                </div>

                {{-- Footer --}}
                <div class="text-center mt-4 text-muted small">
                    <p class="mb-0">
                        <i class="ri ri-shield-check-line me-1"></i>
                        Sistema seguro de autorización • Token: {{ substr($token, 0, 8) }}...
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>
