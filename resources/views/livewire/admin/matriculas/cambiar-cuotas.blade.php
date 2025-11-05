<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Cambiar Montos de Cuotas</h4>
            <p class="text-muted mb-0">Actualizar montos de cuotas mensuales pendientes</p>
        </div>
        <div>
            <a href="{{ route('admin.matriculas.index') }}" class="btn btn-secondary">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">Cuotas Mensuales Pendientes</h5>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ri ri-search-line"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar estudiante...">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(count($this->selectedSchedules) > 0)
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <span>{{ count($this->selectedSchedules) }} cuotas seleccionadas</span>
                    <button wire:click="openModal" class="btn btn-sm btn-primary">
                        <i class="ri ri-edit-line me-1"></i> Cambiar Montos
                    </button>
                </div>
            @endif

            <div class="d-flex gap-2 mb-3">
                <button wire:click="selectAll" class="btn btn-sm btn-outline-primary">
                    <i class="ri ri-checkbox-multiple-line me-1"></i> Seleccionar Todo
                </button>
                <button wire:click="deselectAll" class="btn btn-sm btn-outline-secondary">
                    <i class="ri ri-checkbox-blank-line me-1"></i> Deseleccionar Todo
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input"
                                       @if(count($this->selectedSchedules) > 0) checked @endif
                                       wire:click="@if(count($this->selectedSchedules) > 0) deselectAll @else selectAll @endif">
                            </th>
                            <th>Estudiante</th>
                            <th>Cuota</th>
                            <th>Monto Actual</th>
                            <th>Fecha Vencimiento</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input"
                                           wire:click="selectSchedule({{ $schedule->id }})"
                                           @if(in_array($schedule->id, $this->selectedSchedules)) checked @endif>
                                </td>
                                <td>
                                    @if($schedule->matricula && $schedule->matricula->student)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <div class="avatar-initial bg-primary bg-opacity-10 text-primary rounded">
                                                    {{ substr($schedule->matricula->student->nombres, 0, 1) }}{{ substr($schedule->matricula->student->apellidos, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $schedule->matricula->student->nombres }} {{ $schedule->matricula->student->apellidos }}</h6>
                                                <small class="text-muted">{{ $schedule->matricula->student->codigo }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-muted">
                                            <i class="ri ri-user-line me-1"></i> Estudiante no disponible
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info">
                                        Cuota #{{ $schedule->numero_cuota }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">${{ number_format($schedule->monto, 2) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="ri ri-calendar-line text-muted me-1"></i>
                                        {{ $schedule->fecha_vencimiento->format('d/m/Y') }}
                                        @if($schedule->fecha_vencimiento < now())
                                            <span class="badge bg-danger ms-2">Vencida</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                        {{ ucfirst($schedule->estado) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ri ri-file-list-line ri-48px text-muted mb-2"></i>
                                        <h6 class="text-muted">No hay cuotas pendientes</h6>
                                        <small class="text-muted">Todas las cuotas han sido pagadas o no hay matrículas activas</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $schedules->links() }}
        </div>
    </div>

    <!-- Modal para cambiar montos -->
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Monto de Cuotas</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <form wire:submit="cambiarMontos">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="ri ri-information-line me-2"></i>
                            Se actualizarán <strong>{{ count($this->selectedSchedules) }}</strong> cuotas mensuales.
                            <br><small>Nota: La cuota inicial (cuota 0) no será modificada.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nuevo Monto ($) *</label>
                            <input type="number" step="0.01" wire:model="nuevo_monto"
                                   class="form-control @error('nuevo_monto') is-invalid @enderror"
                                   placeholder="Ingrese el nuevo monto" required>
                            @error('nuevo_monto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri ri-save-line me-1"></i> Actualizar Montos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
