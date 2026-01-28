<div>
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Crear Nueva Materia</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" id="code" class="form-control @error('code') is-invalid @enderror" 
                                   wire:model="code" placeholder="Ej: MAT-101">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   wire:model="name" placeholder="Ej: Matemáticas I">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="program_id" class="form-label">Programa <span class="text-danger">*</span></label>
                            <select id="program_id" class="form-select @error('program_id') is-invalid @enderror" wire:model="program_id">
                                <option value="">Seleccione un programa</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->nombre }}</option>
                                @endforeach
                            </select>
                            @error('program_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="educational_level_id" class="form-label">Nivel Educativo <span class="text-danger">*</span></label>
                            <select id="educational_level_id" class="form-select @error('educational_level_id') is-invalid @enderror" wire:model="educational_level_id">
                                <option value="">Seleccione un nivel</option>
                                @foreach($educationalLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->nombre }}</option>
                                @endforeach
                            </select>
                            @error('educational_level_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="credits" class="form-label">Créditos <span class="text-danger">*</span></label>
                            <input type="number" id="credits" class="form-control @error('credits') is-invalid @enderror" 
                                   wire:model="credits" min="0" placeholder="Ej: 3">
                            @error('credits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="hours_per_week" class="form-label">Horas por Semana <span class="text-danger">*</span></label>
                            <input type="number" id="hours_per_week" class="form-control @error('hours_per_week') is-invalid @enderror" 
                                   wire:model="hours_per_week" min="0" placeholder="Ej: 4">
                            @error('hours_per_week')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" 
                                      wire:model="description" rows="3" placeholder="Descripción de la materia (opcional)"></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" id="is_active" class="form-check-input" wire:model="is_active">
                                <label for="is_active" class="form-check-label">
                                    Materia Activa
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Materia
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>