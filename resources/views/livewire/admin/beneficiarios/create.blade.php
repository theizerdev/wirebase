<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-1">Crear Beneficiario</h5>
                    <p class="mb-0">Completa la información para registrar un nuevo beneficiario</p>
                </div>
                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="save">
                        <!-- Wizard Progress Bar -->
                        <div class="mb-4">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $currentStep >= 1 ? 'bg-primary' : 'bg-secondary' }}" style="width: {{ $currentStep >= 1 ? '20%' : '0%' }}"></div>
                                <div class="progress-bar {{ $currentStep >= 2 ? 'bg-primary' : 'bg-secondary' }}" style="width: {{ $currentStep >= 2 ? '20%' : '0%' }}"></div>
                                <div class="progress-bar {{ $currentStep >= 3 ? 'bg-primary' : 'bg-secondary' }}" style="width: {{ $currentStep >= 3 ? '20%' : '0%' }}"></div>
                                <div class="progress-bar {{ $currentStep >= 4 ? 'bg-primary' : 'bg-secondary' }}" style="width: {{ $currentStep >= 4 ? '20%' : '0%' }}"></div>
                                <div class="progress-bar {{ $currentStep >= 5 ? 'bg-primary' : 'bg-secondary' }}" style="width: {{ $currentStep >= 5 ? '20%' : '0%' }}"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="text-center">
                                    <div class="badge {{ $currentStep == 1 ? 'bg-primary' : 'bg-secondary' }} rounded-pill">
                                        <i class="ri ri-user-line me-1"></i> Paso 1
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="badge {{ $currentStep == 2 ? 'bg-primary' : 'bg-secondary' }} rounded-pill">
                                        <i class="ri ri-graduation-cap-line me-1"></i> Paso 2
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="badge {{ $currentStep == 3 ? 'bg-primary' : 'bg-secondary' }} rounded-pill">
                                        <i class="ri ri-briefcase-line me-1"></i> Paso 3
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="badge {{ $currentStep == 4 ? 'bg-primary' : 'bg-secondary' }} rounded-pill">
                                        <i class="ri ri-heart-pulse-line me-1"></i> Paso 4
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="badge {{ $currentStep == 5 ? 'bg-primary' : 'bg-secondary' }} rounded-pill">
                                        <i class="ri ri-group-line me-1"></i> Paso 5
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 1: Personal Data -->
                        @if($currentStep == 1)
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h5><i class="ri ri-user-line me-2"></i>Datos Personales</h5>
                                <p class="text-muted">Información básica del beneficiario</p>
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
                                <label class="form-label">Cédula <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('cedula') is-invalid @enderror"
                                       wire:model="cedula" placeholder="Ingrese la cédula">
                                @error('cedula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                       wire:model.live="fecha_nacimiento">
                                @error('fecha_nacimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Edad</label>
                                <input type="text" class="form-control" value="{{ $edad ?: 'N/A' }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono Principal</label>
                                <input type="text" class="form-control @error('telefono_principal') is-invalid @enderror"
                                       wire:model="telefono_principal" placeholder="Ingrese el teléfono principal">
                                @error('telefono_principal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono Alternativo</label>
                                <input type="text" class="form-control @error('telefono_alternativo') is-invalid @enderror"
                                       wire:model="telefono_alternativo" placeholder="Ingrese el teléfono alternativo">
                                @error('telefono_alternativo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado Civil</label>
                                <select class="form-select @error('estado_civil') is-invalid @enderror" wire:model="estado_civil">
                                    <option value="">Seleccione...</option>
                                    <option value="Soltero(a)">Soltero(a)</option>
                                    <option value="Casado(a)">Casado(a)</option>
                                    <option value="Viudo(a)">Viudo(a)</option>
                                    <option value="Divorciado(a)">Divorciado(a)</option>
                                </select>
                                @error('estado_civil')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Casa de Alimentación</label>
                                <select class="form-select" wire:model.live="casa_alimentacion_id">
                                    <option value="">Seleccione una casa de alimentación...</option>
                                    @foreach($casas_alimentacion as $casa)
                                        <option value="{{ $casa->id }}">{{ $casa->codigo }} - {{ $casa->sector ?? 'Sin sector' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Responsable Asociado</label>
                                <select class="form-select" wire:model.live="responsable_id">
                                    <option value="">Sin responsable</option>
                                    @foreach($responsables as $responsable)
                                        <option value="{{ $responsable->id }}">{{ $responsable->nombre_completo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Campos de ubicación geográfica -->
                            @php
                                $casaSeleccionada = $casa_alimentacion_id ? collect($casas_alimentacion)->firstWhere('id', $casa_alimentacion_id) : null;
                            @endphp
                            @if($responsable_id)
                                <!-- Mostrar ubicación del responsable como texto de solo lectura -->
                                @php
                                    $responsableSeleccionado = collect($responsables)->firstWhere('id', $responsable_id);
                                @endphp
                                @if($responsableSeleccionado)
                                    <div class="col-12 mb-2">
                                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                                            <i class="ri ri-information-line me-2"></i>
                                            La ubicación ha sido copiada automáticamente del responsable seleccionado.
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Estado</label>
                                        <input type="text" class="form-control" value="{{ $responsableSeleccionado->estado->nombre ?? 'No especificado' }}" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Municipio</label>
                                        <input type="text" class="form-control" value="{{ $responsableSeleccionado->municipio->nombre ?? 'No especificado' }}" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Parroquia</label>
                                        <input type="text" class="form-control" value="{{ $responsableSeleccionado->parroquia->nombre ?? 'No especificado' }}" readonly>
                                    </div>
                                @endif
                            @elseif($casaSeleccionada)
                                <div class="col-12 mb-2">
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        <i class="ri ri-information-line me-2"></i>
                                        La ubicación ha sido copiada automáticamente de la casa de alimentación seleccionada.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Estado</label>
                                    <input type="text" class="form-control" value="{{ $casaSeleccionada->estado->nombre ?? 'No especificado' }}" readonly>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Municipio</label>
                                    <input type="text" class="form-control" value="{{ $casaSeleccionada->municipio->nombre ?? 'No especificado' }}" readonly>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Parroquia</label>
                                    <input type="text" class="form-control" value="{{ $casaSeleccionada->parroquia->nombre ?? 'No especificado' }}" readonly>
                                </div>
                            @else
                                <!-- Mostrar selectores normales cuando no hay responsable ni casa -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select @error('estado_id') is-invalid @enderror" wire:model.live="estado_id">
                                        <option value="">Seleccione...</option>
                                        @foreach($estados as $estado)
                                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('estado_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Municipio</label>
                                    <select class="form-select @error('municipio_id') is-invalid @enderror" wire:model.live="municipio_id">
                                        <option value="">Seleccione...</option>
                                        @foreach($municipios as $municipio)
                                            <option value="{{ $municipio->id }}">{{ $municipio->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('municipio_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Parroquia</label>
                                    <select class="form-select @error('parroquia_id') is-invalid @enderror" wire:model="parroquia_id">
                                        <option value="">Seleccione...</option>
                                        @foreach($parroquias as $parroquia)
                                            <option value="{{ $parroquia->id }}">{{ $parroquia->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('parroquia_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>
                        @endif

                        <!-- Step 2: Education Data -->
                        @if($currentStep == 2)
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h5><i class="ri ri-graduation-cap-line me-2"></i>Educación</h5>
                                <p class="text-muted">Información sobre nivel de instrucción y estudios actuales</p>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="estudia_actualmente" wire:model.live="estudia_actualmente">
                                    <label class="form-check-label" for="estudia_actualmente">¿Estudia actualmente?</label>
                                </div>
                            </div>

                            @if($estudia_actualmente)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">¿Qué está estudiando?</label>
                                <input type="text" class="form-control @error('estudio_actual') is-invalid @enderror"
                                       wire:model="estudio_actual" placeholder="Ingrese lo que está estudiando">
                                @error('estudio_actual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nivel de Instrucción <span class="text-danger">*</span></label>
                                <select class="form-select @error('nivel_instruccion') is-invalid @enderror" wire:model="nivel_instruccion">
                                    <option value="">Seleccione...</option>
                                    <option value="Analfabeto">Analfabeto</option>
                                    <option value="Básica">Básica</option>
                                    <option value="Media Diversificada">Media Diversificada</option>
                                    <option value="TSU">TSU</option>
                                    <option value="Universitario">Universitario</option>
                                    <option value="Maestría">Maestría</option>
                                    <option value="Doctorado">Doctorado</option>
                                </select>
                                @error('nivel_instruccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Último Título Obtenido</label>
                                <input type="text" class="form-control @error('ultimo_titulo_obtenido') is-invalid @enderror"
                                       wire:model="ultimo_titulo_obtenido" placeholder="Ingrese el último título obtenido">
                                @error('ultimo_titulo_obtenido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <!-- Step 3: Work Data and Additional Fields -->
                        @if($currentStep == 3)
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h5><i class="ri ri-briefcase-line me-2"></i>Trabajo</h5>
                                <p class="text-muted">Información sobre situación laboral</p>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="trabaja_actualmente" wire:model.live="trabaja_actualmente">
                                    <label class="form-check-label" for="trabaja_actualmente">¿Trabaja actualmente?</label>
                                </div>
                            </div>

                            @if($trabaja_actualmente)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Lugar de Trabajo</label>
                                <input type="text" class="form-control @error('lugar_trabajo') is-invalid @enderror"
                                       wire:model="lugar_trabajo" placeholder="Ingrese el lugar de trabajo">
                                @error('lugar_trabajo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ocupación</label>
                                <input type="text" class="form-control @error('ocupacion') is-invalid @enderror"
                                       wire:model="ocupacion" placeholder="Ingrese la ocupación">
                                @error('ocupacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ingreso Mensual</label>
                                <input type="number" class="form-control @error('ingreso_mensual') is-invalid @enderror"
                                       wire:model="ingreso_mensual" placeholder="Ingrese el ingreso mensual" step="0.01" min="0">
                                @error('ingreso_mensual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            <div class="col-md-12 mb-3">
                                <h5 class="mt-4"><i class="ri ri-tools-line me-2"></i>Otras Informaciones</h5>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="posee_habilidad_productiva" wire:model.live="posee_habilidad_productiva">
                                    <label class="form-check-label" for="posee_habilidad_productiva">¿Posee alguna habilidad productiva?</label>
                                </div>
                            </div>

                            @if($this->posee_habilidad_productiva)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">¿Cuál es la habilidad productiva?</label>
                                <input type="text" class="form-control @error('habilidad_productiva') is-invalid @enderror"
                                       wire:model="habilidad_productiva" placeholder="Ingrese la habilidad productiva">
                                @error('habilidad_productiva')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="pertenece_organizacion_social" wire:model.live="pertenece_organizacion_social">
                                    <label class="form-check-label" for="pertenece_organizacion_social">¿Pertenece a alguna organización social?</label>
                                </div>
                            </div>

                            @if($pertenece_organizacion_social)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo de Organización Social</label>
                                <select class="form-select @error('tipo_organizacion_social') is-invalid @enderror" wire:model="tipo_organizacion_social">
                                    <option value="">Seleccione...</option>
                                    <option value="JPSUV/PSUV">JPSUV/PSUV</option>
                                    <option value="Consejo comunal">Consejo comunal</option>
                                    <option value="Comuna">Comuna</option>
                                    <option value="CLAP">CLAP</option>
                                    <option value="Somos Venezuela">Somos Venezuela</option>
                                    <option value="Movimiento Social">Movimiento Social</option>
                                    <option value="Otros">Otros</option>
                                </select>
                                @error('tipo_organizacion_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if($tipo_organizacion_social == 'Otros')
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Especifique Otra Organización</label>
                                <input type="text" class="form-control @error('otra_organizacion_social') is-invalid @enderror"
                                       wire:model="otra_organizacion_social" placeholder="Ingrese otra organización">
                                @error('otra_organizacion_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                            @endif

                            <div class="col-md-12 mb-3">
                                <label class="form-label">¿Percibe alguna asignación económica del sistema patria?</label>

                                <!-- Cart-like interface -->
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Opciones Disponibles</h6>
                                                <input type="text" class="form-control mt-2" wire:model.live="searchAsignacion" placeholder="Buscar asignaciones...">
                                            </div>
                                            <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                                @forelse($optionsNotInCart as $option)
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span>{{ $option }}</span>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addToCart('{{ $option }}')">
                                                            <i class="ri ri-add-line"></i>
                                                        </button>
                                                    </div>
                                                @empty
                                                    <p class="text-muted text-center mb-0">No hay más opciones disponibles</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Asignaciones Seleccionadas</h6>
                                            </div>
                                            <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                                @forelse($cartItems as $item)
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span>{{ $item }}</span>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeFromCart('{{ $item }}')">
                                                            <i class="ri ri-delete-bin-line"></i>
                                                        </button>
                                                    </div>
                                                @empty
                                                    <p class="text-muted text-center mb-0">No hay asignaciones seleccionadas</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Step 4: Datos de Salud -->
                        @if($currentStep == 4)
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h5><i class="ri ri-heart-pulse-line me-2"></i>Datos de Salud</h5>
                                <p class="text-muted">Información sobre estado de salud y condiciones médicas</p>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="tiene_evaluacion_antropometrica" wire:model.live="tiene_evaluacion_antropometrica">
                                    <label class="form-check-label" for="tiene_evaluacion_antropometrica">¿Tiene evaluación antropométrica?</label>
                                </div>
                            </div>

                            @if($tiene_evaluacion_antropometrica)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">¿Quién la realizó?</label>
                                <input type="text" class="form-control @error('evaluacion_realizada_por') is-invalid @enderror"
                                       wire:model="evaluacion_realizada_por" placeholder="Ingrese quién realizó la evaluación">
                                @error('evaluacion_realizada_por')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Condición de Ingreso</label>
                                <select class="form-select @error('condicion_ingreso') is-invalid @enderror" wire:model="condicion_ingreso">
                                    <option value="">Seleccione...</option>
                                    <option value="Persona en situación de calle">Persona en situación de calle</option>
                                    <option value="Mujer embarazada">Mujer embarazada</option>
                                    <option value="Desnutrición">Desnutrición</option>
                                    <option value="Adulto mayor sin recursos">Adulto mayor sin recursos</option>
                                    <option value="Incapacitado para trabajar">Incapacitado para trabajar</option>
                                    <option value="Persona discapacitada">Persona discapacitada</option>
                                    <option value="Estudiante sin programa de estudio">Estudiante sin programa de estudio</option>
                                    <option value="Desempleado">Desempleado</option>
                                </select>
                                @error('condicion_ingreso')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Ingreso</label>
                                <input type="date" class="form-control @error('fecha_ingreso') is-invalid @enderror"
                                       wire:model="fecha_ingreso">
                                @error('fecha_ingreso')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="padece_discapacidad_enfermedad" wire:model.live="padece_discapacidad_enfermedad">
                                    <label class="form-check-label" for="padece_discapacidad_enfermedad">¿Padece de alguna discapacidad o enfermedad?</label>
                                </div>
                            </div>

                            @if($padece_discapacidad_enfermedad)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Diagnóstico</label>
                                <textarea class="form-control @error('diagnostico') is-invalid @enderror"
                                          wire:model="diagnostico"
                                          rows="3"
                                          placeholder="Ingrese el diagnóstico médico"></textarea>
                                @error('diagnostico')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Recipe o ayuda técnica que requiere</label>
                                <textarea class="form-control @error('recipe_ayuda_tecnica') is-invalid @enderror"
                                          wire:model="recipe_ayuda_tecnica"
                                          rows="3"
                                          placeholder="Ingrese el recipe o ayuda técnica requerida"></textarea>
                                @error('recipe_ayuda_tecnica')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <!-- Step 5: Datos Socio-Familiares -->
                        @if($currentStep == 5)
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h5><i class="ri ri-group-line me-2"></i>Datos Socio-Familiares</h5>
                                <p class="text-muted">Información sobre núcleo familiar y situación socio-familiar</p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">¿Cuántas personas conforman su núcleo familiar?</label>
                                <input type="number" class="form-control @error('personas_nucleo_familiar') is-invalid @enderror"
                                       wire:model.live="personas_nucleo_familiar"
                                       min="0"
                                       placeholder="Ingrese el número total de personas">
                                @error('personas_nucleo_familiar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if($personas_nucleo_familiar > 0)
                            <div class="col-md-12 mb-3">
                                <h6 class="mb-3">Distribución del núcleo familiar:</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Niños y/o niñas (0-12 años)</label>
                                        <input type="number" class="form-control @error('ninos_niñas') is-invalid @enderror"
                                               wire:model.live="ninos_niñas"
                                               min="0"
                                               placeholder="Cantidad">
                                        @error('ninos_niñas')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Adolescentes (13-17 años)</label>
                                        <input type="number" class="form-control @error('adolescentes') is-invalid @enderror"
                                               wire:model.live="adolescentes"
                                               min="0"
                                               placeholder="Cantidad">
                                        @error('adolescentes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mujeres (18+ años)</label>
                                        <input type="number" class="form-control @error('mujeres') is-invalid @enderror"
                                               wire:model.live="mujeres"
                                               min="0"
                                               placeholder="Cantidad">
                                        @error('mujeres')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Hombres (18+ años)</label>
                                        <input type="number" class="form-control @error('hombres') is-invalid @enderror"
                                               wire:model.live="hombres"
                                               min="0"
                                               placeholder="Cantidad">
                                        @error('hombres')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Adultos mayores (65+ años)</label>
                                        <input type="number" class="form-control @error('adultos_mayores') is-invalid @enderror"
                                               wire:model.live="adultos_mayores"
                                               min="0"
                                               placeholder="Cantidad">
                                        @error('adultos_mayores')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mujeres embarazadas</label>
                                        <input type="number" class="form-control @error('mujeres_embarazadas') is-invalid @enderror"
                                               wire:model.live="mujeres_embarazadas"
                                               min="0"
                                               placeholder="Cantidad">
                                        @error('mujeres_embarazadas')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Recipe o ayuda técnica que requiere (socio-familiar)</label>
                                <textarea class="form-control @error('recipe_socio_familiar') is-invalid @enderror"
                                          wire:model="recipe_socio_familiar"
                                          rows="3"
                                          placeholder="Ingrese el recipe o ayuda técnica requerida a nivel socio-familiar"></textarea>
                                @error('recipe_socio_familiar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="es_mujer_embarazada" wire:model.live="es_mujer_embarazada">
                                    <label class="form-check-label" for="es_mujer_embarazada">¿Es una mujer embarazada?</label>
                                </div>
                            </div>

                            @if($es_mujer_embarazada)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de última menstruación</label>
                                <input type="date" class="form-control @error('fecha_ultima_menstruacion') is-invalid @enderror"
                                       wire:model="fecha_ultima_menstruacion">
                                @error('fecha_ultima_menstruacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Edad de gestación (semanas)</label>
                                <input type="number" class="form-control @error('edad_gestacion') is-invalid @enderror"
                                       wire:model="edad_gestacion"
                                       min="0"
                                       max="42"
                                       placeholder="Ingrese las semanas de gestación">
                                @error('edad_gestacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                                          wire:model="observaciones"
                                          rows="4"
                                          placeholder="Ingrese observaciones adicionales"></textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <!-- Navigation Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    @if($currentStep > 1)
                                        <button type="button" class="btn btn-secondary" wire:click="prevStep">
                                            <i class="ri ri-arrow-left-line me-1"></i>Anterior
                                        </button>
                                    @else
                                        <div></div> <!-- Empty div to align buttons right when no previous button -->
                                    @endif

                                    @if($currentStep < 5)
                                        <button type="button" class="btn btn-primary" wire:click="nextStep">
                                            Siguiente<i class="ri ri-arrow-right-line ms-1"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-success">
                                            <i class="ri ri-save-line me-1"></i>Guardar Beneficiario
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
