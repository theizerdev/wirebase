<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información del Profesor</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="avatar avatar-xl mb-3">
                                <span class="avatar-title rounded-circle bg-primary">
                                    {{ strtoupper(substr($teacher->user->name, 0, 1)) }}
                                </span>
                            </div>
                            <h4>{{ $teacher->user->name }}</h4>
                            <p class="text-muted">{{ $teacher->user->email }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6 text-center">
                                <h5 class="text-primary">{{ $allAssignments->count() }}</h5>
                                <small class="text-muted">Materias Asignadas</small>
                            </div>
                            <div class="col-6 text-center">
                                <h5 class="text-success">{{ $currentAssignments->count() }}</h5>
                                <small class="text-muted">Materias Principales</small>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <strong>Código de Empleado:</strong>
                            <p class="text-muted">{{ $teacher->employee_code }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Especialización:</strong>
                            <p class="text-muted">{{ $teacher->specialization }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Título Académico:</strong>
                            <p class="text-muted">{{ $teacher->degree }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Años de Experiencia:</strong>
                            <p class="text-muted">{{ $teacher->years_experience }} años</p>
                        </div>

                        <div class="mb-3">
                            <strong>Fecha de Contratación:</strong>
                            <p class="text-muted">{{ $teacher->hire_date->format('d/m/Y') }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Estado:</strong>
                            <span class="badge bg-{{ $teacher->is_active ? 'success' : 'danger' }}">
                                {{ $teacher->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>

                        @if($teacher->notes)
                            <div class="mb-3">
                                <strong>Notas:</strong>
                                <p class="text-muted">{{ $teacher->notes }}</p>
                            </div>
                        @endif

                        <div class="mb-3">
                            <strong>Creado por:</strong>
                            <p class="text-muted">{{ $teacher->createdBy->name ?? 'Sistema' }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Actualizado por:</strong>
                            <p class="text-muted">{{ $teacher->updatedBy->name ?? 'Sistema' }}</p>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editar Profesor
                            </a>
                            <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver a la lista
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Materias Asignadas</h3>
                    </div>
                    <div class="card-body">
                        @if($currentAssignments->count() > 0)
                            <h5 class="text-primary mb-3">Materias Principales (Profesor Principal)</h5>
                            <div class="table-responsive mb-4">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Materia</th>
                                            <th>Programa</th>
                                            <th>Nivel Educativo</th>
                                            <th>Período Académico</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($currentAssignments as $subject)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.subjects.show', $subject) }}">
                                                        {{ $subject->name }}
                                                    </a>
                                                </td>
                                                <td>{{ $subject->programa->nombre ?? 'N/A' }}</td>
                                                <td>{{ $subject->educationalLevel->nombre ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $subject->pivot->academic_period ?? 'N/A' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <h5 class="text-muted mb-3">Todas las Asignaciones</h5>
                        @if($allAssignments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Materia</th>
                                            <th>Programa</th>
                                            <th>Nivel Educativo</th>
                                            <th>Período Académico</th>
                                            <th>Rol</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allAssignments as $subject)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.subjects.show', $subject) }}">
                                                        {{ $subject->name }}
                                                    </a>
                                                </td>
                                                <td>{{ $subject->programa->nombre ?? 'N/A' }}</td>
                                                <td>{{ $subject->educationalLevel->nombre ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ $subject->pivot->academic_period ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $subject->pivot->is_primary ? 'success' : 'warning' }}">
                                                        {{ $subject->pivot->is_primary ? 'Principal' : 'Suplente' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                                <p>Este profesor no tiene materias asignadas actualmente.</p>
                                <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-plus"></i> Asignar Materias
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>