<div>
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="mb-0"><i class="ri ri-list-check me-2"></i>Crear Tipo de Evaluación</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Ej: Examen Parcial">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="code" class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" id="code" class="form-control @error('code') is-invalid @enderror" wire:model="code" placeholder="Ej: EXAM" maxlength="20">
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="default_weight" class="form-label">Peso por Defecto %</label>
                        <input type="number" id="default_weight" class="form-control @error('default_weight') is-invalid @enderror" wire:model="default_weight" min="0" max="100" step="0.01">
                        @error('default_weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea id="description" class="form-control @error('description') is-invalid @enderror" wire:model="description" rows="3"></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check">
                            <input type="checkbox" id="is_active" class="form-check-input" wire:model="is_active">
                            <label for="is_active" class="form-check-label">Activo</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.evaluation-types.index') }}" class="btn btn-label-secondary">
                        <i class="ri ri-arrow-left-line"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri ri-save-line"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
