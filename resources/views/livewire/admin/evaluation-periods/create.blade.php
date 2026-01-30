<div>
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="mb-0"><i class="ri ri-calendar-line me-2"></i>Crear Nuevo Lapso de Evaluación</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="school_period_id" class="form-label">Período Escolar <span class="text-danger">*</span></label>
                        <select id="school_period_id" class="form-select @error('school_period_id') is-invalid @enderror" wire:model="school_period_id">
                            <option value="">Seleccione...</option>
                            @foreach($schoolPeriods as $sp)
                                <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                            @endforeach
                        </select>
                        @error('school_period_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nombre del Lapso <span class="text-danger">*</span></label>
                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Ej: 1er Lapso">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="number" class="form-label">Número de Lapso <span class="text-danger">*</span></label>
                        <input type="number" id="number" class="form-control @error('number') is-invalid @enderror" wire:model="number" min="1" max="10">
                        @error('number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="start_date" class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                        <input type="date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" wire:model="start_date">
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="end_date" class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                        <input type="date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" wire:model="end_date">
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="weight" class="form-label">Peso % <span class="text-danger">*</span></label>
                        <input type="number" id="weight" class="form-control @error('weight') is-invalid @enderror" wire:model="weight" min="0" max="100" step="0.01">
                        @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8 mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea id="description" class="form-control @error('description') is-invalid @enderror" wire:model="description" rows="2"></textarea>
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
                    <a href="{{ route('admin.evaluation-periods.index') }}" class="btn btn-label-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri ri-save-line me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
