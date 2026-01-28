<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Detalles de la Materia: {{ $subject->name }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>Código:</th>
                            <td>{{ $subject->code }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $subject->name }}</td>
                        </tr>
                        <tr>
                            <th>Descripción:</th>
                            <td>{{ $subject->description ?? 'Sin descripción' }}</td>
                        </tr>
                        <tr>
                            <th>Créditos:</th>
                            <td>{{ $subject->credits }}</td>
                        </tr>
                        <tr>
                            <th>Horas por Semana:</th>
                            <td>{{ $subject->hours_per_week }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>Programa:</th>
                            <td>{{ $subject->programa->nombre ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Nivel Educativo:</th>
                            <td>{{ $subject->educationalLevel->nombre ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <span class="badge bg-{{ $subject->is_active ? 'success' : 'danger' }}">
                                    {{ $subject->is_active ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Creado por:</th>
                            <td>{{ $subject->createdBy->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Creación:</th>
                            <td>{{ $subject->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última Actualización:</th>
                            <td>{{ $subject->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($subject->updatedBy)
                            <tr>
                                <th>Actualizado por:</th>
                                <td>{{ $subject->updatedBy->name }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Teachers Section -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <h6 class="mb-3">Profesores Asignados</h6>
                    @if($subject->teachers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nombre del Profesor</th>
                                        <th>Código de Empleado</th>
                                        <th>Especialización</th>
                                        <th>Grado</th>
                                        <th>Fecha de Asignación</th>
                                        <th>Principal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subject->teachers as $teacher)
                                        <tr>
                                            <td>{{ $teacher->user->name ?? '-' }}</td>
                                            <td>{{ $teacher->employee_code }}</td>
                                            <td>{{ $teacher->specialization ?? '-' }}</td>
                                            <td>{{ $teacher->degree ?? '-' }}</td>
                                            <td>{{ $teacher->pivot->assigned_date ? \Carbon\Carbon::parse($teacher->pivot->assigned_date)->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                @if($teacher->pivot->is_primary)
                                                    <span class="badge bg-primary">Principal</span>
                                                @else
                                                    <span class="badge bg-secondary">Auxiliar</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No hay profesores asignados a esta materia.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <div>
                            @can('edit subjects')
                                <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            @endcan
                            @can('assign teachers')
                                <a href="{{ route('admin.subjects.assign-teachers', $subject->id) }}" class="btn btn-info">
                                    <i class="fas fa-chalkboard-teacher"></i> Asignar Profesores
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>