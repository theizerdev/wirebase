<div>
    {{-- Botón para abrir modal --}}
    <button type="button" 
            class="btn btn-primary btn-sm"
            wire:click="abrirModal">
        <i class="ri ri-phone-line me-1"></i>
        Actualizar Teléfono
    </button>

    {{-- Modal --}}
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="ri ri-phone-line me-2"></i>
                        Solicitar Modificación de Teléfono
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="cerrarModal"></button>
                </div>
                
                <div class="modal-body p-4">
                    @if($solicitudCreada)
                        {{-- Mensaje de éxito --}}
                        <div class="alert alert-success">
                            <div class="d-flex align-items-center">
                                <i class="ri ri-checkbox-circle-fill fs-3 me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">¡Solicitud Enviada!</h6>
                                    <p class="mb-0">{{ $success }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Información del Pastor --}}
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body py-3">
                                <div class="d-flex align-items-center">
                                    @if($pastor->foto)
                                        <img src="{{ asset('storage/' . $pastor->foto) }}" 
                                             alt="{{ $pastor->nombre_completo }}"
                                             class="rounded-circle me-3"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 60px; height: 60px;">
                                            <i class="ri ri-user-fill fs-4"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ $pastor->nombre_completo }}</h6>
                                        <p class="mb-0 text-muted small">
                                            <i class="ri ri-map-pin-line me-1"></i>{{ $pastor->zona ?? 'Sin zona' }}
                                            @if($pastor->distrito)
                                                | <i class="ri ri-road-map-line me-1"></i>{{ $pastor->distrito }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Explicación del proceso --}}
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="ri ri-information-line me-2"></i>¿Cómo funciona?</h6>
                            <ol class="mb-0 ps-3">
                                <li class="mb-2">Ingrese su nuevo número de teléfono</li>
                                <li class="mb-2">El sistema buscará al Presbítero de su zona</li>
                                <li class="mb-2">Se enviará una notificación WhatsApp al Presbítero</li>
                                <li class="mb-2">El Presbítero debe aprobar su solicitud</li>
                                <li>Una vez aprobado, podrá actualizar sus datos</li>
                            </ol>
                        </div>

                        {{-- Formulario --}}
                        <form wire:submit.prevent="enviarSolicitud">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri ri-phone-line me-1"></i>
                                    Nuevo Número de Teléfono <span class="text-danger">*</span>
                                </label>
                                <input type="tel" 
                                       class="form-control @error('telefonoNuevo') is-invalid @enderror"
                                       wire:model="telefonoNuevo"
                                       placeholder="Ej: 04141234567"
                                       autocomplete="off">
                                
                                @error('telefonoNuevo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <small class="text-muted mt-1 d-block">
                                    <i class="ri ri-information-line me-1"></i>
                                    Ingrese el número sin espacios ni guiones
                                </small>
                            </div>

                            @if($error)
                                <div class="alert alert-danger">
                                    <i class="ri ri-error-warning-line me-2"></i>
                                    {{ $error }}
                                </div>
                            @endif

                            <div class="d-grid gap-2">
                                <button type="submit" 
                                        class="btn btn-primary"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        <i class="ri ri-send-plane-line me-1"></i>
                                        Enviar Solicitud
                                    </span>
                                    <span wire:loading>
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        Procesando...
                                    </span>
                                </button>
                                <button type="button" 
                                        class="btn btn-outline-secondary"
                                        wire:click="cerrarModal"
                                        wire:loading.remove>
                                    <i class="ri ri-close-line me-1"></i>
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@script
<script>
    // Escuchar evento para cerrar modal después de delay
    $wire.on('close-modal-after-delay', (event) => {
        setTimeout(() => {
            $wire.cerrarModal();
        }, event.delay || 3000);
    });
</script>
@endscript
