<div>
    @section('title', 'Detalles de Casa de Alimentación')

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.casas_alimentacion.index') }}">Casas de Alimentación</a></li>
                <li class="breadcrumb-item active">Detalles</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="ri ri-home-heart-line me-2 text-primary"></i>Detalles de Casa de Alimentación</h5>
                            <div>
                                @can('edit casas_alimentacion')
                                <a href="{{ route('admin.casas_alimentacion.edit', $casa) }}" class="btn btn-sm btn-primary">
                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        {{-- Información principal --}}
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-xl me-3">
                                        <span class="avatar-initial rounded bg-label-primary" style="font-size: 1.5rem;">
                                            {{ substr($casa->codigo, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h4 class="mb-0">{{ $casa->codigo }}</h4>
                                        <div class="text-muted">{{ $casa->vocero_alimentacion ?: 'Sin vocero designado' }}</div>
                                        <div>
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
                                                <span class="badge bg-info-subtle text-info ms-1">Zona base de misiones</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <div class="small text-muted">Fecha de registro</div>
                                        <div class="fw-semibold">
                                            @if($casa->fecha)
                                                {{ $casa->fecha->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">No especificada</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted mt-2">Consejo Comunal</div>
                                        <div class="fw-semibold">{{ $casa->consejo_comunal ?: 'No especificado' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sección: Ubicación --}}
                        <div class="border-top pt-3 mb-4">
                            <h6 class="fw-semibold mb-3"><i class="ri ri-map-pin-line me-2"></i>Ubicación</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="small text-muted">Estado</div>
                                    <div class="fw-semibold">{{ $casa->estado?->nombre ?: 'No especificado' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="small text-muted">Municipio</div>
                                    <div class="fw-semibold">{{ $casa->municipio?->nombre ?: 'No especificado' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="small text-muted">Parroquia</div>
                                    <div class="fw-semibold">{{ $casa->parroquia?->nombre ?: 'No especificado' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">Sector / Comunidad</div>
                                    <div class="fw-semibold">{{ $casa->sector ?: 'No especificado' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">Dirección completa</div>
                                    <div class="fw-semibold">
                                        {{ $casa->calle_avenida ?: 'Sin calle' }}{{ $casa->numero_vivienda ? ' #' . $casa->numero_vivienda : '' }}
                                    </div>
                                    <div class="small text-muted">{{ $casa->punto_referencia ?: 'Sin punto de referencia' }}</div>
                                </div>
                                @if($casa->zona_base_misiones)
                                <div class="col-md-12">
                                    <div class="small text-muted">Información de base de misiones</div>
                                    <div class="fw-semibold">
                                        @if($casa->distancia_a_base_misiones)
                                            Distancia: {{ $casa->distancia_a_base_misiones }} km
                                        @else
                                            Zona base de misiones
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Sección: Contacto --}}
                        <div class="border-top pt-3 mb-4">
                            <h6 class="fw-semibold mb-3"><i class="ri ri-contacts-line me-2"></i>Contacto</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="small text-muted">Vocero de Alimentación</div>
                                    <div class="fw-semibold">{{ $casa->vocero_alimentacion ?: 'No especificado' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">Teléfono principal</div>
                                    @if($casa->telefono_principal)
                                        <div class="fw-semibold">
                                            <a href="tel:{{ $casa->telefono_principal }}" class="text-decoration-none">{{ $casa->telefono_principal }}</a>
                                        </div>
                                    @else
                                        <div class="fw-semibold text-muted">No especificado</div>
                                    @endif
                                </div>
                                @if($casa->telefono_secundario)
                                <div class="col-md-6">
                                    <div class="small text-muted">Teléfono secundario</div>
                                    <div class="fw-semibold">
                                        <a href="tel:{{ $casa->telefono_secundario }}" class="text-decoration-none">{{ $casa->telefono_secundario }}</a>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Sección: Estado operativo --}}
                        <div class="border-top pt-3 mb-4">
                            <h6 class="fw-semibold mb-3"><i class="ri ri-information-line me-2"></i>Estado operativo</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="small text-muted">Estado CDA</div>
                                    <div>
                                        @php
                                            $estadoColor = match($casa->estado_cda) {
                                                'Operativa' => 'success',
                                                'Inoperativa' => 'danger',
                                                'Inactiva' => 'warning',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $estadoColor }}-subtle text-{{ $estadoColor }}">{{ $casa->estado_cda }}</span>
                                    </div>
                                </div>
                                @if(in_array($casa->estado_cda, ['Inoperativa', 'Inactiva']) && $casa->motivo_inoperatividad)
                                <div class="col-md-12">
                                    <div class="small text-muted">Motivo de inoperatividad</div>
                                    <div class="fw-semibold">{{ $casa->motivo_inoperatividad }}</div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Sección: Información adicional --}}
                        <div class="border-top pt-3">
                            <h6 class="fw-semibold mb-3"><i class="ri ri-calendar-line me-2"></i>Información adicional</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="small text-muted">Creado el</div>
                                    <div class="fw-semibold">{{ $casa->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">Última actualización</div>
                                    <div class="fw-semibold">{{ $casa->updated_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="border-top pt-3 mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('admin.casas_alimentacion.index') }}" class="btn btn-sm btn-label-secondary">
                                        <i class="ri ri-arrow-left-line me-1"></i> Volver al listado
                                    </a>
                                </div>
                                <div class="d-flex gap-2">
                                    @can('edit casas_alimentacion')
                                    <a href="{{ route('admin.casas_alimentacion.edit', $casa) }}" class="btn btn-sm btn-primary">
                                        <i class="ri ri-pencil-line me-1"></i> Editar
                                    </a>
                                    @endcan
                                    @can('delete casas_alimentacion')
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            wire:click="deleteCasa({{ $casa->id }})"
                                            wire:confirm="¿Estás seguro de eliminar esta casa de alimentación?">
                                        <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Método para eliminar la casa
    function deleteCasa(id) {
        if (confirm('¿Estás seguro de eliminar esta casa de alimentación? Esta acción no se puede deshacer.')) {
            Livewire.dispatch('deleteCasa', { casaId: id });
        }
    }
</script>
@endpush