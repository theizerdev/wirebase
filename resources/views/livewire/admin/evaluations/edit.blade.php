<div>
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="mb-0"><i class="ri ri-pencil-line me-2"></i>Editar Evaluación: {{ $evaluation->name }}</h5>
        </div>
        <form wire:submit="save">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="subject_id" class="form-label">Materia <span class="text-danger">*</span></label>
                        <select id="subject_id" class="form-select @error('subject_id') is-invalid @enderror" wire:model="subject_id">
                            <option value="">Seleccione...</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @error('subject_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="teacher_id" class="form-label">Profesor <span class="text-danger">*</span></label>
                        <select id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" wire:model="teacher_id">
                            <option value="">Seleccione...</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->user->name ?? $teacher->employee_code }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="evaluation_period_id" class="form-label">Lapso <span class="text-danger">*</span></label>
                        <select id="evaluation_period_id" class="form-select @error('evaluation_period_id') is-invalid @enderror" wire:model="evaluation_period_id">
                            <option value="">Seleccione...</option>
                            @foreach($evaluationPeriods as $period)
                                <option value="{{ $period->id }}">{{ $period->name }}</option>
                            @endforeach
                        </select>
                        @error('evaluation_period_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="evaluation_type_id" class="form-label">Tipo de Evaluación <span class="text-danger">*</span></label>
                        <select id="evaluation_type_id" class="form-select @error('evaluation_type_id') is-invalid @enderror" wire:model="evaluation_type_id">
                            <option value="">Seleccione...</option>
                            @foreach($evaluationTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->code }})</option>
                            @endforeach
                        </select>
                        @error('evaluation_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nombre de la Evaluación <span class="text-danger">*</span></label>
                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="evaluation_date" class="form-label">Fecha <span class="text-danger">*</span></label>
                        <input type="date" id="evaluation_date" class="form-control @error('evaluation_date') is-invalid @enderror" wire:model="evaluation_date">
                        @error('evaluation_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="max_score" class="form-label">Nota Máxima <span class="text-danger">*</span></label>
                        <input type="number" id="max_score" class="form-control @error('max_score') is-invalid @enderror" wire:model="max_score" min="1" max="100" step="0.01">
                        @error('max_score') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="weight" class="form-label">Peso % <span class="text-danger">*</span></label>
                        <input type="number" id="weight" class="form-control @error('weight') is-invalid @enderror" wire:model="weight" min="0" max="100" step="0.01">
                        @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div class="form-check me-3">
                            <input type="checkbox" id="is_active" class="form-check-input" wire:model="is_active">
                            <label for="is_active" class="form-check-label">Activa</label>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" id="is_published" class="form-check-input" wire:model="is_published">
                            <label for="is_published" class="form-check-label">Notas Publicadas</label>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea id="description" class="form-control @error('description') is-invalid @enderror" wire:model="description" rows="2"></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('admin.evaluations.index') }}" class="btn btn-label-secondary">
                    <i class="ri ri-arrow-left-line me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri ri-save-line me-1"></i> Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
