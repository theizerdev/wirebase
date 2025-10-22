<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Detalles del Nivel Educativo</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Nombre:</div>
                <div class="col-md-8">{{ $nivel->nombre }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Costo:</div>
                <div class="col-md-8">${{ number_format($nivel->costo, 2) }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Cuotas:</div>
                <div class="col-md-8">{{ $nivel->cuotas }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Creado:</div>
                <div class="col-md-8">{{ $nivel->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="row">
                <div class="col-md-4 fw-bold">Actualizado:</div>
                <div class="col-md-8">{{ $nivel->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('admin.niveles-educativos.index') }}" class="btn btn-secondary">
                Volver
            </a>
        </div>
    </div>
</div>
