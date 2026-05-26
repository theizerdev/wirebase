<div>
    Uncaught SyntaxError: Unexpected token '}' (at 1:1226:1)<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Detalle del Pastor</h5>
                            <p class="mb-0">Información completa del pastor</p>
                        </div>
                        <div class="d-flex gap-2">
                            @can('edit pastores')
                            <a href="{{ route('admin.pastores.edit', $pastor->id) }}" class="btn btn-primary">
                                <i class="ri ri-edit-line"></i> Editar
                            </a>
                            @endcan
                            <a href="{{ route('admin.pastores.index') }}" class="btn btn-label-secondary">
                                <i class="ri ri-arrow-left-line"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Datos Personales -->
                        <div class="col-12 mb-4">
                            <h6 class="text-primary">
                                <i class="ri ri-user-line me-2"></i>Datos Personales
                            </h6>
                            <hr>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Código:</label>
                            <p>{{ $pastor->codigo }}</p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Documento:</label>
                            <p>{{ $pastor->documento }}</p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Nombres:</label>
                            <p>{{ $pastor->nombres }}</p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Apellidos:</label>
                            <p>{{ $pastor->apellidos }}</p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Fecha de Nacimiento:</label>
                            <p>{{ $pastor->fe_nacimiento ? $pastor->fe_nacimiento->format('d/m/Y') : 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Edad:</label>
                            <p>{{ $pastor->edad ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Género:</label>
                            <p>{{ $pastor->genero ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Estado Civil:</label>
                            <p>{{ $pastor->estado_civil ?? 'No especificado' }}</p>
                        </div>
                        
                        @if($pastor->estado_civil === 'Casado' && $pastor->conyuge)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cónyuge:</label>
                            <p>
                                <a href="{{ route('admin.pastores.show', $pastor->conyuge->id) }}">
                                    {{ $pastor->conyuge->nombres }} {{ $pastor->conyuge->apellidos }}
                                </a>
                            </p>
                        </div>
                        @endif
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Email:</label>
                            <p>{{ $pastor->email ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Teléfono Habitación:</label>
                            <p>{{ $pastor->telefono_hab ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Teléfono Móvil:</label>
                            <div class="d-flex align-items-center gap-2">
                                <p class="mb-0">{{ $pastor->telefono_tlf ?? 'No especificado' }}</p>
                                @if($pastor->user_id)
                                    @php
                                        $user = \App\Models\User::find($pastor->user_id);
                                        $tieneSeguridad = $pastor->preguntasSeguridad && $pastor->preguntasSeguridad->activado;
                                    @endphp
                                    @if(!$tieneSeguridad)
                                        <livewire:admin.pastores.solicitar-modificacion-telefono :pastor="$pastor" />
                                    @endif
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Otro Teléfono:</label>
                            <p>{{ $pastor->telefono_otro ?? 'No especificado' }}</p>
                        </div>
                        
                        <!-- Datos de Ubicación -->
                        <div class="col-12 mt-4 mb-4">
                            <h6 class="text-primary">
                                <i class="ri ri-map-pin-line me-2"></i>Datos de Ubicación
                            </h6>
                            <hr>
                        </div>
                        
                        @if($pastor->direccion_completa)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Dirección:</label>
                            <p>{{ $pastor->direccion_completa }}</p>
                        </div>
                        @endif
                        
                        @if($pastor->estado)
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Estado:</label>
                            <p>{{ $pastor->estado->nombre }}</p>
                        </div>
                        @endif
                        
                        @if($pastor->municipio)
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Municipio:</label>
                            <p>{{ $pastor->municipio->nombre }}</p>
                        </div>
                        @endif
                        
                        @if($pastor->ciudad)
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Ciudad:</label>
                            <p>{{ $pastor->ciudad->nombre }}</p>
                        </div>
                        @endif
                        
                        @if($pastor->parroquia)
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Parroquia:</label>
                            <p>{{ $pastor->parroquia->nombre }}</p>
                        </div>
                        @endif
                        
                        @if($pastor->latitud && $pastor->longitud)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Coordenadas:</label>
                            <p>Lat: {{ $pastor->latitud }}, Lng: {{ $pastor->longitud }}</p>
                        </div>
                        @endif
                        
                        <!-- Seguridad de la Cuenta -->
                        @if($pastor->user_id)
                        <div class="col-12 mt-4 mb-4">
                            <h6 class="text-primary">
                                <i class="ri ri-shield-keyhole-line me-2"></i>Seguridad de la Cuenta
                            </h6>
                            <hr>
                        </div>
                        
                        <div class="col-12 mb-3">
                            @php
                                $tieneSeguridad = $pastor->preguntasSeguridad && $pastor->preguntasSeguridad->activado;
                            @endphp
                            
                            @if($tieneSeguridad)
                                <div class="alert alert-success d-flex align-items-center">
                                    <i class="ri ri-shield-check-fill fs-4 me-3"></i>
                                    <div>
                                        <h6 class="mb-1 fw-bold">✅ Protección Activada</h6>
                                        <p class="mb-0 small">
                                            Este pastor tiene configuradas preguntas de seguridad y códigos de respaldo.
                                            <br>
                                            <strong>Backup codes disponibles:</strong> {{ $pastor->preguntasSeguridad->backupCodesDisponibles() }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning d-flex align-items-center">
                                    <i class="ri ri-alert-line fs-4 me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">⚠️ Sin Protección de Seguridad</h6>
                                        <p class="mb-2 small">
                                            Este pastor aún no ha configurado sus preguntas de seguridad. Se recomienda configurarlas para proteger su cuenta.
                                        </p>
                                        @can('edit pastores')
                                            <a href="{{ route('admin.pastores.configurar-seguridad', $pastor->id) }}" class="btn btn-warning btn-sm">
                                                <i class="ri ri-shield-keyhole-line me-1"></i>
                                                Configurar Seguridad Ahora
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            @endif
                        </div>
                        @endif
                        
                        <!-- Datos Académicos -->
                        <div class="col-12 mt-4 mb-4">
                            <h6 class="text-primary">
                                <i class="ri ri-graduation-cap-line me-2"></i>Datos Académicos
                            </h6>
                            <hr>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Grado de Instrucción:</label>
                            <p>{{ $pastor->grado_instruccion ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Título Obtenido:</label>
                            <p>{{ $pastor->titulo_obtenido ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">¿Estudió Teología?</label>
                            <p>
                                @if($pastor->estudio_teologico)
                                    <span class="badge bg-success">Sí</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </p>
                        </div>
                        
                        @if($pastor->estudio_teologico)
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Título Teológico:</label>
                            <p>{{ $pastor->titulo_teologico ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Tiempo de Estudio Teológico:</label>
                            <p>{{ $pastor->tiempo_de_estudio_teologico ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Instituto Teológico:</label>
                            <p>{{ $pastor->instituto_teologico ?? 'No especificado' }}</p>
                        </div>
                        @endif
                        
                        <!-- Datos Ministeriales -->
                        <div class="col-12 mt-4 mb-4">
                            <h6 class="text-primary">
                                <i class="ri ri-church-line me-2"></i>Datos Ministeriales
                            </h6>
                            <hr>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nivel Ministerial:</label>
                            <p>{{ $pastor->nivel_ministerial ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Año de Promoción:</label>
                            <p>{{ $pastor->ano_promocion ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Tiempo Colaborando:</label>
                            <p>{{ $pastor->tiempo_colaborando ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Zona:</label>
                            <p>{{ $pastor->zona ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Distrito:</label>
                            <p>{{ $pastor->distrito ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Cargo Nacional:</label>
                            <p>{{ $pastor->cargo_nacional ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">¿Pertenece al Ministerio?</label>
                            <p>
                                @if($pastor->pertenece_ministerio)
                                    <span class="badge bg-success">Sí</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">¿Bautizado con el Espíritu Santo?</label>
                            <p>
                                @if($pastor->batizado_espiritu_santo)
                                    <span class="badge bg-success">Sí</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </p>
                        </div>
                        
                        @if($pastor->mencion)
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Mención:</label>
                            <p>{{ $pastor->mencion }}</p>
                        </div>
                        @endif
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Estado:</label>
                            <p>
                                @if($pastor->status)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </p>
                        </div>
                        
                        @if($pastor->nota)
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Nota:</label>
                            <p>{{ $pastor->nota }}</p>
                        </div>
                        @endif
                        
                        <!-- Datos de Cónyuge -->
                        @if($pastor->conyuge)
                        <div class="col-12 mt-4 mb-4">
                            <h6 class="text-primary">
                                <i class="ri ri-heart-line me-2"></i>Datos del Cónyuge
                            </h6>
                            <hr>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombre del Cónyuge:</label>
                            <p>{{ $pastor->conyuge->nombres }} {{ $pastor->conyuge->apellidos }}</p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Código:</label>
                            <p>{{ $pastor->conyuge->codigo }}</p>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Nivel Ministerial:</label>
                            <p>{{ $pastor->conyuge->nivel_ministerial ?? 'No especificado' }}</p>
                        </div>
                        @endif
                        
                        <!-- Datos de Extensión -->
                        @if($this->churchesToDisplay->count() > 0)
                        <div class="col-12 mt-4 mb-4">
                            <h6 class="text-primary">
                                <i class="ri ri-building-line me-2"></i>Datos de Extensión
                            </h6>
                            <hr>
                        </div>
                        
                        @if($pastor->conyuge && $this->churchesToDisplay->count() > 0)
                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <i class="ri ri-information-line me-2"></i>
                                Esta información incluye las extensiones asociadas tanto a {{ $pastor->nombres }} como a su cónyuge {{ $pastor->conyuge->nombres }}.
                            </div>
                        </div>
                        @endif
                        
                        @foreach($this->churchesToDisplay as $iglesia)
                        <div class="col-12 mb-4">
                            <div class="border rounded p-3 bg-light">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Nombre de la Iglesia:</label>
                                        <p>{{ $iglesia->nombre }}</p>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Dirección:</label>
                                        <p>{{ $iglesia->direccion ?? 'No especificada' }}</p>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">Teléfono:</label>
                                        <p>{{ $iglesia->telefono ?? 'No especificado' }}</p>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">Email:</label>
                                        <p>{{ $iglesia->email ?? 'No especificado' }}</p>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">Zona:</label>
                                        <p>{{ $iglesia->zona ?? 'No especificada' }}</p>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">Distrito:</label>
                                        <p>{{ $iglesia->distrito ?? 'No especificado' }}</p>
                                    </div>
                                    
                                    @if($iglesia->fecha_fundacion)
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">Fecha de Fundación:</label>
                                        <p>{{ $iglesia->fecha_fundacion->format('d/m/Y') }}</p>
                                    </div>
                                    @endif
                                    
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">Estado:</label>
                                        <p>
                                            @if($iglesia->activa)
                                                <span class="badge bg-success">Activa</span>
                                            @else
                                                <span class="badge bg-secondary">Inactiva</span>
                                            @endif
                                        </p>
                                    </div>
                                    
                                    @if($iglesia->descripcion)
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Descripción:</label>
                                        <p>{{ $iglesia->descripcion }}</p>
                                    </div>
                                    @endif
                                    
                                    @if($iglesia->latitud && $iglesia->longitud)
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Ubicación:</label>
                                        <div wire:ignore id="mapa-iglesia-{{ $iglesia->id }}" class="border rounded" style="height: 300px;"></div>
                                        <p class="mt-2 mb-0 text-muted">
                                            <small>Coordenadas: {{ $iglesia->latitud }}, {{ $iglesia->longitud }}</small>
                                        </p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Initialize maps when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    @foreach($this->churchesToDisplay as $iglesia)
        @if($iglesia->latitud && $iglesia->longitud)
        // Initialize map for church {{ $iglesia->id }}
        (function() {
            var mapId = 'mapa-iglesia-{{ $iglesia->id }}';
            var mapElement = document.getElementById(mapId);
            
            if (mapElement && typeof L !== 'undefined') {
                var map = L.map(mapId).setView([{{ $iglesia->latitud }}, {{ $iglesia->longitud }}], 15);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                
                var marker = L.marker([{{ $iglesia->latitud }}, {{ $iglesia->longitud }}])
                    .addTo(map)
                    .bindPopup('<b>{{ $iglesia->nombre }}</b><br>{{ $iglesia->direccion ?? "Dirección no especificada" }}')
                    .openPopup();
                
                // Ensure map renders properly
                setTimeout(function() {
                    map.invalidateSize();
                }, 200);
            }
        })();
        @endif
    @endforeach
});
</script>
@endpush
</div>