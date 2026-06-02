<div>
    @section('title', 'Detalles del Beneficiario')

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.beneficiarios.index') }}">Beneficiarios</a></li>
                <li class="breadcrumb-item active">Detalles</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-2 mb-md-0">Detalles del Beneficiario</h5>
                            <p class="mb-0 text-muted">{{ $beneficiario->nombres }} {{ $beneficiario->apellidos }}</p>
                        </div>
                        <div class="mt-2 mt-md-0">
                            @can('edit beneficiarios')
                            <a href="{{ route('admin.beneficiarios.edit', $beneficiario) }}" class="btn btn-primary">
                                <i class="ri ri-edit-line me-1"></i>Editar
                            </a>
                            @endcan
                            <a href="{{ route('admin.beneficiarios.index') }}" class="btn btn-outline-secondary ms-2">
                                <i class="ri ri-arrow-left-line me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted text-uppercase mb-3">Información Personal</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Nombres</label>
                                <p class="mb-0">{{ $beneficiario->nombres }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Apellidos</label>
                                <p class="mb-0">{{ $beneficiario->apellidos }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Cédula</label>
                                <p class="mb-0">{{ $beneficiario->cedula }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Fecha de Nacimiento</label>
                                <p class="mb-0">{{ $beneficiario->fecha_nacimiento ? $beneficiario->fecha_nacimiento->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Edad</label>
                                <p class="mb-0">{{ $beneficiario->edad ?? 'N/A' }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Teléfono Principal</label>
                                <p class="mb-0">{{ $beneficiario->telefono_principal ?: 'N/A' }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Teléfono Alternativo</label>
                                <p class="mb-0">{{ $beneficiario->telefono_alternativo ?: 'N/A' }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Estado Civil</label>
                                <p class="mb-0">{{ $beneficiario->estado_civil }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Responsable Asociado</label>
                                <p class="mb-0">{{ $beneficiario->responsable ? $beneficiario->responsable->nombre_completo : 'N/A' }}</p>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-muted">Estado</label>
                                <p class="mb-0">{{ $beneficiario->estado ? $beneficiario->estado->nombre : 'N/A' }}</p>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-muted">Municipio</label>
                                <p class="mb-0">{{ $beneficiario->municipio ? $beneficiario->municipio->nombre : 'N/A' }}</p>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-muted">Parroquia</label>
                                <p class="mb-0">{{ $beneficiario->parroquia ? $beneficiario->parroquia->nombre : 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted text-uppercase mb-3">Educación</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">¿Estudia Actualmente?</label>
                                <p class="mb-0">{{ $beneficiario->estudia_actualmente ? 'Sí' : 'No' }}</p>
                            </div>
                            
                            @if($beneficiario->estudia_actualmente)
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Estudio Actual</label>
                                <p class="mb-0">{{ $beneficiario->estudio_actual ?: 'N/A' }}</p>
                            </div>
                            @endif
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Nivel de Instrucción</label>
                                <p class="mb-0">{{ $beneficiario->nivel_instruccion }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Último Título Obtenido</label>
                                <p class="mb-0">{{ $beneficiario->ultimo_titulo_obtenido ?: 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted text-uppercase mb-3">Trabajo</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">¿Trabaja Actualmente?</label>
                                <p class="mb-0">{{ $beneficiario->trabaja_actualmente ? 'Sí' : 'No' }}</p>
                            </div>
                            
                            @if($beneficiario->trabaja_actualmente)
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Lugar de Trabajo</label>
                                <p class="mb-0">{{ $beneficiario->lugar_trabajo ?: 'N/A' }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Ocupación</label>
                                <p class="mb-0">{{ $beneficiario->ocupacion ?: 'N/A' }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Ingreso Mensual</label>
                                <p class="mb-0">{{ $beneficiario->ingreso_mensual ? '$' . number_format($beneficiario->ingreso_mensual, 2) : 'N/A' }}</p>
                            </div>
                            @endif
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted text-uppercase mb-3">Otras Informaciones</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">¿Posee Habilidad Productiva?</label>
                                <p class="mb-0">{{ $beneficiario->posee_habilidad_productiva ? 'Sí' : 'No' }}</p>
                            </div>
                            
                            @if($beneficiario->posee_habilidad_productiva)
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Habilidad Productiva</label>
                                <p class="mb-0">{{ $beneficiario->habilidad_productiva ?: 'N/A' }}</p>
                            </div>
                            @endif
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">¿Pertenece a Organización Social?</label>
                                <p class="mb-0">{{ $beneficiario->pertenece_organizacion_social ? 'Sí' : 'No' }}</p>
                            </div>
                            
                            @if($beneficiario->pertenece_organizacion_social)
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tipo de Organización Social</label>
                                <p class="mb-0">{{ $beneficiario->tipo_organizacion_social ?: 'N/A' }}</p>
                            </div>
                            
                            @if($beneficiario->tipo_organizacion_social == 'Otros')
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Otra Organización</label>
                                <p class="mb-0">{{ $beneficiario->otra_organizacion_social ?: 'N/A' }}</p>
                            </div>
                            @endif
                            @endif
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-muted">Asignaciones Económicas del Sistema Patria</label>
                                <p class="mb-0">
                                    @if($beneficiario->asignaciones_economicas && count($beneficiario->asignaciones_economicas) > 0)
                                        {{ implode(', ', $beneficiario->asignaciones_economicas) }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted text-uppercase mb-3">Información Adicional</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Fecha de Creación</label>
                                <p class="mb-0">{{ $beneficiario->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Última Actualización</label>
                                <p class="mb-0">{{ $beneficiario->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>