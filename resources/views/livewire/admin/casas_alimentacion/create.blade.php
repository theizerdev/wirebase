<div>
    @section('title', 'Crear Casa de Alimentación')

    @push('styles')
    <style>
        .wizard-step { display: none; }
        .wizard-step.active { display: block; }
        .step-indicator { display: flex; justify-content: center; margin-bottom: 2rem; }
        .step-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            background: #e9ecef;
            color: #6c757d;
            font-weight: 600;
            margin: 0 0.5rem;
            transition: all 0.3s;
        }
        .step-item.active {
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            color: white;
        }
        .step-item.completed {
            background: #10b981;
            color: white;
        }
        .step-number {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
            font-size: 0.85rem;
        }
    </style>
    @endpush

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.casas_alimentacion.index') }}">Casas de Alimentación</a></li>
                <li class="breadcrumb-item active">Crear</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="mb-0"><i class="ri ri-home-heart-line me-2 text-primary"></i>Crear nueva Casa de Alimentación</h5>
                    </div>
                    <div class="card-body">
                        <!-- Step Indicator -->
                        <div class="step-indicator">
                            <div class="step-item {{ $currentStep >= 1 ? 'active' : '' }} {{ $currentStep > 1 ? 'completed' : '' }}">
                                <span class="step-number">1</span>
                                Información General
                            </div>
                            <div class="step-item {{ $currentStep >= 2 ? 'active' : '' }} {{ $currentStep > 2 ? 'completed' : '' }}">
                                <span class="step-number">2</span>
                                Operatividad
                            </div>
                        </div>

                        <form wire:submit.prevent="save">
                            <!-- STEP 1: Información General -->
                            <div class="wizard-step {{ $currentStep === 1 ? 'active' : '' }}">
                                <h6 class="fw-semibold mb-3 text-primary"><i class="ri ri-information-line me-2"></i>Paso 1: Información General</h6>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Código identificador</label>
                                            <input type="text" class="form-control form-control-sm @error('codigo') is-invalid @enderror"
                                                   wire:model="codigo" placeholder="Ej: CDA-001" required>
                                            @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <small class="text-muted d-block mt-1">Código único para identificar la casa de alimentación.</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Fecha de registro</label>
                                            <input type="date" class="form-control form-control-sm @error('fecha') is-invalid @enderror"
                                                   wire:model="fecha">
                                            @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="border-top pt-3 mb-3">
                                        <h6 class="fw-semibold mb-3"><i class="ri ri-map-pin-line me-2"></i>Ubicación geográfica</h6>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Estado</label>
                                                <select class="form-select form-select-sm @error('estado_id') is-invalid @enderror"
                                                        wire:model="estado_id">
                                                    <option value="">-- Seleccione --</option>
                                                    @foreach($estados as $estado)
                                                        <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                                    @endforeach
                                                </select>
                                                @error('estado_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Municipio</label>
                                                <select class="form-select form-select-sm @error('municipio_id') is-invalid @enderror"
                                                        wire:model="municipio_id" {{ !$municipios->count() ? 'disabled' : '' }}>
                                                    <option value="">-- Seleccione --</option>
                                                    @foreach($municipios as $municipio)
                                                        <option value="{{ $municipio->id }}">{{ $municipio->nombre }}</option>
                                                    @endforeach
                                                </select>
                                                @error('municipio_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Parroquia</label>
                                                <select class="form-select form-select-sm @error('parroquia_id') is-invalid @enderror"
                                                        wire:model="parroquia_id" {{ !$parroquias->count() ? 'disabled' : '' }}>
                                                    <option value="">-- Seleccione --</option>
                                                    @foreach($parroquias as $parroquia)
                                                        <option value="{{ $parroquia->id }}">{{ $parroquia->nombre }}</option>
                                                    @endforeach
                                                </select>
                                                @error('parroquia_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Sector / Comunidad</label>
                                                <input type="text" class="form-control form-control-sm @error('sector') is-invalid @enderror"
                                                       wire:model="sector" placeholder="Ej: Sector Los Alamos">
                                                @error('sector') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Consejo Comunal</label>
                                                <input type="text" class="form-control form-control-sm @error('consejo_comunal') is-invalid @enderror"
                                                       wire:model="consejo_comunal" placeholder="Ej: Consejo Comunal La Esperanza">
                                                @error('consejo_comunal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Calle / Avenida</label>
                                                <input type="text" class="form-control form-control-sm @error('calle_avenida') is-invalid @enderror"
                                                       wire:model="calle_avenida" placeholder="Ej: Calle Bolívar">
                                                @error('calle_avenida') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Número vivienda</label>
                                                <input type="text" class="form-control form-control-sm @error('numero_vivienda') is-invalid @enderror"
                                                       wire:model="numero_vivienda" placeholder="Ej: Casa #15">
                                                @error('numero_vivienda') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Punto de referencia</label>
                                                <input type="text" class="form-control form-control-sm @error('punto_referencia') is-invalid @enderror"
                                                       wire:model="punto_referencia" placeholder="Ej: Frente a la iglesia">
                                                @error('punto_referencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top pt-3 mb-3">
                                        <h6 class="fw-semibold mb-3"><i class="ri ri-contacts-line me-2"></i>Contacto y comunidad</h6>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Vocero de Alimentación</label>
                                                <input type="text" class="form-control form-control-sm @error('vocero_alimentacion') is-invalid @enderror"
                                                       wire:model="vocero_alimentacion" placeholder="Nombre completo del vocero">
                                                @error('vocero_alimentacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Estado CDA</label>
                                                <select class="form-select form-select-sm @error('estado_cda') is-invalid @enderror"
                                                        wire:model="estado_cda" required>
                                                    <option value="Operativa">Operativa</option>
                                                    <option value="Inoperativa">Inoperativa</option>
                                                    <option value="Inactiva">Inactiva</option>
                                                </select>
                                                @error('estado_cda') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Teléfono principal</label>
                                                <input type="text" class="form-control form-control-sm @error('telefono_principal') is-invalid @enderror"
                                                       wire:model="telefono_principal" placeholder="Ej: 0412-1234567">
                                                @error('telefono_principal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Teléfono secundario</label>
                                                <input type="text" class="form-control form-control-sm @error('telefono_secundario') is-invalid @enderror"
                                                       wire:model="telefono_secundario" placeholder="Opcional">
                                                @error('telefono_secundario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" wire:model="zona_base_misiones">
                                                    <span class="form-check-label fw-semibold small">Zona base de misiones</span>
                                                </label>
                                                <small class="text-muted d-block mt-1">Marcar si se encuentra en zona base de misiones.</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Distancia a base (km)</label>
                                                <input type="number" step="0.01" class="form-control form-control-sm @error('distancia_a_base_misiones') is-invalid @enderror"
                                                       wire:model="distancia_a_base_misiones" placeholder="Ej: 2.5">
                                                @error('distancia_a_base_misiones') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold small">Motivo de inoperatividad (solo si aplica)</label>
                                                <textarea class="form-control form-control-sm @error('motivo_inoperatividad') is-invalid @enderror"
                                                          wire:model="motivo_inoperatividad" rows="2"
                                                          placeholder="Describa el motivo si está inoperativa o inactiva"></textarea>
                                                @error('motivo_inoperatividad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Código identificador</label>
                                    <input type="text" class="form-control form-control-sm @error('codigo') is-invalid @enderror"
                                           wire:model="codigo" placeholder="Ej: CDA-001" required>
                                    @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted d-block mt-1">Código único para identificar la casa de alimentación.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Fecha de registro</label>
                                    <input type="date" class="form-control form-control-sm @error('fecha') is-invalid @enderror"
                                           wire:model="fecha">
                                    @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="border-top pt-3 mb-3">
                                <h6 class="fw-semibold mb-3"><i class="ri ri-map-pin-line me-2"></i>Ubicación geográfica</h6>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Estado</label>
                                        <select class="form-select form-select-sm @error('estado_id') is-invalid @enderror"
                                                wire:model="estado_id">
                                            <option value="">-- Seleccione --</option>
                                            @foreach($estados as $estado)
                                                <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @error('estado_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Municipio</label>
                                        <select class="form-select form-select-sm @error('municipio_id') is-invalid @enderror"
                                                wire:model="municipio_id" {{ !$municipios->count() ? 'disabled' : '' }}>
                                            <option value="">-- Seleccione --</option>
                                            @foreach($municipios as $municipio)
                                                <option value="{{ $municipio->id }}">{{ $municipio->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @error('municipio_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Parroquia</label>
                                        <select class="form-select form-select-sm @error('parroquia_id') is-invalid @enderror"
                                                wire:model="parroquia_id" {{ !$parroquias->count() ? 'disabled' : '' }}>
                                            <option value="">-- Seleccione --</option>
                                            @foreach($parroquias as $parroquia)
                                                <option value="{{ $parroquia->id }}">{{ $parroquia->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @error('parroquia_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Sector / Comunidad</label>
                                        <input type="text" class="form-control form-control-sm @error('sector') is-invalid @enderror"
                                               wire:model="sector" placeholder="Ej: Sector Los Alamos">
                                        @error('sector') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Consejo Comunal</label>
                                        <input type="text" class="form-control form-control-sm @error('consejo_comunal') is-invalid @enderror"
                                               wire:model="consejo_comunal" placeholder="Ej: Consejo Comunal La Esperanza">
                                        @error('consejo_comunal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Calle / Avenida</label>
                                        <input type="text" class="form-control form-control-sm @error('calle_avenida') is-invalid @enderror"
                                               wire:model="calle_avenida" placeholder="Ej: Calle Bolívar">
                                        @error('calle_avenida') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Número vivienda</label>
                                        <input type="text" class="form-control form-control-sm @error('numero_vivienda') is-invalid @enderror"
                                               wire:model="numero_vivienda" placeholder="Ej: Casa #15">
                                        @error('numero_vivienda') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Punto de referencia</label>
                                        <input type="text" class="form-control form-control-sm @error('punto_referencia') is-invalid @enderror"
                                               wire:model="punto_referencia" placeholder="Ej: Frente a la iglesia">
                                        @error('punto_referencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="border-top pt-3 mb-3">
                                <h6 class="fw-semibold mb-3"><i class="ri ri-contacts-line me-2"></i>Contacto y comunidad</h6>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Vocero de Alimentación</label>
                                        <input type="text" class="form-control form-control-sm @error('vocero_alimentacion') is-invalid @enderror"
                                               wire:model="vocero_alimentacion" placeholder="Nombre completo del vocero">
                                        @error('vocero_alimentacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Estado CDA</label>
                                        <select class="form-select form-select-sm @error('estado_cda') is-invalid @enderror"
                                                wire:model="estado_cda" required>
                                            <option value="Operativa">Operativa</option>
                                            <option value="Inoperativa">Inoperativa</option>
                                            <option value="Inactiva">Inactiva</option>
                                        </select>
                                        @error('estado_cda') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Teléfono principal</label>
                                        <input type="text" class="form-control form-control-sm @error('telefono_principal') is-invalid @enderror"
                                               wire:model="telefono_principal" placeholder="Ej: 0412-1234567">
                                        @error('telefono_principal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Teléfono secundario</label>
                                        <input type="text" class="form-control form-control-sm @error('telefono_secundario') is-invalid @enderror"
                                               wire:model="telefono_secundario" placeholder="Opcional">
                                        @error('telefono_secundario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" wire:model="zona_base_misiones">
                                            <span class="form-check-label fw-semibold small">Zona base de misiones</span>
                                        </label>
                                        <small class="text-muted d-block mt-1">Marcar si se encuentra en zona base de misiones.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Distancia a base (km)</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm @error('distancia_a_base_misiones') is-invalid @enderror"
                                               wire:model="distancia_a_base_misiones" placeholder="Ej: 2.5">
                                        @error('distancia_a_base_misiones') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Motivo de inoperatividad (solo si aplica)</label>
                                        <textarea class="form-control form-control-sm @error('motivo_inoperatividad') is-invalid @enderror"
                                                  wire:model="motivo_inoperatividad" rows="2"
                                                  placeholder="Describa el motivo si está inoperativa o inactiva"></textarea>
                                        @error('motivo_inoperatividad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                                <!-- Navigation Buttons for Step 1 -->
                                <div class="border-top pt-3 mt-4">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.casas_alimentacion.index') }}" class="btn btn-sm btn-label-secondary">
                                            <i class="ri ri-arrow-left-line me-1"></i> Cancelar
                                        </a>
                                        <button type="button" wire:click="nextStep" class="btn btn-sm btn-primary">
                                            Siguiente <i class="ri ri-arrow-right-line ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 2: Operatividad -->
                            <div class="wizard-step {{ $currentStep === 2 ? 'active' : '' }}">
                                <h6 class="fw-semibold mb-3 text-success"><i class="ri ri-settings-3-line me-2"></i>Paso 2: Operatividad de la Casa</h6>

                                <!-- Sección: Sello e Identificación -->
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="fw-semibold mb-3 small text-uppercase text-muted">Sello e Identificación</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="posee_sello">
                                                <span class="form-check-label fw-semibold small">¿Posee sello?</span>
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="posee_listado_beneficiarios">
                                                <span class="form-check-label fw-semibold small">¿Listado actualizado de beneficiarios?</span>
                                            </label>
                                        </div>
                                        @if($posee_listado_beneficiarios)
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Nº Beneficiarios Registrados</label>
                                            <input type="number" class="form-control form-control-sm @error('num_beneficiarios_registrados') is-invalid @enderror"
                                                   wire:model="num_beneficiarios_registrados" min="0">
                                            @error('num_beneficiarios_registrados') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Nº Promedio Diario de Beneficiarios</label>
                                            <input type="number" class="form-control form-control-sm @error('num_promedio_diario_beneficiarios') is-invalid @enderror"
                                                   wire:model="num_promedio_diario_beneficiarios" min="0">
                                            @error('num_promedio_diario_beneficiarios') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        @endif
                                        <div class="col-md-6">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="posee_identificacion_fundaproal">
                                                <span class="form-check-label fw-semibold small">¿Identificación de Fundaproal?</span>
                                            </label>
                                        </div>
                                        @if($posee_identificacion_fundaproal)
                                        <div class="col-md-6">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="identificacion_visible">
                                                <span class="form-check-label fw-semibold small">¿Está en lugar visible?</span>
                                            </label>
                                        </div>
                                        @endif
                                        <div class="col-md-6">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="posee_cartelera_informativa">
                                                <span class="form-check-label fw-semibold small">¿Posee Cartelera Informativa?</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Certificados y Equipamiento -->
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="fw-semibold mb-3 small text-uppercase text-muted">Certificados y Equipamiento Personal</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="posee_certificado_manipulacion_alimentos">
                                                <span class="form-check-label fw-semibold small">Certificado manipulación alimentos</span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="posee_certificado_salud">
                                                <span class="form-check-label fw-semibold small">Certificado de salud</span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="posee_gorros_delantales">
                                                <span class="form-check-label fw-semibold small">Gorros y delantales</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Suministro de Gas -->
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="fw-semibold mb-3 small text-uppercase text-muted">Suministro de Gas</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="recibe_suministro_gas">
                                                <span class="form-check-label fw-semibold small">¿Recibe suministro de gas?</span>
                                            </label>
                                        </div>
                                        @if($recibe_suministro_gas)
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Cantidad de reguladores (kgs)</label>
                                            <input type="number" class="form-control form-control-sm @error('cantidad_reguladores_kg') is-invalid @enderror"
                                                   wire:model="cantidad_reguladores_kg" min="0">
                                            @error('cantidad_reguladores_kg') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Bombonas propias (cantidad)</label>
                                            <input type="number" class="form-control form-control-sm @error('bombonas_propias_cantidad') is-invalid @enderror"
                                                   wire:model="bombonas_propias_cantidad" min="0">
                                            @error('bombonas_propias_cantidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Bombonas prestadas (cantidad)</label>
                                            <input type="number" class="form-control form-control-sm @error('bombonas_prestadas_cantidad') is-invalid @enderror"
                                                   wire:model="bombonas_prestadas_cantidad" min="0">
                                            @error('bombonas_prestadas_cantidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Sección: Manipulación de Alimentos -->
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="fw-semibold mb-3 small text-uppercase text-muted">Manipulación de Alimentos</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="manipulacion_alimentos_adecuada">
                                                <span class="form-check-label fw-semibold small">¿Manipulación adecuada?</span>
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Días por semana que preparan alimentos</label>
                                            <input type="number" class="form-control form-control-sm @error('dias_preparacion_semana') is-invalid @enderror"
                                                   wire:model="dias_preparacion_semana" min="0" max="7">
                                            @error('dias_preparacion_semana') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Personas Itinerantes -->
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="fw-semibold mb-3 small text-uppercase text-muted">Personas Itinerantes</h6>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Femeninos</label>
                                            <input type="number" class="form-control form-control-sm @error('itinerantes_femeninos') is-invalid @enderror"
                                                   wire:model="itinerantes_femeninos" min="0">
                                            @error('itinerantes_femeninos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Masculinos</label>
                                            <input type="number" class="form-control form-control-sm @error('itinerantes_masculinos') is-invalid @enderror"
                                                   wire:model="itinerantes_masculinos" min="0">
                                            @error('itinerantes_masculinos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Menores masc.</label>
                                            <input type="number" class="form-control form-control-sm @error('itinerantes_menores_masculinos') is-invalid @enderror"
                                                   wire:model="itinerantes_menores_masculinos" min="0">
                                            @error('itinerantes_menores_masculinos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Menores fem.</label>
                                            <input type="number" class="form-control form-control-sm @error('itinerantes_menores_femeninos') is-invalid @enderror"
                                                   wire:model="itinerantes_menores_femeninos" min="0">
                                            @error('itinerantes_menores_femeninos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Cocina -->
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="fw-semibold mb-3 small text-uppercase text-muted">Cocina</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Condición</label>
                                            <select class="form-select form-select-sm @error('condicion_cocina') is-invalid @enderror" wire:model="condicion_cocina">
                                                <option value="">-- Seleccione --</option>
                                                <option value="Operativa">Operativa</option>
                                                <option value="Inoperativa">Inoperativa</option>
                                                <option value="No posee">No posee</option>
                                            </select>
                                            @error('condicion_cocina') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Tipo</label>
                                            <select class="form-select form-select-sm @error('tipo_cocina') is-invalid @enderror" wire:model="tipo_cocina">
                                                <option value="">-- Seleccione --</option>
                                                <option value="Industrial">Industrial</option>
                                                <option value="Domestica">Doméstica</option>
                                                <option value="Fogon/Reverbero">Fogón/Reverbero</option>
                                            </select>
                                            @error('tipo_cocina') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Dominio</label>
                                            <select class="form-select form-select-sm @error('dominio_cocina') is-invalid @enderror" wire:model="dominio_cocina">
                                                <option value="">-- Seleccione --</option>
                                                <option value="Propia">Propia</option>
                                                <option value="Fundaproal">Fundaproal</option>
                                                <option value="Prestada">Prestada</option>
                                            </select>
                                            @error('dominio_cocina') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Nevera y Congelador -->
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="fw-semibold mb-3 small text-uppercase text-muted">Nevera y Congelador</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Condición Nevera</label>
                                            <select class="form-select form-select-sm @error('condicion_nevera') is-invalid @enderror" wire:model="condicion_nevera">
                                                <option value="">-- Seleccione --</option>
                                                <option value="Operativa">Operativa</option>
                                                <option value="Inoperativa">Inoperativa</option>
                                                <option value="No posee">No posee</option>
                                            </select>
                                            @error('condicion_nevera') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Dominio Congelador</label>
                                            <select class="form-select form-select-sm @error('dominio_congelador') is-invalid @enderror" wire:model="dominio_congelador">
                                                <option value="">-- Seleccione --</option>
                                                <option value="Propia">Propia</option>
                                                <option value="Fundaproal">Fundaproal</option>
                                                <option value="Prestada">Prestada</option>
                                            </select>
                                            @error('dominio_congelador') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Condición Congelador</label>
                                            <select class="form-select form-select-sm @error('condicion_congelador') is-invalid @enderror" wire:model="condicion_congelador">
                                                <option value="">-- Seleccione --</option>
                                                <option value="Operativa">Operativa</option>
                                                <option value="Inoperativa">Inoperativa</option>
                                                <option value="No posee">No posee</option>
                                            </select>
                                            @error('condicion_congelador') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Utensilios y Mobiliario -->
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="fw-semibold mb-3 small text-uppercase text-muted">Utensilios y Mobiliario</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Estatus utensilios</label>
                                            <select class="form-select form-select-sm @error('estatus_utensilios') is-invalid @enderror" wire:model="estatus_utensilios">
                                                <option value="">-- Seleccione --</option>
                                                <option value="Buenos">Buenos</option>
                                                <option value="Regular">Regular</option>
                                                <option value="Malos">Malos</option>
                                            </select>
                                            @error('estatus_utensilios') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="posee_meson">
                                                <span class="form-check-label fw-semibold small">¿Posee Mesón?</span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="posee_fregadero">
                                                <span class="form-check-label fw-semibold small">Fregadero</span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="posee_tanque_agua">
                                                <span class="form-check-label fw-semibold small">Tanque agua</span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="posee_estante_almacenamiento">
                                                <span class="form-check-label fw-semibold small">Estante almacen.</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Observaciones -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Observaciones</label>
                                    <textarea class="form-control form-control-sm @error('observaciones_operatividad') is-invalid @enderror"
                                              wire:model="observaciones_operatividad" rows="3"
                                              placeholder="Observaciones adicionales sobre la operatividad"></textarea>
                                    @error('observaciones_operatividad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Navigation Buttons for Step 2 -->
                                <div class="border-top pt-3 mt-4">
                                    <div class="d-flex justify-content-between">
                                        <button type="button" wire:click="previousStep" class="btn btn-sm btn-label-secondary">
                                            <i class="ri ri-arrow-left-line me-1"></i> Anterior
                                        </button>
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="ri ri-save-line me-1"></i> Guardar Casa de Alimentación
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Coordenadas por defecto
    const defaultLat = -12.0464;
    const defaultLng = -77.0428;

    // Inicializar mapa si existe el elemento
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof google !== 'undefined' && typeof window.initMap === 'function') {
            setTimeout(() => {
                window.initMap(defaultLat, defaultLng);
            }, 500);
        }
    });
</script>
@endpush
