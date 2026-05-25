<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Editar Pastor</h5>
                            <p class="mb-0">Modifique los datos del pastor en el sistema</p>
                        </div>
                        <a href="{{ route('admin.pastores.index') }}" class="btn btn-label-secondary">
                            <i class="ri ri-arrow-left-line"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Indicador de pasos -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="steps-progress-container my-5">
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar"
                                         role="progressbar"
                                         style="width: {{ ($currentStep - 1) / $totalSteps * 100 }}%"
                                         aria-valuenow="{{ ($currentStep - 1) / $totalSteps * 100 }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100"></div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    @for ($i = 1; $i <= $totalSteps; $i++)
                                        <div class="text-center" style="width: {{ 100 / $totalSteps }}%">
                                            <div class="step-indicator d-inline-flex align-items-center justify-content-center rounded-circle
                                                {{ $i < $currentStep ? 'bg-success text-white' : ($i == $currentStep ? 'bg-primary text-white' : 'bg-light text-dark') }}"
                                                style="width: 40px; height: 40px;">
                                                @if ($i < $currentStep)
                                                    <i class="ri ri-check-line"></i>
                                                @else
                                                    <span>{{ $i }}</span>
                                                @endif
                                            </div>
                                            <div class="small mt-2 text-muted">
                                                @if ($i == 1) Datos Personales
                                                @elseif ($i == 2) Datos de Ubicación
                                                @elseif ($i == 3) Datos Académicos
                                                @elseif ($i == 4) Datos Ministeriales
                                                @endif
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
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
                                    <select class="form-select @error('genero') is-invalid @enderror" wire:model="genero">
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
                                    <label class="form-label">Buscar cónyuge</label>
                                    <input type="text" class="form-control @error('searchConyuge') is-invalid @enderror"
                                           wire:model.live="searchConyuge"
                                           placeholder="Buscar por nombre, apellido o cédula">
                                    <div class="form-text">
                                        @if($genero === 'Masculino')
                                            Mostrando mujeres disponibles
                                        @elseif($genero === 'Femenino')
                                            Mostrando hombres disponibles
                                        @else
                                            Seleccione el género para filtrar el cónyuge.
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Cónyuge</label>
                                    <div>
                                        @if($conyuge_id)
                                            @php
                                                $selected = \App\Models\Pastor::find($conyuge_id);
                                            @endphp
                                            @if($selected)
                                                <div class="d-flex align-items-center gap-3 p-2 border rounded">
                                                    @php
                                                        $fotoUrl = null;
                                                        if(!empty($selected->foto)) {
                                                            if(preg_match('/^https?:\/\//i', $selected->foto)) {
                                                                $fotoUrl = $selected->foto;
                                                            } else {
                                                                $fotoUrl = Storage::url($selected->foto);
                                                            }
                                                        }
                                                    @endphp
                                                    @if($fotoUrl)
                                                         <img class="rounded-circle img-fluid border border-2 border-primary"
                                                            src="/pastores/{{ str_replace(' ', '',$selected->foto) }}"
                                                            alt="Foto de {{ $selected->nombres }} {{ $selected->apellidos }}" 
                                                            style="width: 80px; height: 80px; object-fit: cover;" />
                                                    @else
                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                                            <i class="ri ri-user-line fs-4 text-muted"></i>
                                                        </div>
                                                    @endif

                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold">{{ $selected->nombres }} {{ $selected->apellidos }}</div>
                                                        <div class="small text-muted">{{ $selected->documento }}</div>
                                                    </div>

                                                    <div class="d-flex gap-2">
                                                        <a href="{{ url('admin/pastores/'.$selected->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver perfil">
                                                            Ver
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="$set('conyuge_id', '')" title="Quitar selección">Quitar</button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-muted small">Seleccionado (ID: {{ $conyuge_id }})</div>
                                            @endif
                                        @else
                                            <div class="text-muted small">No hay cónyuge seleccionado. Seleccione uno desde la lista de resultados.</div>
                                        @endif

                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-primary" type="button" wire:click="openConyugeModal" title="Registrar nuevo cónyuge">
                                                <i class="ri ri-user-add-line me-2"></i> Registrar cónyuge
                                            </button>
                                        </div>
                                    </div>
                                    @if($searchConyuge !== '' && empty($pastores))
                                        <div class="form-text text-muted">No se encontraron resultados para la búsqueda.</div>
                                    @endif
                                    @error('conyuge_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- Lista de resultados clickeables -->
                                @if(!empty($pastores))
                                    <div class="col-12">
                                        <div class="list-group mt-2">
                                            @foreach($pastores as $p)
                                                @php
                                                    $label = $p->nombres . ' ' . $p->apellidos . ' — ' . $p->documento;
                                                    if(trim($searchConyuge) !== '') {
                                                        $pattern = '/' . preg_quote(trim($searchConyuge), '/') . '/i';
                                                        $labelHighlighted = preg_replace($pattern, '<mark>$0</mark>', e($label));
                                                    } else {
                                                        $labelHighlighted = e($label);
                                                    }
                                                @endphp

                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <a href="#" wire:click.prevent="selectConyuge({{ $p->id }})" class="flex-grow-1 text-decoration-none text-body">
                                                        {!! $labelHighlighted !!}
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-secondary ms-2" wire:click.prevent="showPastorProfile({{ $p->id }})">Ver</button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

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

    <!-- Modal para ver perfil de pastor -->
    <div class="modal fade" id="viewPastorModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perfil del Pastor</h5>
                    <button type="button" class="btn-close" wire:click="closeViewPastor"></button>
                </div>
                <div class="modal-body">
                    @if($viewPastor)
                        <div class="d-flex gap-3 align-items-center">
                            @php
                                $fotoUrl = null;
                                if(!empty($viewPastor->foto)) {
                                    if(preg_match('/^https?:\/\//i', $viewPastor->foto)) {
                                        $fotoUrl = $viewPastor->foto;
                                    } else {
                                        $fotoUrl = Storage::url($viewPastor->foto);
                                    }
                                }
                            @endphp
                            @if($fotoUrl)
                                <img src="{{ $fotoUrl }}" alt="Foto" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width:80px;height:80px;">
                                    <i class="ri ri-user-line fs-2 text-muted"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold fs-5">{{ $viewPastor->nombres }} {{ $viewPastor->apellidos }}</div>
                                <div class="small text-muted">{{ $viewPastor->documento }}</div>
                                <div class="small text-muted">{{ $viewPastor->genero ?? '-' }} · {{ $viewPastor->estado_civil ?? '-' }}</div>
                            </div>
                        </div>
                        <hr>
                        <div>
                            <p class="mb-1"><strong>Email:</strong> {{ $viewPastor->email ?? '-' }}</p>
                            <p class="mb-1"><strong>Teléfono:</strong> {{ $viewPastor->telefono_tlf ?? $viewPastor->telefono_hab ?? '-' }}</p>
                        </div>
                    @else
                        <div class="text-muted">Cargando...</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeViewPastor">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', function () {
            window.addEventListener('open-view-pastor-modal', function () {
                const modal = new bootstrap.Modal(document.getElementById('viewPastorModal'));
                modal.show();
            });
            window.addEventListener('close-view-pastor-modal', function () {
                const modalEl = document.getElementById('viewPastorModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            });
        });
    </script>
