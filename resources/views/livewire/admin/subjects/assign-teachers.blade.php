<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Asignar Profesores a: {{ $subject->name }}</h5>
        </div>
        <div class="card-body">
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
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="academic_period" class="form-label">Período Académico</label>
                            <input type="text" id="academic_period" class="form-control" wire:model="academicPeriod" placeholder="Ej: 2024-2025">
                            @error('academicPeriod') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Seleccionar Profesores</label>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" wire:click="toggleSelectAll" id="selectAll">
                                            </th>
                                            <th>Nombre</th>
                                            <th>Código de Empleado</th>
                                            <th>Especialización</th>
                                            <th>Principal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($availableTeachers as $teacher)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" 
                                                           wire:model="selectedTeachers" 
                                                           value="{{ $teacher->id }}" 
                                                           id="teacher_{{ $teacher->id }}">
                                                </td>
                                                <td>
                                                    <label for="teacher_{{ $teacher->id }}">
                                                        {{ $teacher->user->name ?? 'Sin nombre' }}
                                                    </label>
                                                </td>
                                                <td>{{ $teacher->employee_code }}</td>
                                                <td>{{ $teacher->specialization ?? 'Sin especialización' }}</td>
                                                <td>
                                                    <input type="radio" 
                                                           name="primaryTeacher" 
                                                           wire:model="primaryTeacherId" 
                                                           value="{{ $teacher->id }}"
                                                           {{ !in_array($teacher->id, $selectedTeachers) ? 'disabled' : '' }}>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No hay profesores disponibles</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @error('selectedTeachers') <span class="text-danger">{{ $message }}</span> @enderror
                            @error('primaryTeacherId') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.subjects.show', $subject->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Asignaciones
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>