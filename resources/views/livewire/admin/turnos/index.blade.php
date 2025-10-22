<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Turnos</h2>
        <a href="{{ route('admin.turnos.create') }}" class="btn btn-primary">
            <i class="ri ri-add-circle-line me-1"></i> Nuevo Turno
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Turnos</h5>
                <div class="d-flex gap-2">
                    <!-- Búsqueda y paginación -->
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control form-control-sm" placeholder="Buscar..." style="width: 200px;">
                    <select wire:model.live="perPage" class="form-select form-select-sm" style="width: auto;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                Nombre @if($sortField == 'nombre') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th wire:click="sortBy('hora_inicio')" style="cursor: pointer;">
                                Hora Inicio @if($sortField == 'hora_inicio') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th wire:click="sortBy('hora_fin')" style="cursor: pointer;">
                                Hora Fin @if($sortField == 'hora_fin') <i class="ri ri-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}-line"></i> @endif
                            </th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($turnos as $turno)
                            <tr>
                                <td>{{ $turno->nombre }}</td>
                                <td>{{ $turno->hora_inicio->format('H:i') }}</td>
                                <td>{{ $turno->hora_fin->format('H:i') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="actionsDropdown{{ $turno->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="ri ri-more-2-fill ri-24px"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $turno->id }}">
                                            <a class="dropdown-item" href="{{ route('admin.turnos.show', $turno) }}">
                                                <i class="ri ri-eye-line me-1"></i> Ver
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.turnos.edit', $turno) }}">
                                                <i class="ri ri-pencil-line me-1"></i> Editar
                                            </a>
                                            <button class="dropdown-item text-danger" wire:click="delete({{ $turno->id }})" wire:confirm="¿Estás seguro de eliminar este turno?">
                                                <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No se encontraron turnos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="card-footer">
                {{ $turnos->links('vendor.pagination.materialize') }}
            </div>
        </div>
    </div>
</div>
