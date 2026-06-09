<div>
    @section('title', 'Beneficiarios')

    @push('styles')
    <style>
        .beneficiario-hero { background: linear-gradient(135deg, #10B981 0%, #3B82F6 100%); color:#fff; border-radius:.75rem; padding:1.4rem 1.6rem; }
        .beneficiario-hero h2 { color:#fff; margin:0; }
        .beneficiario-hero p { opacity:.9; margin:0; }

        .stat-card { border:1px solid rgba(0,0,0,.06); border-radius:.65rem; padding:.9rem 1rem;
                     transition:all .2s; display:flex; align-items:center; gap:.85rem; height:100%; background:#fff; }
        .stat-card:hover { box-shadow:0 6px 18px rgba(0,0,0,.07); transform:translateY(-1px); }
        .stat-card .stat-icon { width:44px; height:44px; border-radius:11px; flex:0 0 44px;
                                display:flex; align-items:center; justify-content:center; font-size:1.15rem; }
        .stat-card .stat-value { font-size:1.35rem; font-weight:600; line-height:1; }
        .stat-card .stat-label { font-size:.72rem; color:var(--bs-secondary-color);
                                 text-transform:uppercase; letter-spacing:.4px; font-weight:600; }

        .beneficiario-row { transition:background .12s; }
        .beneficiario-row:hover { background:#f8f9ff; }
    </style>
    @endpush

    <div class="container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri ri-home-line me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Beneficiarios</li>
            </ol>
        </nav>

        {{-- Hero + acciones --}}
        <div class="beneficiario-hero mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="fw-semibold"><i class="ri ri-user-heart-line me-2"></i>Beneficiarios</h2>
                <p class="mt-1">Gestión de beneficiarios del sistema</p>
            </div>
            @can('create beneficiarios')
                <a href="{{ route('admin.beneficiarios.create') }}" class="btn btn-light btn-sm">
                    <i class="ri ri-add-line me-1"></i>Nuevo Beneficiario
                </a>
            @endcan
        </div>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dbeafe;color:#2563eb;"><i class="ri ri-user-heart-line"></i></div>
                    <div>
                        <div class="stat-label">Total</div>
                        <div class="stat-value">{{ $totalBeneficiarios }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="ri ri-user-line"></i></div>
                    <div>
                        <div class="stat-label">Con responsable</div>
                        <div class="stat-value">{{ $totalConResponsable }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="ri ri-graduation-cap-line"></i></div>
                    <div>
                        <div class="stat-label">Estudian</div>
                        <div class="stat-value">{{ $totalEstudian }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#ffe4e6;color:#dc2626;"><i class="ri ri-briefcase-line"></i></div>
                    <div>
                        <div class="stat-label">Trabajan</div>
                        <div class="stat-value">{{ $totalTrabajan }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros compactos --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold"><i class="ri ri-search-line me-1"></i>Buscar</label>
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="Nombres, apellidos, cédula...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Estudia</label>
                        <select class="form-select form-select-sm" wire:model.live="estudia_actualmente">
                            <option value="">Todos</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Trabaja</label>
                        <select class="form-select form-select-sm" wire:model.live="trabaja_actualmente">
                            <option value="">Todos</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Mostrar</label>
                        <select class="form-select form-select-sm" wire:model.live="perPage">
                            <option value="10">10 por página</option>
                            <option value="25">25 por página</option>
                            <option value="50">50 por página</option>
                            <option value="100">100 por página</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-label-secondary w-100" wire:click="clearFilters">
                            <i class="ri ri-eraser-line"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="mb-0"><i class="ri ri-user-heart-line me-2 text-primary"></i>Listado de beneficiarios</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Beneficiario</th>
                                <th>Cédula</th>
                                <th>Edad</th>
                                <th>Teléfono</th>
                                <th>Responsable</th>
                                <th>Condición</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($beneficiarios as $beneficiario)
                                <tr class="beneficiario-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary">{{ substr($beneficiario->nombres, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $beneficiario->nombres }} {{ $beneficiario->apellidos }}</h6>
                                                <small class="text-muted">{{ $beneficiario->telefono_principal ? $beneficiario->telefono_principal : 'Sin teléfono' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $beneficiario->cedula }}</span>
                                    </td>
                                    <td>
                                        {{ $beneficiario->edad ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $beneficiario->telefono_principal ?: '-' }}
                                    </td>
                                    <td>
                                        {{ $beneficiario->responsable ? $beneficiario->responsable->nombre_completo : 'N/A' }}
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if($beneficiario->estudia_actualmente)
                                                <span class="badge bg-label-warning text-dark">Estudia</span>
                                            @endif
                                            @if($beneficiario->trabaja_actualmente)
                                                <span class="badge bg-label-success">Trabaja</span>
                                            @endif
                                            @if(!$beneficiario->estudia_actualmente && !$beneficiario->trabaja_actualmente)
                                                <span class="text-muted text-danger badge bg-label-danger">Desempleado</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ri ri-more-2-line"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @can('view beneficiarios')
                                                <a class="dropdown-item" href="{{ route('admin.beneficiarios.show', $beneficiario) }}">
                                                    <i class="ri ri-eye-line me-1"></i> Ver
                                                </a>
                                                @endcan
                                                @can('edit beneficiarios')
                                                <a class="dropdown-item" href="{{ route('admin.beneficiarios.edit', $beneficiario) }}">
                                                    <i class="ri ri-pencil-line me-1"></i> Editar
                                                </a>
                                                @endcan
                                                @can('delete beneficiarios')
                                                <button type="button" class="dropdown-item text-danger"
                                                        wire:click="$dispatch('confirmDelete', { id: {{ $beneficiario->id }} })">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="ri ri-user-search-line" style="font-size:1.5rem;opacity:.3;"></i>
                                        <p class="mb-0 mt-1 small">No se encontraron beneficiarios</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $beneficiarios->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Livewire.on('confirmDelete', ({ id }) => {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('delete', { id: id });
                    }
                })
            });
        });
    </script>
    @endpush
</div>
