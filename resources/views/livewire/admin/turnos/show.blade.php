<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Detalles del Turno</h2>
        <a href="{{ route('admin.turnos.index') }}" class="btn btn-outline-secondary">
            <i class="ri ri-arrow-left-line me-1"></i> Volver
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Información del Turno</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <p class="form-control-static">{{ $turno->nombre }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Hora de Inicio</label>
                    <p class="form-control-static">{{ $turno->hora_inicio->format('H:i') }}</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Hora de Fin</label>
                    <p class="form-control-static">{{ $turno->hora_fin->format('H:i') }}</p>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('admin.turnos.edit', $turno) }}" class="btn btn-primary me-2">
                    <i class="ri ri-pencil-line me-1"></i> Editar
                </a>
            </div>
        </div>
    </div>
</div>
