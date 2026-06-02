<div>
    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">Detalle del Responsable</h5>
                    <p class="mb-0">Información detallada del responsable</p>
                </div>
                <div>
                    <a href="{{ route('admin.responsables.index') }}" class="btn btn-label-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Volver
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre Completo:</label>
                        <p class="mb-0">{{ $responsable->nombre_completo }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cédula:</label>
                        <p class="mb-0">{{ $responsable->cedula }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado:</label>
                        <p class="mb-0">{{ $responsable->estado ? $responsable->estado->nombre : 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Municipio:</label>
                        <p class="mb-0">{{ $responsable->municipio ? $responsable->municipio->nombre : 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Parroquia:</label>
                        <p class="mb-0">{{ $responsable->parroquia ? $responsable->parroquia->nombre : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Teléfono:</label>
                        <p class="mb-0">{{ $responsable->telefono ?: 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha de Levantamiento:</label>
                        <p class="mb-0">{{ $responsable->fecha_levantamiento ? $responsable->fecha_levantamiento->format('d/m/Y') : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dirección:</label>
                        <p class="mb-0">{{ $responsable->direccion ?: 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Punto de Referencia:</label>
                        <p class="mb-0">{{ $responsable->punto_referencia ?: 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Código de Casa de Alimentación:</label>
                        <p class="mb-0">{{ $responsable->codigo_casa_alimentacion ?: 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Empresa:</label>
                        <p class="mb-0">{{ $responsable->empresa ? $responsable->empresa->razon_social : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sucursal:</label>
                        <p class="mb-0">{{ $responsable->sucursal ? $responsable->sucursal->nombre : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.responsables.index') }}" class="btn btn-label-secondary">
                    <i class="ri ri-arrow-left-line me-1"></i> Volver
                </a>
                @can('update responsables')
                <a href="{{ route('admin.responsables.edit', $responsable) }}" class="btn btn-warning">
                    <i class="ri ri-edit-line me-1"></i> Editar
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>