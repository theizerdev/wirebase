<div>
    @section('title', 'Editar Municipio')

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.municipios.index') }}">Municipios</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>

        <div class="row gy-3">
            <div class="col-md-8">
                <div class="card">
                    <h5 class="card-header">Editar Municipio</h5>
                    <div class="card-body">
                        <form wire:submit="update">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="nombre" placeholder="Nombre del municipio">
                                    @error('nombre') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Código</label>
                                    <input type="text" class="form-control" wire:model="codigo" placeholder="Código opcional">
                                    @error('codigo') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model="estado_id">
                                        <option value="">Seleccione un estado</option>
                                        @foreach(\App\Models\Estado::where('activo', true)->get() as $estado)
                                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('estado_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Estado</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="activo" wire:model="activo">
                                        <label class="form-check-label" for="activo">Activo</label>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ri ri-save-line me-1"></i> Actualizar
                                </button>
                                <a href="{{ route('admin.municipios.index') }}" class="btn btn-label-secondary">
                                    <i class="ri ri-arrow-go-back-line me-1"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <h5 class="card-header">Información</h5>
                    <div class="card-body">
                        <div class="info-list">
                            <p class="mb-3">
                                <i class="ri ri-information-line text-info me-2"></i>
                                <span class="fw-medium">Municipio: {{ $municipio->nombre }}</span>
                            </p>
                            <p class="small text-muted">
                                <i class="ri ri-map-pin-line me-1"></i>
                                <strong>Estado:</strong> {{ $municipio->estado->nombre ?? 'N/A' }}
                            </p>
                            <p class="small text-muted">
                                <i class="ri ri-profile-line me-1"></i>
                                <strong>Estado actual:</strong> 
                                <span class="badge bg-{{ $municipio->activo ? 'success' : 'danger' }}">
                                    {{ $municipio->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>