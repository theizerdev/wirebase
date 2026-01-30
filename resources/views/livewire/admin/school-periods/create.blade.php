<div class="card">
    <div class="card-header border-bottom">
        <h5 class="mb-0"><i class="ri ri-calendar-line me-2"></i>Crear Período Escolar</h5>
    </div>
    <div class="card-body">
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form wire:submit.prevent="store">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           wire:model="name" placeholder="Ingrese el nombre del período">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              wire:model="description" rows="3" placeholder="Ingrese una descripción"></textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                           wire:model="start_date">
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha de Fin <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                           wire:model="end_date">
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror"
                               id="is_active" wire:model="is_active">
                        <label class="form-check-label" for="is_active">¿Está activo?</label>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.school-periods.index') }}" class="btn btn-label-secondary">
                    <i class="ri ri-arrow-left-line"></i> Volver
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri ri-save-line"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
