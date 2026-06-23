<div>
    @section('title', 'Actividades')

    @push('styles')
    <style>
        .actividad-hero { background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); color:#fff; border-radius:.75rem; padding:1.4rem 1.6rem; }
        .actividad-hero h2 { color:#fff; margin:0; }
        .actividad-hero p { opacity:.9; margin:0; }
        .actividad-row { transition:background .12s; }
        .actividad-row:hover { background:#f8f9ff; }
    </style>
    @endpush

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Actividades</li>
            </ol>
        </nav>

        {{-- Hero + acciones --}}
        <div class="actividad-hero mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="fw-semibold"><i class="ri ri-calendar-todo-line me-2"></i>Actividades</h2>
                <p class="mt-1">Gestión de actividades del sistema</p>
            </div>
            <button type="button" wire:click="create" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#actividadModal">
                <i class="ri ri-add-line me-1"></i>Nueva Actividad
            </button>
        </div>

        {{-- Filtros compactos --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold"><i class="ri ri-search-line me-1"></i>Buscar</label>
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="Nombre, tipo o lugar...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Mostrar</label>
                        <select class="form-select form-select-sm" wire:model.live="perPage">
                            <option value="10">10 por página</option>
                            <option value="25">25 por página</option>
                            <option value="50">50 por página</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="mb-0"><i class="ri ri-calendar-todo-line me-2 text-primary"></i>Listado de actividades</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                                    Actividad @if($sortBy === 'nombre') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th wire:click="sortBy('fecha_inicio')" style="cursor: pointer;">
                                    Fechas @if($sortBy === 'fecha_inicio') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Lugar</th>
                                <th>Coordinador</th>
                                <th wire:click="sortBy('estado')" style="cursor: pointer;">
                                    Estado @if($sortBy === 'estado') <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i> @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($actividades as $actividad)
                                <tr class="actividad-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">{{ substr($actividad->nombre, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $actividad->nombre }}</h6>
                                                <small class="text-muted">{{ $actividad->tipo_actividad }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div><strong>Inicio:</strong> {{ $actividad->fecha_inicio ? $actividad->fecha_inicio->format('d/m/Y H:i') : '' }}</div>
                                        <div><strong>Fin:</strong> {{ $actividad->fecha_fin ? $actividad->fecha_fin->format('d/m/Y H:i') : '' }}</div>
                                    </td>
                                    <td>
                                        {{ $actividad->lugar }}
                                    </td>
                                    <td>
                                        {{ $actividad->coordinador ? $actividad->coordinador->nombres . ' ' . $actividad->coordinador->apellidos : 'Sin asignar' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-label-{{ $actividad->estado == 'Activo' ? 'success' : ($actividad->estado == 'Suspendido' ? 'danger' : 'secondary') }}">
                                            {{ $actividad->estado }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ri ri-more-2-line"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{ route('actividades.asistencia', ['actividad_id' => $actividad->id]) }}" class="dropdown-item">
                                                    <i class="ri ri-user-follow-line me-1"></i> Asistencia
                                                </a>
                                                <button type="button" class="dropdown-item" wire:click="edit({{ $actividad->id }})" data-bs-toggle="modal" data-bs-target="#actividadModal">
                                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                                </button>
                                                <button type="button" class="dropdown-item text-danger"
                                                        wire:click="deleteActividad({{ $actividad->id }})"
                                                        wire:confirm="¿Estás seguro de eliminar esta actividad?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="ri ri-calendar-todo-line" style="font-size:1.5rem;opacity:.3;"></i>
                                        <p class="mb-0 mt-1 small">No se encontraron actividades</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $actividades->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Crear/Editar -->
    <div wire:ignore.self class="modal fade" id="actividadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">{{ $actividadId ? 'Editar Actividad' : 'Nueva Actividad' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="nombre" class="form-control" wire:model="nombre" placeholder="Nombre de la actividad">
                                <label for="nombre">Nombre</label>
                            </div>
                            @error('nombre') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="tipo_actividad" class="form-control" wire:model="tipo_actividad" placeholder="Tipo de actividad">
                                <label for="tipo_actividad">Tipo de Actividad</label>
                            </div>
                            @error('tipo_actividad') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="datetime-local" id="fecha_inicio" class="form-control" wire:model="fecha_inicio">
                                <label for="fecha_inicio">Fecha de Inicio</label>
                            </div>
                            @error('fecha_inicio') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="datetime-local" id="fecha_fin" class="form-control" wire:model="fecha_fin">
                                <label for="fecha_fin">Fecha de Fin</label>
                            </div>
                            @error('fecha_fin') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <select id="estado" class="form-select" wire:model="estado">
                                    <option value="Activo">Activo</option>
                                    <option value="Finalizado">Finalizado</option>
                                    <option value="Suspendido">Suspendido</option>
                                </select>
                                <label for="estado">Estado</label>
                            </div>
                            @error('estado') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <select id="coordinador_id" class="form-select" wire:model="coordinador_id">
                                    <option value="">-- Seleccionar Pastor --</option>
                                    @foreach($pastores as $pastor)
                                        <option value="{{ $pastor->id }}">{{ $pastor->nombres }} {{ $pastor->apellidos }}</option>
                                    @endforeach
                                </select>
                                <label for="coordinador_id">Coordinador</label>
                            </div>
                            @error('coordinador_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="number" id="zona" class="form-control" wire:model="zona" placeholder="Número de zona">
                                <label for="zona">Zona (Número)</label>
                            </div>
                            @error('zona') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="number" id="distrito" class="form-control" wire:model="distrito" placeholder="Número de distrito">
                                <label for="distrito">Distrito (Número)</label>
                            </div>
                            @error('distrito') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="lugar" class="form-control" wire:model="lugar" placeholder="Lugar del evento">
                                <label for="lugar">Lugar</label>
                            </div>
                            @error('lugar') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <textarea id="nota" class="form-control" style="height: 100px" wire:model="nota" placeholder="Notas adicionales"></textarea>
                                <label for="nota">Nota</label>
                            </div>
                            @error('nota') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" wire:click="closeModal">Cancelar</button>
                    <button type="button" class="btn btn-primary" wire:click="{{ $actividadId ? 'update' : 'store' }}">
                        {{ $actividadId ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.addEventListener('close-modal', event => {
            $('#actividadModal').modal('hide');
        });
        window.addEventListener('open-modal', event => {
            $('#actividadModal').modal('show');
        });
    </script>
    @endpush
</div>
