<div>
    {{-- Estilos de Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @push('styles')
    <style>
        #mapa-iglesia {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            margin-bottom: 15px;
            z-index: 1; /* Para evitar conflictos con dropdowns */
        }
        .coordenadas-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        .ubicacion-actual {
            color: #495057;
            font-weight: 500;
        }
        
        /* Estilos para el buscador de pastores */
        .pastor-search-results {
            position: absolute;
            width: 100%;
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .pastor-search-results .list-group-item {
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .pastor-search-results .list-group-item:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
        }
        
        .pastor-search-results .list-group-item:active {
            background-color: #e9ecef;
        }
    </style>
    @endpush
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Editar Extensión</h5>
                        <a href="{{ route('admin.iglesias.index') }}" class="btn btn-label-secondary">
                            <i class="ri ri-arrow-left-line"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Indicador de pasos -->
                    <div class="bs-stepper wizard-numbered mt-2 mb-4">
                        <div class="bs-stepper-header">
                            <div class="step @if($currentStep >= 1) active @endif" wire:click="goToStep(1)">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">1</span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Datos Básicos</span>
                                        <span class="bs-stepper-subtitle">Información general</span>
                                    </span>
                                </button>
                            </div>
                            <div class="line"></div>
                            <div class="step @if($currentStep >= 2) active @endif" wire:click="goToStep(2)">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">2</span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Ubicación</span>
                                        <span class="bs-stepper-subtitle">Dirección y geolocalización</span>
                                    </span>
                                </button>
                            </div>
                            <div class="line"></div>
                            <div class="step @if($currentStep >= 3) active @endif" wire:click="goToStep(3)">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">3</span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Información</span>
                                        <span class="bs-stepper-subtitle">Detalles de la extensión</span>
                                    </span>
                                </button>
                            </div>
                            <div class="line"></div>
                            <div class="step @if($currentStep >= 4) active @endif" wire:click="goToStep(4)">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">4</span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Estadísticas</span>
                                        <span class="bs-stepper-subtitle">Datos estadísticos</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <form wire:submit="save">
                        <!-- Paso 1: Datos Básicos -->
                        @if($currentStep == 1)
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <h6 class="text-muted mb-3">Datos Básicos de la Extensión</h6>
                                    <hr>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" wire:model.change="nombre">
                                    @error('nombre') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Pastor Principal</label>
                                    
                                    <!-- Campo de búsqueda -->
                                    <div class="position-relative">
                                        <input type="text" 
                                               class="form-control" 
                                               wire:model.live.debounce.300ms="pastorSearch"
                                               placeholder="Buscar por nombre, apellido o cédula..."
                                               autocomplete="off">
                                        
                                        @if($pastor_id)
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger position-absolute top-50 end-0 translate-middle-y me-1"
                                                    wire:click="limpiarBusquedaPastor"
                                                    title="Limpiar selección">
                                                <i class="ri ri-close-line"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <!-- Resultados de búsqueda -->
                                    @if($mostrarResultadosPastor && count($pastoresBuscados) > 0)
                                        <div class="list-group position-absolute w-100 mt-1 shadow-lg border" style="z-index: 1050; max-height: 300px; overflow-y: auto; background-color: #ffffff; border-radius: 0.5rem;">
                                            @foreach($pastoresBuscados as $pastor)
                                                <button type="button" 
                                                        class="list-group-item list-group-item-action"
                                                        wire:click="seleccionarPastor({{ $pastor->id }})">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>{{ $pastor->nombres }} {{ $pastor->apellidos }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="ri ri-id-card-line"></i> {{ $pastor->documento }}
                                                                @if($pastor->zona)
                                                                    | <i class="ri ri-map-pin-line"></i> {{ $pastor->zona }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                        @if($pastor->conyuge_id || $pastor->pastoresConyuge->count() > 0)
                                                            <span class="badge bg-info">
                                                                <i class="ri ri-team-line"></i> Matrimonio
                                                            </span>
                                                        @endif
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    @error('pastor_id') <div class="text-danger mt-1">{{ $message }}</div> @enderror

                                    <!-- Información del pastor seleccionado -->
                                    @if($pastorSeleccionado)
                                        <div class="mt-3">
                                            @if($pastorSeleccionado->esConyuge() && $pastorSeleccionado->pastorPrincipal)
                                                {{-- El pastor seleccionado es la esposa (tiene conyuge_id asignado y es Femenino) --}}
                                                <div class="alert alert-info p-2">
                                                    <small>
                                                        <i class="ri-team-line"></i>
                                                        <strong>Matrimonio Pastoral:</strong><br>
                                                        Pastor Principal: {{ $pastorSeleccionado->pastorPrincipal->nombres }} {{ $pastorSeleccionado->pastorPrincipal->apellidos }}<br>
                                                        Cónyuge: {{ $pastorSeleccionado->nombres }} {{ $pastorSeleccionado->apellidos }}
                                                    </small>
                                                </div>
                                            @elseif($pastorSeleccionado->conyuge_id && $pastorSeleccionado->genero === 'Masculino')
                                                {{-- El pastor seleccionado es el esposo (pastor principal) y tiene esposa --}}
                                                <div class="alert alert-info p-2">
                                                    <small>
                                                        <i class="ri-team-line"></i>
                                                        <strong>Matrimonio Pastoral:</strong><br>
                                                        Pastor Principal: {{ $pastorSeleccionado->nombres }} {{ $pastorSeleccionado->apellidos }}<br>
                                                        Cónyuge: {{ $pastorSeleccionado->conyuge->nombres }} {{ $pastorSeleccionado->conyuge->apellidos }}
                                                    </small>
                                                </div>
                                            @elseif($pastorSeleccionado->pastoresConyuge->count() > 0)
                                                {{-- Fallback: El pastor seleccionado es el esposo (pastor principal) --}}
                                                <div class="alert alert-info p-2">
                                                    <small>
                                                        <i class="ri-team-line"></i>
                                                        <strong>Matrimonio Pastoral:</strong><br>
                                                        Pastor Principal: {{ $pastorSeleccionado->nombres }} {{ $pastorSeleccionado->apellidos }}<br>
                                                        Cónyuge: {{ $pastorSeleccionado->pastoresConyuge->first()->nombres }} {{ $pastorSeleccionado->pastoresConyuge->first()->apellidos }}
                                                    </small>
                                                </div>
                                            @else
                                                {{-- El pastor seleccionado no tiene cónyuge --}}
                                                <div class="text-muted">
                                                    <small>
                                                        <i class="ri-user-line"></i>
                                                        Pastor: {{ $pastorSeleccionado->nombres }} {{ $pastorSeleccionado->apellidos }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" wire:model.change="telefono">
                                    @error('telefono') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" wire:model.change="email">
                                    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3" wire:ignore>
                                    <label class="form-label">Fecha de Fundación</label>
                                    <input type="date" class="form-control" wire:model.live="fecha_fundacion">
                                    @error('fecha_fundacion') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Años Activa</label>
                                    <input type="text" class="form-control" value="{{ intval($this->aniosActiva) }}" disabled readonly>
                                </div>
                            </div>
                        @endif

                        <!-- Paso 2: Ubicación -->
                        @if($currentStep == 2)
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <h6 class="text-muted mb-3">Ubicación Geográfica</h6>
                                    <hr>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" wire:model.live="estado_id">
                                        <option value="">Seleccionar</option>
                                        @foreach($estados as $estado)
                                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('estado_id') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Ciudad</label>
                                    <select class="form-select" wire:model.live="ciudad_id">
                                        <option value="">Seleccionar</option>
                                        @foreach($ciudades as $ciudad)
                                            <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('ciudad_id') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Municipio</label>
                                    <select class="form-select" wire:model.live="municipio_id">
                                        <option value="">Seleccionar</option>
                                        @foreach($municipios as $municipio)
                                            <option value="{{ $municipio->id }}">{{ $municipio->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('municipio_id') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Parroquia</label>
                                    <select class="form-select" wire:model.change="parroquia_id">
                                        <option value="">Seleccionar</option>
                                        @foreach($parroquias as $parroquia)
                                            <option value="{{ $parroquia->id }}">{{ $parroquia->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('parroquia_id') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Zona</label>
                                    <input type="text" class="form-control" wire:model.change="zona">
                                    @error('zona') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Distrito</label>
                                    <input type="text" class="form-control" wire:model.change="distrito">
                                    @error('distrito') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Sector</label>
                                    <input type="text" class="form-control" wire:model.change="sector">
                                    @error('sector') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Calle</label>
                                    <input type="text" class="form-control" wire:model.change="calle">
                                    @error('calle') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Avenida</label>
                                    <input type="text" class="form-control" wire:model.change="avenida">
                                    @error('avenida') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Dirección</label>
                                    <textarea class="form-control" wire:model.change="direccion" rows="3" placeholder="La dirección se actualizará automáticamente al hacer clic en el mapa"></textarea>
                                    @error('direccion') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                {{-- Mapa interactivo con LeafletJS --}}
                                <div class="col-12 mb-3" wire:ignore>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Ubicación en el Mapa</label>
                                        <button type="button" onclick="getCurrentLocation()" class="btn btn-primary btn-sm">
                                            <i class="ri-crosshairs-gps-line me-1"></i> Ubicación Actual
                                        </button>
                                    </div>
                                    <div class="coordenadas-info">
                                        <div class="ubicacion-actual">
                                            <i class="ri-map-pin-line"></i>
                                            Coordenadas actuales:
                                            <span id="coordenadas-actuales">
                                                @if($latitud && $longitud)
                                                    {{ number_format($latitud, 6) }}, {{ number_format($longitud, 6) }}
                                                @else
                                                    No seleccionado
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div id="mapa-iglesia"></div>
                                    <small class="text-muted">
                                        <i class="ri-information-line"></i>
                                        Haz clic en el mapa para seleccionar la ubicación exacta de la iglesia.
                                        También puedes arrastrar el marcador para ajustar la posición.
                                    </small>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Latitud</label>
                                    <input type="text" class="form-control" wire:model.change="latitud" readonly>
                                    @error('latitud') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Longitud</label>
                                    <input type="text" class="form-control" wire:model.change="longitud" readonly>
                                    @error('longitud') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        @endif

                        <!-- Paso 3: Información de la Extensión -->
                        @if($currentStep == 3)
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <h6 class="text-muted mb-3">Información de la Extensión</h6>
                                    <hr>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo de Local</label>
                                    <select class="form-select" wire:model.change="tipo_local_id">
                                        <option value="">Seleccionar Tipo</option>
                                        @foreach($tiposLocal as $tipoLocal)
                                            <option value="{{ $tipoLocal->id }}">{{ $tipoLocal->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_local_id') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" wire:model.change="activa" id="activa">
                                        <label class="form-check-label" for="activa">
                                            Extensión Activa
                                        </label>
                                    </div>
                                    @error('activa') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" wire:model.change="descripcion" rows="4"></textarea>
                                    @error('descripcion') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        @endif

                        <!-- Paso 4: Estadísticas y Medios -->
                        @if($currentStep == 4)
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <h6 class="text-muted mb-3">Estadísticas y Medios de Comunicación</h6>
                                    <hr>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Miembros Activos</label>
                                    <input type="number" class="form-control" wire:model.change="miembros_activos" min="0">
                                    @error('miembros_activos') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Campos Blancos</label>
                                    <input type="number" class="form-control" wire:model.change="cantidad_campos_blancos" min="0">
                                    @error('cantidad_campos_blancos') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Miembro Probante</label>
                                    <input type="number" class="form-control" wire:model.change="miembro_probante" min="0">
                                    @error('miembro_probante') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Tiempo de Trabajo</label>
                                    <input type="text" class="form-control" wire:model.change="tiempo_trabajo" placeholder="Ej: 5 años">
                                    @error('tiempo_trabajo') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Iglesias Fundadas</label>
                                    <input type="number" class="form-control" wire:model.change="iglesias_fundadas" min="0">
                                    @error('iglesias_fundadas') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Pastores en Ministerio</label>
                                    <input type="number" class="form-control" wire:model.change="pastores_ministerio" min="0">
                                    @error('pastores_ministerio') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Logros Obtenidos</label>
                                    <textarea class="form-control" wire:model.change="logros_obtenidos" rows="3"></textarea>
                                    @error('logros_obtenidos') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model.change="posee_medio_comunicacion" id="posee_medio_comunicacion">
                                        <label class="form-check-label" for="posee_medio_comunicacion">
                                            ¿Posee Medio de Comunicación?
                                        </label>
                                    </div>
                                </div>

                                @if($posee_medio_comunicacion)
                                    <div class="col-12 mt-3">
                                        <div class="card bg-light border">
                                            <div class="card-body">
                                                <h6 class="card-title"><i class="ri-shopping-cart-line"></i> Agregar Medio de Comunicación</h6>
                                                <div class="row align-items-end">
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label">Tipo de Medio</label>
                                                        <select class="form-select" wire:model="nuevo_medio_tipo">
                                                            <option value="">Seleccionar</option>
                                                            <option value="Radio">Radio</option>
                                                            <option value="Televisión">Televisión</option>
                                                            <option value="Internet">Internet</option>
                                                            <option value="Prensa">Prensa</option>
                                                            <option value="Otro">Otro</option>
                                                        </select>
                                                        @error('nuevo_medio_tipo') <div class="text-danger small">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Nombre del Medio</label>
                                                        <input type="text" class="form-control" wire:model="nuevo_medio_nombre" placeholder="Ej: Radio Renuevo">
                                                        @error('nuevo_medio_nombre') <div class="text-danger small">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label">Ubicación / Frecuencia / URL</label>
                                                        <input type="text" class="form-control" wire:model="nuevo_medio_ubicacion" placeholder="Ej: 89.1 FM">
                                                        @error('nuevo_medio_ubicacion') <div class="text-danger small">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="col-md-2 mb-3">
                                                        <button type="button" class="btn btn-primary w-100" wire:click="agregarMedio">
                                                            <i class="ri-add-line"></i> Agregar
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Lista de medios agregados -->
                                                @if(count($medios_comunicacion_list) > 0)
                                                    <div class="mt-4">
                                                        <h6>Medios Agregados:</h6>
                                                        <ul class="list-group">
                                                            @foreach($medios_comunicacion_list as $index => $medio)
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <strong>{{ $medio['tipo'] }}</strong>: {{ $medio['nombre'] }}
                                                                        @if($medio['ubicacion'])
                                                                            <span class="text-muted">({{ $medio['ubicacion'] }})</span>
                                                                        @endif
                                                                    </div>
                                                                    <button type="button" class="btn btn-sm btn-danger btn-icon" wire:click="eliminarMedio({{ $index }})" title="Eliminar">
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </button>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Botones de navegación -->
                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                @if($currentStep > 1)
                                    <button type="button" class="btn btn-label-secondary" wire:click="previousStep">
                                        <i class="ri ri-arrow-left-line"></i> Anterior
                                    </button>
                                @else
                                    <a href="{{ route('admin.iglesias.index') }}" class="btn btn-label-secondary">
                                        <i class="ri ri-arrow-left-line"></i> Volver
                                    </a>
                                @endif
                            </div>

                            <div>
                                @if($currentStep < $totalSteps)
                                    <button type="button" class="btn btn-primary" wire:click="nextStep">
                                        Siguiente <i class="ri ri-arrow-right-line"></i>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-success">
                                        <i class="ri ri-save-line"></i> Actualizar Extensión
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.bs-stepper-header {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
}

.step {
    flex: 1;
    display: flex;
    align-items: center;
    cursor: pointer;
}

.step-trigger {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 0.5rem;
    background: none;
    border: none;
    text-align: left;
}

.bs-stepper-circle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    margin-right: 0.5rem;
    font-weight: 600;
}

.step.active .bs-stepper-circle {
    background-color: #7367f0;
    color: white;
}

.step.completed .bs-stepper-circle {
    background-color: #28c76f;
    color: white;
}

.bs-stepper-label {
    flex: 1;
}

.bs-stepper-title {
    display: block;
    font-weight: 600;
    color: #495057;
}

.step.active .bs-stepper-title {
    color: #7367f0;
}

.bs-stepper-subtitle {
    display: block;
    font-size: 0.875rem;
    color: #6c757d;
}

.line {
    flex: 1;
    height: 1px;
    background-color: #e9ecef;
    margin: 0 1rem;
}

.step.completed ~ .line {
    background-color: #28c76f;
}
</style>
@endpush

@push('scripts')
{{-- Script para cerrar el dropdown de búsqueda al hacer clic fuera --}}
<script>
document.addEventListener('click', function(e) {
    const pastorSearchInput = document.querySelector('[wire\:model="pastorSearch"]');
    const pastorResults = document.querySelector('.list-group.position-absolute');
    
    if (pastorSearchInput && pastorResults) {
        if (!pastorSearchInput.contains(e.target) && !pastorResults.contains(e.target)) {
            // Disparar evento para ocultar resultados
            Livewire.dispatch('closePastorDropdown');
        }
    }
});
</script>

{{-- Script de Leaflet --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Función para obtener la ubicación actual del usuario (Global scope)
window.getCurrentLocation = function() {
    if (navigator.geolocation) {
        const btnLocation = document.querySelector('button[onclick="getCurrentLocation()"]');
        let originalHtml = '';
        if (btnLocation) {
            originalHtml = btnLocation.innerHTML;
            btnLocation.innerHTML = '<i class="ri-loader-4-line ri-spin me-1"></i> Buscando...';
            btnLocation.disabled = true;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Disparar un evento personalizado con las coordenadas para que las procese el mapa interno
                const event = new CustomEvent('location-found', { detail: { lat: lat, lng: lng } });
                window.dispatchEvent(event);

                if (btnLocation) {
                    btnLocation.innerHTML = originalHtml;
                    btnLocation.disabled = false;
                }
            },
            function(error) {
                console.error('Error obteniendo ubicación:', error);
                alert('No se pudo obtener tu ubicación. Por favor, verifica los permisos de tu navegador.');
                if (btnLocation) {
                    btnLocation.innerHTML = originalHtml;
                    btnLocation.disabled = false;
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    } else {
        alert('Tu navegador no soporta geolocalización.');
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Coordenadas iniciales de Venezuela
    const venezuelaCoords = [8.0, -66.0];
    let mapa;
    let marcador;
    let mapaInicializado = false;
    let coordenadasGuardadas = null;

    // Función para obtener coordenadas de la ciudad o estado desde Livewire
    async function obtenerCoordenadasGeograficas() {
        try {
            // Primero intentar obtener la ciudad (más preciso)
            const coordenadasCiudad = await @this.getCoordenadasCiudad();
            if (coordenadasCiudad && coordenadasCiudad.lat && coordenadasCiudad.lng) {
                return { ...coordenadasCiudad, zoom: 12 };
            }

            // Si no hay ciudad, intentar obtener el estado
            const coordenadasEstado = await @this.getCoordenadasEstado();

            if (coordenadasEstado) {
                // Si el backend devolvió lat/lng directos
                if (coordenadasEstado.lat && coordenadasEstado.lng) {
                    return { ...coordenadasEstado, zoom: 8 };
                }
                // Si el backend devolvió el nombre del estado para geocodificar
                else if (coordenadasEstado.nombre) {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(coordenadasEstado.nombre)}&limit=1`, {
                        headers: {
                            'Accept': 'application/json',
                            'User-Agent': 'Extensión-Location-Picker'
                        }
                    });
                    const data = await response.json();
                    if (data && data.length > 0) {
                        return { lat: parseFloat(data[0].lat), lng: parseFloat(data[0].lon), zoom: 8 };
                    }
                }
            }

            return { lat: venezuelaCoords[0], lng: venezuelaCoords[1], zoom: 6 };
        } catch (error) {
            console.error('Error al obtener coordenadas geográficas:', error);
            return { lat: venezuelaCoords[0], lng: venezuelaCoords[1], zoom: 6 };
        }
    }

    // Función para obtener coordenadas actuales del formulario
    function obtenerCoordenadasFormulario() {
        const latInput = document.querySelector('[wire\\:model="latitud"]');
        const lngInput = document.querySelector('[wire\\:model="longitud"]');

        if (latInput && lngInput) {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);

            if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                return { lat, lng };
            }
        }
        return null;
    }

    // Función para inicializar el mapa
    async function inicializarMapa() {
        console.log('Inicializando mapa...');

        // Destruir mapa existente si hay uno
        if (mapa) {
            mapa.remove();
            mapa = null;
            marcador = null;
        }

        // Obtener coordenadas del formulario (coordenadas guardadas)
        coordenadasGuardadas = obtenerCoordenadasFormulario();

        // Obtener coordenadas de la ciudad o estado seleccionado
        const coordenadasGeograficas = await obtenerCoordenadasGeograficas();

        // Decidir qué coordenadas usar
        let coordsCentro;
        let zoomLevel = 12;

        if (coordenadasGuardadas) {
            // Si hay coordenadas guardadas, usar esas
            coordsCentro = [coordenadasGuardadas.lat, coordenadasGuardadas.lng]; // Leaflet usa [lat, lng]
            zoomLevel = 15;
        } else {
            // Usar coordenadas de la ciudad, estado o defecto
            coordsCentro = [coordenadasGeograficas.lat, coordenadasGeograficas.lng];
            zoomLevel = coordenadasGeograficas.zoom || 6;
        }

        // Crear el mapa Leaflet
        mapa = L.map('mapa-iglesia').setView(coordsCentro, zoomLevel);

        // Agregar capa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mapa);

        // Si hay coordenadas guardadas, agregar marcador
        if (coordenadasGuardadas) {
            agregarMarcador([coordenadasGuardadas.lat, coordenadasGuardadas.lng]);
        }

        // Evento de clic en el mapa
        mapa.on('click', function(e) {
            agregarMarcador([e.latlng.lat, e.latlng.lng]);
            actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
        });

        mapaInicializado = true;
        console.log('Mapa inicializado correctamente');
    }

    // Función para agregar o mover marcador
    function agregarMarcador(coordenadas) {
        // Si ya existe un marcador, moverlo
        if (marcador) {
            marcador.setLatLng(coordenadas);
        } else {
            // Crear nuevo marcador
            marcador = L.marker(coordenadas, {
                draggable: true
            }).addTo(mapa);

            // Evento de arrastre del marcador
            marcador.on('dragend', function(e) {
                const position = marcador.getLatLng();
                actualizarCoordenadas(position.lat, position.lng);
            });
        }
    }

    // Escuchar evento personalizado de ubicación encontrada
    window.addEventListener('location-found', function(e) {
        const lat = e.detail.lat;
        const lng = e.detail.lng;

        if (mapa) {
            mapa.flyTo([lat, lng], 15);

            agregarMarcador([lat, lng]);
            actualizarCoordenadas(lat, lng);
        }
    });

    // Función para obtener dirección a partir de coordenadas (Geocoding Inverso con Nominatim)
    async function obtenerDireccionDesdeCoordenadas(lat, lng) {
        try {
            // Consulta directa a Nominatim OpenStreetMap (CORS habilitado)
            const nominatimUrl = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&addressdetails=1&accept-language=es`;

            const response = await fetch(nominatimUrl, {
                headers: {
                    'Accept': 'application/json',
                    'User-Agent': 'Extensión-Location-Picker' // Requerido por Nominatim
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data && data.display_name) {
                // Limpiar y formatear la dirección
                let direccion = data.display_name;

                // Si la dirección es muy larga, truncarla a un tamaño razonable
                if (direccion.length > 200) {
                    direccion = direccion.substring(0, 200) + '...';
                }

                console.log('Dirección formateada (Nominatim):', direccion);
                return direccion;
            } else {
                console.warn('No se encontró display_name en la respuesta de Nominatim:', data);
                return null;
            }
        } catch (error) {
            console.error('Error al obtener dirección desde Nominatim:', error);

            // Si falla Nominatim, generar una dirección básica con las coordenadas
            const direccionBasica = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
            console.log('Usando dirección básica:', direccionBasica);
            return direccionBasica;
        }
    }

    // Función para actualizar las coordenadas en Livewire
    async function actualizarCoordenadas(lat, lng) {
        console.log('Actualizando coordenadas:', lat, lng);

        // Actualizar el texto de coordenadas
        const coordenadasActuales = document.getElementById('coordenadas-actuales');
        if (coordenadasActuales) {
            coordenadasActuales.textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
        }

        // Actualizar SOLO los campos de latitud y longitud específicos
        const latInput = document.querySelector('input[wire\\:model="latitud"]');
        const lngInput = document.querySelector('input[wire\\:model="longitud"]');

        if (latInput) {
            latInput.value = lat.toFixed(6);
            latInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        if (lngInput) {
            lngInput.value = lng.toFixed(6);
            lngInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        // Obtener dirección a partir de las coordenadas
        const direccion = await obtenerDireccionDesdeCoordenadas(lat, lng);
        console.log('Dirección obtenida:', direccion);

        if (direccion) {
            // Actualizar SOLO el campo de dirección específico
            const direccionTextarea = document.querySelector('textarea[wire\\:model="direccion"]');
            if (direccionTextarea) {
                direccionTextarea.value = direccion;
                direccionTextarea.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }

        // Llamar al método de Livewire con la dirección
        @this.actualizarCoordenadas(lat, lng, direccion);
    }

    // Función para verificar si el mapa debe estar visible
    function verificarYInicializarMapa() {
        const mapaContainer = document.getElementById('mapa-iglesia');

        if (mapaContainer && mapaContainer.offsetParent !== null && !mapaInicializado) {
            console.log('Contenedor del mapa visible, inicializando...');
            setTimeout(inicializarMapa, 200);
        } else if (mapaContainer && mapaContainer.offsetParent !== null && mapaInicializado) {
            setTimeout(function() {
                if (mapa) {
                    mapa.invalidateSize();
                }
            }, 200);
        }
    }

    // Escuchar eventos de Livewire para re-inicializar el mapa o reposicionar
    Livewire.hook('message.processed', (message, component) => {
        setTimeout(verificarYInicializarMapa, 300);
    });

    // Escuchar eventos personalizados de Livewire para cambiar ubicación (Estado/Ciudad)
    window.addEventListener('update-map-location', async function(e) {
        if (mapa && mapaInicializado && !obtenerCoordenadasFormulario()) {
            const coordenadasGeograficas = await obtenerCoordenadasGeograficas();
            if (coordenadasGeograficas) {
                mapa.flyTo([coordenadasGeograficas.lat, coordenadasGeograficas.lng], coordenadasGeograficas.zoom || 8);
            }
        }
    });

    // Observar cambios en el DOM para detectar cuando el paso 2 se hace visible
    const observer = new MutationObserver(function(mutations) {
        const mapaContainer = document.getElementById('mapa-iglesia');
        if (mapaContainer && mutations.some(mutation =>
            mutation.type === 'childList' ||
            (mutation.type === 'attributes' &&
             (mutation.target.id === 'mapa-iglesia' ||
              mutation.target.closest('#mapa-iglesia') ||
              mutation.target.classList.contains('step') ||
              mutation.target.classList.contains('active')))
        )) {
            verificarYInicializarMapa();
        }
    });

    // Observar cambios en el DOM, pero solo elementos relevantes
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['class']
    });

    // Inicializar si el paso 2 ya está visible al cargar
    setTimeout(verificarYInicializarMapa, 1000);
});
</script>
@endpush
