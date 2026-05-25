<div>
    @section('title', 'Editar Estado')

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.estados.index') }}">Estados</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="mb-0"><i class="ri ri-pencil-line me-2 text-primary"></i>Editar Estado</h5>
            </div>
            <div class="card-body">
                <form wire:submit="update">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="nombre" placeholder="Nombre del estado">
                            @error('nombre') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Código</label>
                            <input type="text" class="form-control" wire:model="codigo" placeholder="Código opcional">
                            @error('codigo') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                
                
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri ri-save-line me-1"></i> Actualizar
                        </button>
                        <a href="{{ route('admin.estados.index') }}" class="btn btn-secondary">
                            <i class="ri ri-close-line me-1"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
