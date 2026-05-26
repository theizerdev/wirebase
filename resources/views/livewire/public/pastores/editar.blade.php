<div class="container-xxl container-p-y mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card mb-4">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">Actualizar Datos Ministeriales</h5>
                        <p class="mb-0 text-muted">Complete la información solicitada en los siguientes pasos</p>
                    </div>
                    <a href="{{ route('public.pastores.busqueda') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ri ri-arrow-left-line"></i> Volver
                    </a>
                </div>
                <div class="card-body pt-4">
                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Paso {{ $currentStep }} de {{ $totalSteps }}</span>
                            <span class="text-primary fw-bold">{{ round(($currentStep / $totalSteps) * 100) }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                                 style="width: {{ ($currentStep / $totalSteps) * 100 }}%"
                                 aria-valuenow="{{ ($currentStep / $totalSteps) * 100 }}"
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <form wire:submit.prevent="save">
                        <!-- Paso 1: Datos Personales -->
                        @if($currentStep == 1)
                        <div class="tab-pane fade show active">
                            <div class="row">


                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Código <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                                           wire:model="codigo" placeholder="Ingrese el código" readonly>
                                    <div class="form-text">El código se genera automáticamente con 8 dígitos</div>
                                    @error('codigo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>



                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombres <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nombres') is-invalid @enderror"
                                           wire:model="nombres" placeholder="Ingrese los nombres">
                                    @error('nombres')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('apellidos') is-invalid @enderror"
                                           wire:model="apellidos" placeholder="Ingrese los apellidos">
                                    @error('apellidos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <label class="form-label">Documento de Identidad <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('documento') is-invalid @enderror"
                                           wire:model="documento" placeholder="Ingrese el documento">
                                    @error('documento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control @error('fe_nacimiento') is-invalid @enderror"
                                           wire:model.live="fe_nacimiento">
                                    @error('fe_nacimiento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Edad <small class="text-muted">(Calculada automáticamente)</small></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('edad') is-invalid @enderror"
                                               wire:model="edad" min="0" placeholder="Edad" readonly>
                                        <span class="input-group-text">
                                            <i class="ri ri-calculator-line" title="Edad calculada automáticamente"></i>
                                        </span>
                                    </div>
                                    @error('edad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Género</label>
                                    <select class="form-select @error('genero') is-invalid @enderror" wire:model.change="genero">
                                        <option value="">Seleccionar género</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                    </select>
                                    @error('genero')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Estado Civil</label>
                                    <select class="form-select @error('estado_civil') is-invalid @enderror" wire:model.change="estado_civil">
                                        <option value="">Seleccionar estado civil</option>
                                        <option value="Soltero">Soltero(a)</option>
                                        <option value="Casado">Casado(a)</option>
                                        <option value="Divorciado">Divorciado(a)</option>
                                        <option value="Viudo">Viudo(a)</option>
                                    </select>
                                    @error('estado_civil')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($estado_civil === 'Casado')
                                <div class="col-md-12 mb-3">
                                    <label class="form-label d-flex justify-content-between align-items-center">
                                        <span>Cónyuge (Pastor Registrado)</span>
                                        @if(!$conyuge_id)
                                        <button class="btn btn-sm btn-primary shadow-sm" type="button" wire:click="openConyugeModal" title="Registrar nuevo cónyuge">
                                            <i class="ri ri-user-add-line me-2"></i> Registrar cónyuge
                                        </button>
                                        @endif
                                    </label>
                                    <div>
                                        @if($pastorSeleccionado)
                                            @if($pastorSeleccionado->esConyuge() && $pastorSeleccionado->pastorPrincipal)
                                                {{-- El pastor actual es la esposa, mostrar al esposo --}}
                                                <div class="mt-2 alert alert-info p-2">
                                                    <small>
                                                        <i class="ri ri-team-line"></i>
                                                        <strong>Matrimonio Pastoral:</strong><br>
                                                        Esposo: {{ $pastorSeleccionado->pastorPrincipal->nombres }} {{ $pastorSeleccionado->pastorPrincipal->apellidos }}
                                                    </small>
                                                </div>
                                            @elseif($pastorSeleccionado->conyuge_id && $pastorSeleccionado->genero === 'Masculino')
                                                {{-- El pastor actual es el esposo, mostrar a la esposa --}}
                                                <div class="mt-2 alert alert-info p-2">
                                                    <small>
                                                        <i class="ri ri-team-line"></i>
                                                        <strong>Matrimonio Pastoral:</strong><br>
                                                        Esposa: {{ $pastorSeleccionado->conyuge->nombres }} {{ $pastorSeleccionado->conyuge->apellidos }}
                                                    </small>
                                                </div>
                                            @else
                                                {{-- Mostrar campo de texto con búsqueda --}}
                                                <div class="input-group">
                                                    <input type="text" 
                                                           class="form-control @error('conyuge_busqueda') is-invalid @enderror" 
                                                           wire:model.live.debounce.500ms="conyuge_busqueda"
                                                           placeholder="Buscar cónyuge por nombre o documento...">
                                                    @if($resultados_conyuge && $resultados_conyuge->count() > 0)
                                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            {{ $resultados_conyuge->count() }} encontrado(s)
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            @foreach($resultados_conyuge as $pastor_resultado)
                                                                <li>
                                                                    <a class="dropdown-item" href="#" wire:click.prevent="seleccionarConyuge({{ $pastor_resultado->id }})">
                                                                        {{ $pastor_resultado->nombres }} {{ $pastor_resultado->apellidos }} ({{ $pastor_resultado->documento }})
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item" href="#" wire:click.prevent="openConyugeModal">
                                                                    <i class="ri ri-user-add-line me-1"></i>Crear nuevo cónyuge
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    @elseif(!empty($conyuge_busqueda))
                                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            Opciones
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item" href="#" wire:click.prevent="openConyugeModal">
                                                                    <i class="ri ri-user-add-line me-1"></i>Crear nuevo cónyuge: "{{ $conyuge_busqueda }}"
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    @endif
                                                </div>
                                                @if($conyuge_encontrado)
                                                    <div class="mt-2 alert alert-success p-2">
                                                        <small>
                                                            <i class="ri ri-check-line"></i>
                                                            <strong>Cónyuge seleccionado:</strong> {{ $conyuge_encontrado->nombres }} {{ $conyuge_encontrado->apellidos }}
                                                            <button type="button" class="btn-close float-end" wire:click="limpiarConyuge"></button>
                                                        </small>
                                                    </div>
                                                @endif
                                            @endif
                                        @else
                                            {{-- Mostrar campo de texto con búsqueda --}}
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control @error('conyuge_busqueda') is-invalid @enderror" 
                                                       wire:model.live.debounce.500ms="conyuge_busqueda"
                                                       placeholder="Buscar cónyuge por nombre o documento...">
                                                @if($resultados_conyuge && $resultados_conyuge->count() > 0)
                                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        {{ $resultados_conyuge->count() }} encontrado(s)
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @foreach($resultados_conyuge as $pastor_resultado)
                                                            <li>
                                                                <a class="dropdown-item" href="#" wire:click.prevent="seleccionarConyuge({{ $pastor_resultado->id }})">
                                                                    {{ $pastor_resultado->nombres }} {{ $pastor_resultado->apellidos }} ({{ $pastor_resultado->documento }})
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" wire:click.prevent="openConyugeModal">
                                                                <i class="ri ri-user-add-line me-1"></i>Crear nuevo cónyuge
                                                            </a>
                                                        </li>
                                                    </ul>
                                                @elseif(!empty($conyuge_busqueda))
                                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        Opciones
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="#" wire:click.prevent="openConyugeModal">
                                                                <i class="ri ri-user-add-line me-1"></i>Crear nuevo cónyuge: "{{ $conyuge_busqueda }}"
                                                            </a>
                                                        </li>
                                                    </ul>
                                                @endif
                                            </div>
                                            @if($conyuge_encontrado)
                                                <div class="mt-2 alert alert-success p-2">
                                                    <small>
                                                        <i class="ri ri-check-line"></i>
                                                        <strong>Cónyuge seleccionado:</strong> {{ $conyuge_encontrado->nombres }} {{ $conyuge_encontrado->apellidos }}
                                                        <button type="button" class="btn-close float-end" wire:click="limpiarConyuge"></button>
                                                    </small>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    @error('conyuge_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           wire:model="email" placeholder="Ingrese el email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Teléfono Habitación</label>
                                    <input type="text" class="form-control @error('telefono_hab') is-invalid @enderror"
                                           wire:model="telefono_hab" placeholder="Ingrese el teléfono">
                                    @error('telefono_hab')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="@if($estado_civil === 'Soltero' || is_null($estado_civil)) col-md-12 @else col-md-12 @endif mb-3">
                                    <label class="form-label">Teléfono Móvil</label>
                                    <input type="text" class="form-control @error('telefono_tlf') is-invalid @enderror"
                                           wire:model="telefono_tlf" placeholder="Ingrese el teléfono móvil">
                                    @error('telefono_tlf')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Otro Teléfono</label>
                                    <input type="text" class="form-control @error('telefono_otro') is-invalid @enderror"
                                           wire:model="telefono_otro" placeholder="Ingrese otro teléfono">
                                    @error('telefono_otro')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>




                            </div>
                        </div>
                        @endif

                        <!-- Paso 2: Datos de Ubicación -->
                        @if($currentStep == 2)
                        <div class="tab-pane fade show active">
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <h6 class="text-primary">
                                        <i class="ri ri-map-pin-line me-2"></i>Dirección y Ubicación
                                    </h6>
                                    <p class="text-muted small">Ingrese la dirección y datos de ubicación del pastor</p>
                                    <hr>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Calle/Avenida</label>
                                    <input type="text" class="form-control @error('calle_avenida') is-invalid @enderror"
                                           wire:model="calle_avenida" placeholder="Ingrese la calle o avenida">
                                    @error('calle_avenida')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Urbanización</label>
                                    <input type="text" class="form-control @error('urbanizacion') is-invalid @enderror"
                                           wire:model="urbanizacion" placeholder="Ingrese la urbanización">
                                    @error('urbanizacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Edificio/Casa/Quinta</label>
                                    <input type="text" class="form-control @error('edificio_casa_quinta') is-invalid @enderror"
                                           wire:model="edificio_casa_quinta" placeholder="Ingrese el edificio">
                                    @error('edificio_casa_quinta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Piso</label>
                                    <input type="text" class="form-control @error('piso') is-invalid @enderror"
                                           wire:model="piso" placeholder="Piso">
                                    @error('piso')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Apartamento</label>
                                    <input type="text" class="form-control @error('apartamento') is-invalid @enderror"
                                           wire:model="apartamento" placeholder="Apto">
                                    @error('apartamento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select @error('estado_id') is-invalid @enderror" wire:model.live="estado_id">
                                        <option value="">Seleccionar estado</option>
                                        @foreach($estados as $estado)
                                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('estado_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Municipio</label>
                                    <select class="form-select @error('municipio_id') is-invalid @enderror" wire:model.live="municipio_id">
                                        <option value="">Seleccionar municipio</option>
                                        @foreach($municipios as $municipio)
                                            <option value="{{ $municipio->id }}">{{ $municipio->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('municipio_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ciudad</label>
                                    <select class="form-select @error('ciudad_id') is-invalid @enderror" wire:model.live="ciudad_id">
                                        <option value="">Seleccionar ciudad</option>
                                        @foreach($ciudades as $ciudad)
                                            <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('ciudad_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Parroquia</label>
                                    <select class="form-select @error('parroquia_id') is-invalid @enderror" wire:model="parroquia_id">
                                        <option value="">Seleccionar parroquia</option>
                                        @foreach($parroquias as $parroquia)
                                            <option value="{{ $parroquia->id }}">{{ $parroquia->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('parroquia_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>




                            </div>
                        </div>
                        @endif

                        <!-- Paso 3: Datos Académicos -->
                        @if($currentStep == 3)
                        <div class="tab-pane fade show active">
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <h6 class="text-primary">
                                        <i class="ri ri-graduation-cap-line me-2"></i>Formación Académica
                                    </h6>
                                    <p class="text-muted small">Ingrese la información académica del pastor</p>
                                    <hr>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Grado de Instrucción</label>
                                    <input type="text" class="form-control @error('grado_instruccion') is-invalid @enderror"
                                           wire:model="grado_instruccion" placeholder="Ingrese el grado de instrucción">
                                    @error('grado_instruccion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Título Obtenido</label>
                                    <input type="text" class="form-control @error('titulo_obtenido') is-invalid @enderror"
                                           wire:model="titulo_obtenido" placeholder="Ingrese el título obtenido">
                                    @error('titulo_obtenido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model.live="estudio_teologico" id="estudio_teologico">
                                        <label class="form-check-label" for="estudio_teologico">¿Ha estudiado teología?</label>
                                    </div>
                                    @error('estudio_teologico')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($estudio_teologico)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Título Teológico</label>
                                    <input type="text" class="form-control @error('titulo_teologico') is-invalid @enderror"
                                           wire:model="titulo_teologico" placeholder="Ingrese el título teológico">
                                    @error('titulo_teologico')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Tiempo de Estudio</label>
                                    <input type="text" class="form-control @error('tiempo_de_estudio_teologico') is-invalid @enderror"
                                           wire:model="tiempo_de_estudio_teologico" placeholder="Ej: 4 años">
                                    @error('tiempo_de_estudio_teologico')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Instituto Teológico</label>
                                    <input type="text" class="form-control @error('instituto_teologico') is-invalid @enderror"
                                           wire:model="instituto_teologico" placeholder="Ingrese el instituto">
                                    @error('instituto_teologico')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Paso 4: Datos Ministeriales -->
                        @if($currentStep == 4)
                        <div class="tab-pane fade show active">
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <h6 class="text-primary">
                                        <i class="ri ri-church-line me-2"></i>Información Ministerial
                                    </h6>
                                    <p class="text-muted small">Ingrese los datos ministeriales del pastor</p>
                                    <hr>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Nivel Ministerial</label>
                                    <input type="text" class="form-control @error('nivel_ministerial') is-invalid @enderror"
                                           wire:model="nivel_ministerial" placeholder="Ingrese el nivel ministerial">
                                    @error('nivel_ministerial')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Año de Promoción</label>
                                    <input type="text" class="form-control @error('ano_promocion') is-invalid @enderror"
                                           wire:model="ano_promocion" placeholder="Ingrese el año de promoción">
                                    @error('ano_promocion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tiempo Colaborando</label>
                                    <input type="text" class="form-control @error('tiempo_colaborando') is-invalid @enderror"
                                           wire:model="tiempo_colaborando" placeholder="Ej: 10 años">
                                    @error('tiempo_colaborando')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Zona</label>
                                    <input type="text" class="form-control @error('zona') is-invalid @enderror"
                                           wire:model="zona" placeholder="Ingrese la zona">
                                    @error('zona')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Distrito</label>
                                    <input type="text" class="form-control @error('distrito') is-invalid @enderror"
                                           wire:model="distrito" placeholder="Ingrese el distrito">
                                    @error('distrito')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Cargo Nacional</label>
                                    <input type="text" class="form-control @error('cargo_nacional') is-invalid @enderror"
                                           wire:model="cargo_nacional" placeholder="Ingrese el cargo nacional">
                                    @error('cargo_nacional')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Mención</label>
                                    <textarea class="form-control @error('mencion') is-invalid @enderror"
                                              wire:model="mencion" rows="3" placeholder="Ingrese alguna mención o logro"></textarea>
                                    @error('mencion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="pertenece_ministerio" id="pertenece_ministerio">
                                        <label class="form-check-label" for="pertenece_ministerio">¿Pertenece al ministerio?</label>
                                    </div>
                                    @error('pertenece_ministerio')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="batizado_espiritu_santo" id="batizado_espiritu_santo">
                                        <label class="form-check-label" for="batizado_espiritu_santo">¿Bautizado con el Espíritu Santo?</label>
                                    </div>
                                    @error('batizado_espiritu_santo')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="status" id="status">
                                        <label class="form-check-label" for="status">Activo</label>
                                    </div>
                                    @error('status')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Foto del Pastor</label>
                                    <div class="d-flex align-items-start gap-3">
                                        <!-- Vista previa de la foto -->
                                        <div class="text-center">
                                            @if($foto)
                                                <div class="position-relative d-inline-block">
                                                    <img src="{{ Storage::url($foto) }}" alt="Foto del Pastor"
                                                         class="rounded-circle border border-2 border-primary"
                                                         style="width: 120px; height: 120px; object-fit: cover;">
                                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle"
                                                            wire:click="removeFoto" style="width: 25px; height: 25px; padding: 0;">
                                                        <i class="ri ri-close-line"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-center bg-light rounded-circle border border-2 border-dashed border-secondary"
                                                     style="width: 120px; height: 120px;">
                                                    <div class="text-center text-muted">
                                                        <i class="ri ri-user-line fs-2"></i>
                                                        <div class="small">Sin foto</div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Campo de carga -->
                                        <div class="flex-grow-1">
                                            <div class="mb-2">
                                                <input type="file" class="form-control @error('foto_temporal') is-invalid @enderror"
                                                       wire:model="foto_temporal" accept="image/*">
                                                @error('foto_temporal')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="small text-muted">
                                                <i class="ri ri-information-line me-1"></i>
                                                Suba una imagen cuadrada o rectangular. El sistema la recortará automáticamente en formato tipo carnet (300x300px).
                                                Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB.
                                            </div>

                                            <!-- Indicador de carga -->
                                            <div wire:loading wire:target="foto_temporal" class="mt-2">
                                                <div class="d-flex align-items-center text-primary">
                                                    <div class="spinner-border spinner-border-sm me-2" role="status">
                                                        <span class="visually-hidden">Cargando...</span>
                                                    </div>
                                                    Procesando imagen...
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mensajes de estado -->
                                    @if (session()->has('message') && str_contains(session('message'), 'Foto'))
                                        <div class="alert alert-success alert-sm mt-2 mb-0">
                                            <i class="ri ri-check-line me-1"></i>
                                            {{ session('message') }}
                                        </div>
                                    @endif

                                    @if (session()->has('error') && str_contains(session('error'), 'imagen'))
                                        <div class="alert alert-danger alert-sm mt-2 mb-0">
                                            <i class="ri ri-error-warning-line me-1"></i>
                                            {{ session('error') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Notas</label>
                                    <textarea class="form-control @error('nota') is-invalid @enderror"
                                              wire:model="nota" rows="3" placeholder="Ingrese notas adicionales"></textarea>
                                    @error('nota')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Botones de navegación -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            @if($currentStep == 1)
                                <div></div>
                            @else
                                <button type="button" class="btn btn-label-secondary" wire:click="prevStep">
                                    <i class="ri ri-arrow-left-line me-1"></i> Anterior
                                </button>
                            @endif

                            @if($currentStep == $totalSteps)
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri ri-save-line me-1"></i> Guardar Pastor
                                </button>
                            @else
                                <button type="button" class="btn btn-primary" wire:click="nextStep">
                                    Siguiente <i class="ri ri-arrow-right-line ms-1"></i>
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>


        </div>
    </div>

    <!-- Modal para registrar cónyuge -->
    <div class="modal fade" id="conyugeModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Datos del Cónyuge</h5>
                    <button type="button" class="btn-close" wire:click="closeConyugeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('conyuge_nombres') is-invalid @enderror"
                                   wire:model="conyuge_nombres" placeholder="Ingrese los nombres del cónyuge">
                            @error('conyuge_nombres')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('conyuge_apellidos') is-invalid @enderror"
                                   wire:model="conyuge_apellidos" placeholder="Ingrese los apellidos del cónyuge">
                            @error('conyuge_apellidos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Documento de Identidad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('conyuge_documento') is-invalid @enderror"
                                   wire:model="conyuge_documento" placeholder="Ingrese el documento del cónyuge">
                            @error('conyuge_documento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Género</label>
                            <select class="form-select @error('conyuge_genero') is-invalid @enderror" wire:model="conyuge_genero">
                                <option value="">Seleccionar género</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                            @error('conyuge_genero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" wire:click="closeConyugeModal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveConyuge">
                        <i class="ri ri-save-line me-1"></i> Registrar Cónyuge
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Verificación de Seguridad -->
    @if($showSecurityModal)
    <div class="modal-backdrop fade show" style="z-index: 1050;"></div>
    <div class="modal fade show d-block" tabindex="-1" style="z-index: 1051;" id="securityModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="ri ri-shield-keyhole-line me-2"></i>
                        Verificación de Seguridad
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="$set('showSecurityModal', false)" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if($securityError)
                        <div class="alert alert-danger mb-4">
                            <i class="ri ri-error-warning-line me-2"></i>
                            {{ $securityError }}
                        </div>
                    @endif

                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-start">
                            <i class="ri ri-information-line fs-4 me-3"></i>
                            <div>
                                <h6 class="alert-heading mb-2">Verificación Requerida</h6>
                                <p class="mb-0 small">
                                    Para proteger sus datos, debe verificar su identidad antes de guardar los cambios.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs para seleccionar método de verificación -->
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $securityVerificationType === 'questions' ? 'active' : '' }}"
                                    wire:click="$set('securityVerificationType', 'questions')"
                                    type="button">
                                <i class="ri ri-questionnaire-line me-1"></i>
                                Preguntas de Seguridad
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $securityVerificationType === 'backup_code' ? 'active' : '' }}"
                                    wire:click="$set('securityVerificationType', 'backup_code')"
                                    type="button">
                                <i class="ri ri-key-2-line me-1"></i>
                                Código de Respaldo
                            </button>
                        </li>
                    </ul>

                    <!-- Verificación con Preguntas -->
                    @if($securityVerificationType === 'questions')
                        <form wire:submit.prevent="verifySecurityAnswers">
                            @foreach($securityQuestions as $index => $questionData)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                        {{ $questionData['pregunta'] }}
                                    </label>
                                    <input type="text" 
                                           class="form-control @error("securityAnswers.{$index}") is-invalid @enderror"
                                           wire:model="securityAnswers.{{ $index }}"
                                           placeholder="Ingrese su respuesta"
                                           autocomplete="off">
                                    
                                    @error("securityAnswers.{$index}")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        <i class="ri ri-check-line me-2"></i>
                                        Verificar Respuestas
                                    </span>
                                    <span wire:loading>
                                        <i class="ri ri-loader-4-line me-2"></i>
                                        Verificando...
                                    </span>
                                </button>
                            </div>
                        </form>

                    <!-- Verificación con Backup Code -->
                    @elseif($securityVerificationType === 'backup_code')
                        <form wire:submit.prevent="verifyBackupCode">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri ri-key-2-line me-1"></i>
                                    Código de Respaldo
                                </label>
                                <input type="text" 
                                       class="form-control text-center @error('backupCodeInput') is-invalid @enderror"
                                       wire:model="backupCodeInput"
                                       placeholder="XXXX-XXXX"
                                       style="font-size: 1.5rem; letter-spacing: 3px;"
                                       maxlength="9"
                                       autocomplete="off">
                                
                                @error('backupCodeInput')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <small class="text-muted mt-1 d-block">
                                    Ingrese uno de sus 10 códigos de respaldo
                                </small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning btn-lg" wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        <i class="ri ri-check-line me-2"></i>
                                        Verificar Código
                                    </span>
                                    <span wire:loading>
                                        <i class="ri ri-loader-4-line me-2"></i>
                                        Verificando...
                                    </span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Mostrar la modal cuando showConyugeModal es true -->
    <script>
        document.addEventListener('livewire:init', function () {

            window.addEventListener('open-conyuge-modal', function () {
                const modal = new bootstrap.Modal(document.getElementById('conyugeModal'));
                modal.show();
            });

            window.addEventListener('close-conyuge-modal', function () {
                const modalEl = document.getElementById('conyugeModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
            });
        });
    </script>
</div>
