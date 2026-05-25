<div>
    @section('title', 'Nuevo Municipio')

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.municipios.index') }}">Municipios</a></li>
                <li class="breadcrumb-item active">Nuevo</li>
            </ol>
        </nav>

        <div class="row gy-3">
            <div class="col-md-8">
                <div class="card">
                    <h5 class="card-header">Agregar Nuevo Municipio</h5>
                    <div class="card-body">
                        <form wire:submit="save">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="nombre" placeholder="Nombre del municipio">
                                    @error('nombre') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model="estado_id">
                                        <option value="">Seleccione un estado</option>
                                        @foreach(\App\Models\Estado::get() as $estado)
                                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('estado_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                              
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ri ri-save-line me-1"></i> Guardar
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
                                <span class="fw-medium">Municipios en Venezuela</span>
                            </p>
                            <p class="small text-muted">
                                Los municipios son subdivisiones administrativas de los estados en Venezuela, 
                                cada uno con su respectiva administración local.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>