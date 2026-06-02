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

        .beneficiario-row { transition: background .12s; }
        .beneficiario-row:hover { background: #f8f9ff; }

        .table-compact th,
        .table-compact td {
            padding: .65rem .8rem;
            vertical-align: middle;
        }

        .avatar-text {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: .9rem;
            font-weight: 700;
        }

        .badge-status {
            font-size: .72em;
            padding: .25em .5em;
            border-radius: .55rem;
        }

        @media (max-width: 991.98px) {
            .table-compact thead {
                display: none;
            }
            .table-compact, .table-compact tbody, .table-compact tr, .table-compact td {
                display: block;
                width: 100%;
            }
            .table-compact tr {
                margin-bottom: 1rem;
                border: 1px solid rgba(0,0,0,.08);
                border-radius: .75rem;
                background: #fff;
                box-shadow: 0 2px 12px rgba(0,0,0,.03);
            }
            .table-compact td {
                padding: .85rem 1rem;
                border: none;
            }
            .table-compact td + td {
                border-top: 1px solid rgba(0,0,0,.06);
            }
            .table-compact td[data-label]::before {
                content: attr(data-label);
                display: block;
                font-size: .7rem;
                color: var(--bs-secondary-color);
                text-transform: uppercase;
                letter-spacing: .04em;
                margin-bottom: .3rem;
            }
            .table-compact td:first-child {
                display: flex;
                align-items: center;
                gap: .75rem;
            }
            .table-compact td.text-end {
                justify-content: flex-end;
            }
            .table-compact td.text-center {
                text-align: left !important;
            }
            .table-compact td .badge {
                white-space: normal;
            }
        }
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

        {{-- Filtros avanzados --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold"><i class="ri ri-search-line me-1"></i>Buscar</label>
                        <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="Nombres, apellidos, cédula...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Estado civil</label>
                        <select class="form-select form-select-sm" wire:model.live="estado_civil">
                            <option value="">Todos</option>
                            <option value="Soltero(a)">Soltero(a)</option>
                            <option value="Casado(a)">Casado(a)</option>
                            <option value="Viudo(a)">Viudo(a)</option>
                            <option value="Divorciado(a)">Divorciado(a)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Nivel instrucción</label>
                        <select class="form-select form-select-sm" wire:model.live="nivel_instruccion">
                            <option value="">Todos</option>
                            <option value="Analfabeto">Analfabeto</option>
                            <option value="Básica">Básica</option>
                            <option value="Media Diversificada">Media Diversificada</option>
                            <option value="TSU">TSU</option>
                            <option value="Universitario">Universitario</option>
                            <option value="Maestría">Maestría</option>
                            <option value="Doctorado">Doctorado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Estudia actualmente</label>
                        <select class="form-select form-select-sm" wire:model.live="estudia_actualmente">
                            <option value="">Todos</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Trabaja actualmente</label>
                        <select class="form-select form-select-sm" wire:model.live="trabaja_actualmente">
                            <option value="">Todos</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small fw-semibold">Mostrar</label>
                        <select class="form-select form-select-sm" wire:model.live="perPage">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Edad mínima</label>
                        <input type="number" class="form-control form-control-sm" wire:model.live="edad_min" placeholder="Min">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Edad máxima</label>
                        <input type="number" class="form-control form-control-sm" wire:model.live="edad_max" placeholder="Max">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Posee habilidad productiva</label>
                        <select class="form-select form-select-sm" wire:model.live="posee_habilidad_productiva">
                            <option value="">Todos</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Pertenece organización social</label>
                        <select class="form-select form-select-sm" wire:model.live="pertenece_organizacion_social">
                            <option value="">Todos</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Fecha nacimiento inicio</label>
                        <input type="date" class="form-control form-control-sm" wire:model.live="fecha_inicio">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Fecha nacimiento fin</label>
                        <input type="date" class="form-control form-control-sm" wire:model.live="fecha_fin">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Asignación económica</label>
                        <select class="form-select form-select-sm" wire:model.live="asignaciones_economicas">
                            <option value="">Todas</option>
                            <option value="Hogares de la patria">Hogares de la patria</option>
                            <option value="Amor Mayor">Amor Mayor</option>
                            <option value="José Gregorio Hernández">José Gregorio Hernández</option>
                            <option value="Parto Humanizado">Parto Humanizado</option>
                            <option value="Pensión por IVSS">Pensión por IVSS</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-label-secondary w-100" wire:click="clearFilters">
                            <i class="ri ri-eraser-line"></i> Limpiar
                        </button>
                        <button type="button" class="btn btn-sm btn-label-info w-100" wire:click="export">
                            <i class="ri ri-download-line"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="mb-0"><i class="ri ri-list-check-3-line me-2 text-primary"></i>Listado de beneficiarios</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" wire:loading.class="opacity-75">
                    <table class="table table-hover table-striped table-sm align-middle mb-0 table-compact">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <a href="#" wire:click.prevent="sortBy('nombres')" class="text-dark text-decoration-none">
                                        Beneficiario
                                        @if ($sortBy === 'nombres')
                                            <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-center text-nowrap">
                                    <a href="#" wire:click.prevent="sortBy('edad')" class="text-dark text-decoration-none">
                                        Edad
                                        @if ($sortBy === 'edad')
                                            <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Teléfono</th>
                                <th class="d-none d-lg-table-cell">Responsable</th>
                                <th class="text-center">Estado</th>
                                <th class="d-none d-md-table-cell">Condición</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($beneficiarios as $beneficiario)
                                <tr wire:key="beneficiario-{{ $beneficiario->id }}" class="beneficiario-row">
                                    <td data-label="Beneficiario">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="avatar-text bg-primary bg-opacity-10 text-primary">{{ strtoupper(substr($beneficiario->nombres, 0, 1)) }}</span>
                                            <div class="text-truncate" style="max-width: 230px;">
                                                <div class="fw-medium text-dark">{{ $beneficiario->nombres }} {{ $beneficiario->apellidos }}</div>
                                                <small class="text-muted d-block">{{ $beneficiario->cedula }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Edad" class="text-center text-nowrap">{{ $beneficiario->edad ?? 'N/A' }}</td>
                                    <td data-label="Teléfono" class="text-truncate" style="max-width: 150px;">
                                        <span class="text-muted">{{ $beneficiario->telefono_principal ?: '-' }}</span>
                                    </td>
                                    <td data-label="Responsable" class="d-none d-lg-table-cell text-truncate" style="max-width: 180px;">
                                        {{ $beneficiario->responsable ? $beneficiario->responsable->nombre_completo : '-' }}
                                    </td>
                                    <td data-label="Estado" class="text-center">
                                        @if($beneficiario->estado)
                                            <span class="badge bg-label-info">{{ $beneficiario->estado->nombre }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td data-label="Condición" class="d-none d-md-table-cell">
                                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                                            @if($beneficiario->estudia_actualmente)
                                                <span class="badge badge-status bg-label-warning text-dark">Estudia</span>
                                            @endif
                                            @if($beneficiario->trabaja_actualmente)
                                                <span class="badge badge-status bg-label-success">Trabaja</span>
                                            @endif
                                            @if($beneficiario->posee_habilidad_productiva)
                                                <span class="badge badge-status bg-label-primary">Habilidad</span>
                                            @endif
                                            @if($beneficiario->pertenece_organizacion_social)
                                                <span class="badge badge-status bg-label-info">Org.</span>
                                            @endif
                                            @if($beneficiario->asignaciones_economicas && count($beneficiario->asignaciones_economicas) > 0)
                                                <span class="badge badge-status bg-label-secondary text-dark">Patria</span>
                                            @endif
                                            @if(!$beneficiario->estudia_actualmente && !$beneficiario->trabaja_actualmente && !$beneficiario->posee_habilidad_productiva && !$beneficiario->pertenece_organizacion_social && !($beneficiario->asignaciones_economicas && count($beneficiario->asignaciones_economicas) > 0))
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Acciones" class="text-end">
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri ri-more-2-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.beneficiarios.show', $beneficiario) }}">
                                                        <i class="ri ri-eye-line me-1"></i> Ver
                                                    </a>
                                                </li>
                                                @can('edit beneficiarios')
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.beneficiarios.edit', $beneficiario) }}">
                                                        <i class="ri ri-pencil-line me-1"></i> Editar
                                                    </a>
                                                </li>
                                                @endcan
                                                @can('delete beneficiarios')
                                                <li>
                                                    <button class="dropdown-item text-danger" type="button" wire:click="$dispatch('confirmDelete', { id: {{ $beneficiario->id }} })">
                                                        <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                    </button>
                                                </li>
                                                @endcan
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <i class="ri ri-user-search-line" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h5 class="mt-3">No se encontraron beneficiarios</h5>
                                            <p class="text-muted">Intenta ajustar tus filtros de búsqueda</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer py-3 border-top-0">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="text-muted">
                            Mostrando {{ $beneficiarios->firstItem() }} a {{ $beneficiarios->lastItem() }} de {{ $beneficiarios->total() }} resultados
                        </div>
                        <div>
                            {{ $beneficiarios->links() }}
                        </div>
                    </div>
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