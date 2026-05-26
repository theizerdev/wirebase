<div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="ri ri-shield-keyhole-line me-2"></i>
                Configuración de Seguridad
            </h5>
        </div>
        
        <div class="card-body p-4">
            
            @if($error)
                <div class="alert alert-danger">
                    <i class="ri ri-error-warning-line me-2"></i>
                    {{ $error }}
                </div>
            @endif

            @if($success)
                <div class="alert alert-success">
                    <i class="ri ri-checkbox-circle-line me-2"></i>
                    {{ $success }}
                </div>
            @endif

            {{-- Paso 1: Configurar Preguntas de Seguridad --}}
            @if($step === 1)
                <div class="alert alert-info mb-4">
                    <h6 class="alert-heading"><i class="ri ri-information-line me-2"></i>Importante</h6>
                    <p class="mb-0">
                        Configure 3 preguntas de seguridad para proteger su cuenta. 
                        Estas preguntas le permitirán recuperar el acceso si olvida su contraseña.
                    </p>
                </div>

                <form wire:submit.prevent="guardarPreguntas">
                    @for($i = 0; $i < 3; $i++)
                        <div class="card border-0 mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">
                                    <span class="badge bg-primary me-2">{{ $i + 1 }}</span>
                                    Pregunta de Seguridad #{{ $i + 1 }}
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Seleccione una pregunta</label>
                                    <select class="form-select @error("preguntasSeguridad.{$i}.pregunta") is-invalid @enderror"
                                            wire:model="preguntasSeguridad.{{ $i }}.pregunta">
                                        <option value="">-- Seleccione una pregunta --</option>
                                        @foreach($preguntasDisponibles as $pregunta)
                                            <option value="{{ $pregunta }}">{{ $pregunta }}</option>
                                        @endforeach
                                    </select>
                                    
                                    @error("preguntasSeguridad.{$i}.pregunta")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-semibold">Su respuesta</label>
                                    <input type="text" 
                                           class="form-control @error("preguntasSeguridad.{$i}.respuesta") is-invalid @enderror"
                                           wire:model="preguntasSeguridad.{{ $i }}.respuesta"
                                           placeholder="Ingrese su respuesta"
                                           autocomplete="off">
                                    
                                    @error("preguntasSeguridad.{$i}.respuesta")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <small class="text-muted mt-1 d-block">
                                        <i class="ri ri-lock-line me-1"></i>
                                        Las respuestas se guardan encriptadas y no son visibles después.
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endfor

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="ri ri-save-line me-2"></i>
                            Guardar Preguntas de Seguridad
                        </button>
                    </div>
                </form>

            {{-- Paso 2: Backup Codes --}}
            @elseif($step === 2)
                <div class="text-center mb-4">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="ri ri-key-2-line text-warning" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="fw-bold">Códigos de Respaldo</h4>
                    <p class="text-muted">
                        Guarde estos códigos en un lugar seguro. Son su respaldo si no puede responder las preguntas de seguridad.
                    </p>
                </div>

                @if(!$mostrarBackupCodes)
                    <div class="alert alert-warning">
                        <i class="ri ri-alert-line me-2"></i>
                        Los códigos se mostrarán una sola vez. Asegúrese de copiarlos o imprimirlos.
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-warning btn-lg" wire:click="$set('mostrarBackupCodes', true)">
                            <i class="ri ri-eye-line me-2"></i>
                            Mostrar Códigos de Respaldo
                        </button>
                    </div>
                @else
                    <div class="row g-2 mb-4">
                        @foreach($backupCodes as $index => $code)
                            <div class="col-md-6">
                                <div class="card border-2 border-warning">
                                    <div class="card-body py-2 px-3 text-center">
                                        <small class="text-muted d-block">Código {{ $index + 1 }}</small>
                                        <h5 class="fw-bold text-warning mb-0 font-monospace">{{ $code }}</h5>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading"><i class="ri ri-printer-line me-2"></i>Recomendaciones:</h6>
                        <ul class="mb-0 ps-3">
                            <li>Imprima esta página y guarde los códigos en un lugar seguro</li>
                            <li>Cada código solo se puede usar una vez</li>
                            <li>No comparta estos códigos con nadie</li>
                            <li>Puede regenerar nuevos códigos si lo necesita</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-lg" wire:click="activarSeguridad">
                            <i class="ri ri-check-double-line me-2"></i>
                            Activar Configuración de Seguridad
                        </button>
                        <button class="btn btn-outline-warning" wire:click="regenerarBackupCodes">
                            <i class="ri ri-refresh-line me-2"></i>
                            Regenerar Códigos
                        </button>
                    </div>
                @endif

            {{-- Paso 3: Completado --}}
            @elseif($step === 3)
                <div class="text-center py-5">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                         style="width: 120px; height: 120px;">
                        <i class="ri ri-shield-check-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h3 class="fw-bold text-success mb-3">¡Configuración Completada!</h3>
                    <p class="text-muted mb-4 fs-5">
                        Su cuenta ahora está protegida con preguntas de seguridad y códigos de respaldo.
                    </p>

                    <div class="card border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Resumen de Seguridad:</h6>
                            <ul class="list-unstyled mb-0 text-start">
                                <li class="mb-2">
                                    <i class="ri ri-questionnaire-line me-2 text-success"></i>
                                    <strong>3 preguntas de seguridad</strong> configuradas
                                </li>
                                <li class="mb-2">
                                    <i class="ri ri-key-2-line me-2 text-success"></i>
                                    <strong>{{ count($backupCodes) }} códigos de respaldo</strong> generados
                                </li>
                                <li>
                                    <i class="ri ri-shield-check-line me-2 text-success"></i>
                                    <strong>Protección activada</strong> exitosamente
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-success">
                        <i class="ri ri-checkbox-circle-line me-2"></i>
                        Ahora puede modificar sus datos de forma segura en el sistema.
                    </div>

                    <a href="{{ route('public.pastores.editar', $pastor->id) }}" class="btn btn-primary btn-lg mt-3">
                        <i class="ri ri-arrow-left-line me-2"></i>
                        Modificar tus datos
                    </a>
                </div>
            @endif

        </div>
    </div>
</div>
