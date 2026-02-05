<div>
    <div class="card">
        <div class="card-header border-bottom d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                <i class="ri ri-user-add-line me-2"></i>Asignar Profesores a: {{ $subject->name }}
            </h5>
            <a href="{{ route('admin.subjects.show', $subject->id) }}" class="btn btn-label-secondary">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
        <div class="card-body pt-4">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="academic_period" class="form-label">Período Académico</label>
                        <input type="text" id="academic_period" class="form-control" wire:model="academicPeriod" placeholder="Ej: 2024-2025">
                        @error('academicPeriod') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold mb-3">
                            <i class="ri ri-user-line me-1"></i>Seleccionar Profesores
                        </label>
                        <div class="card-datatable table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:click="toggleSelectAll" id="selectAll">
                                            </div>
                                        </th>
                                        <th>Nombre</th>
                                        <th>Código de Empleado</th>
                                        <th>Especialización</th>
                                        <th style="width: 100px;">Principal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($availableTeachers as $teacher)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input type="checkbox" 
                                                           class="form-check-input"
                                                           wire:model="selectedTeachers" 
                                                           value="{{ $teacher->id }}" 
                                                           id="teacher_{{ $teacher->id }}">
                                                </div>
                                            </td>
                                            <td>
                                                <label for="teacher_{{ $teacher->id }}" class="mb-0 cursor-pointer">
                                                    <i class="ri ri-user-line me-1 text-muted"></i>
                                                    {{ $teacher->user->name ?? 'Sin nombre' }}
                                                </label>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-secondary">{{ $teacher->employee_code }}</span>
                                            </td>
                                            <td>
                                                @if($teacher->specialization)
                                                    <span class="badge bg-label-info">{{ $teacher->specialization }}</span>
                                                @else
                                                    <span class="text-muted">Sin especialización</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input type="radio" 
                                                           class="form-check-input"
                                                           name="primaryTeacher" 
                                                           wire:model="primaryTeacherId" 
                                                           value="{{ $teacher->id }}"
                                                           {{ !in_array($teacher->id, $selectedTeachers) ? 'disabled' : '' }}>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="ri ri-user-line ri-2x text-muted mb-2 d-block"></i>
                                                <span class="text-muted">No hay profesores disponibles</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @error('selectedTeachers') <span class="text-danger d-block mt-2">{{ $message }}</span> @enderror
                        @error('primaryTeacherId') <span class="text-danger d-block mt-2">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.subjects.show', $subject->id) }}" class="btn btn-label-secondary">
                                <i class="ri ri-arrow-left-line me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri ri-save-line me-1"></i> Guardar Asignaciones
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
