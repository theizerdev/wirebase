<div>
    @section('title', 'Editar Casa de Alimentación')

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.casas_alimentacion.index') }}">Casas de Alimentación</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="mb-0"><i class="ri ri-home-heart-line me-2 text-primary"></i>Editar Casa de Alimentación</h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="save">
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

                            <div class="border-top pt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('admin.casas_alimentacion.index') }}" class="btn btn-sm btn-label-secondary">
                                        <i class="ri ri-arrow-left-line me-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="ri ri-save-line me-1"></i> Actualizar Casa
                                    </button>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar mapa con las coordenadas actuales
        if (typeof google !== 'undefined' && typeof window.initMap === 'function' && window.currentLat && window.currentLng) {
            setTimeout(() => {
                window.initMap(window.currentLat, window.currentLng);
            }, 500);
        }
    });
    
    // Pasar coordenadas actuales al script del mapa
    @if($latitud && $longitud)
        window.currentLat = {{ $latitud }};
        window.currentLng = {{ $longitud }};
    @else
        window.currentLat = -12.0464;
        window.currentLng = -77.0428;
    @endif
</script>
@endpush