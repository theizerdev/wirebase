<div>
    <style>
        .finanzas-hero {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }
    </style>

    {{-- Hero Section --}}
    <div class="finanzas-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h2 class="fw-semibold mb-2">
                <i class="ri ri-money-dollar-circle-line me-2"></i>Finanzas de Iglesias
            </h2>
            <p class="mb-0 opacity-75">Control y registro de transacciones financieras</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" 
                    class="btn btn-light btn-sm"
                    wire:click="exportCsv"
                    title="Exportar a CSV">
                <i class="ri ri-download-line me-1"></i>Exportar
            </button>
            <a href="{{ route('admin.finanzas.create') }}" class="btn btn-light btn-sm">
                <i class="ri ri-add-line me-1"></i>Nueva Transacción
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Transacciones --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-file-list-3-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="fw-semibold d-block mb-1 text-muted">Total Transacciones</span>
                        <h3 class="card-title mb-2 fw-bold">{{ number_format($stats['total_transacciones']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ingresos VES --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-arrow-up-circle-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="fw-semibold d-block mb-1 text-muted">Ingresos (VES)</span>
                        <h3 class="card-title mb-2 fw-bold text-success">Bs. {{ number_format($stats['total_ingresos_ves'], 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Egresos VES --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ri ri-arrow-down-circle-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="fw-semibold d-block mb-1 text-muted">Egresos (VES)</span>
                        <h3 class="card-title mb-2 fw-bold text-danger">Bs. {{ number_format($stats['total_egresos_ves'], 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Balance VES --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded {{ $stats['balance_ves'] >= 0 ? 'bg-label-info' : 'bg-label-warning' }}">
                                <i class="ri ri-bank-card-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="fw-semibold d-block mb-1 text-muted">Balance (VES)</span>
                        <h3 class="card-title mb-2 fw-bold {{ $stats['balance_ves'] >= 0 ? 'text-info' : 'text-warning' }}">
                            Bs. {{ number_format($stats['balance_ves'], 2, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Compact Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row g-3 align-items-end">
                {{-- Búsqueda --}}
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Buscar</label>
                    <input type="text" 
                           class="form-control form-control-sm" 
                           wire:model.live.debounce.300ms="search"
                           placeholder="Descripción, referencia...">
                </div>

                {{-- Extensión --}}
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Extensión</label>
                    <select class="form-select form-select-sm" wire:model.live="iglesiaFilter">
                        <option value="">Todas las extensiones</option>
                        @foreach($iglesias as $iglesia)
                            <option value="{{ $iglesia->id }}">{{ $iglesia->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo de transacción --}}
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Tipo</label>
                    <select class="form-select form-select-sm" wire:model.live="tipoFiltro">
                        <option value="">Todos los tipos</option>
                        <option value="diezmo">Diezmos</option>
                        <option value="ofrenda">Ofrendas</option>
                        <option value="aporte_especial">Aportes Especiales</option>
                        <option value="gasto_operativo">Gastos Operativos</option>
                        <option value="gasto_ministerio">Gastos Ministerio</option>
                        <option value="otro_ingreso">Otros Ingresos</option>
                        <option value="otro_egreso">Otros Egresos</option>
                    </select>
                </div>

                {{-- Método de pago --}}
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Método de Pago</label>
                    <select class="form-select form-select-sm" wire:model.live="metodoPagoFiltro">
                        <option value="">Todos</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="punto_venta">Punto de Venta</option>
                        <option value="cheque">Cheque</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                {{-- Fecha inicio --}}
                <div class="col-md-1">
                    <label class="form-label small text-muted mb-1">Desde</label>
                    <input type="date" 
                           class="form-control form-control-sm" 
                           wire:model.live="fechaInicio">
                </div>

                {{-- Fecha fin --}}
                <div class="col-md-1">
                    <label class="form-label small text-muted mb-1">Hasta</label>
                    <input type="date" 
                           class="form-control form-control-sm" 
                           wire:model.live="fechaFin">
                </div>

                {{-- Botón resetear filtros --}}
                <div class="col-md-1">
                    <button type="button" 
                            class="btn btn-outline-secondary btn-sm w-100" 
                            wire:click="resetFilters"
                            title="Limpiar filtros">
                        <i class="ri ri-refresh-line"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-datatable table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Extensión</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Monto VES</th>
                        <th>Monto USD</th>
                        <th>Método</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaccion)
                        <tr>
                            <td>
                                <span class="fw-medium">
                                    {{ \Carbon\Carbon::parse($transaccion->fecha)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="ri ri-hospital-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="fw-medium">{{ $transaccion->iglesia ? $transaccion->iglesia->nombre : '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $transaccion->esIngreso() ? 'bg-label-success' : 'bg-label-danger' }}">
                                    {{ $transaccion->tipo_label }}
                                </span>
                            </td>
                            <td>
                                <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                    {{ $transaccion->descripcion ?? '-' }}
                                </span>
                            </td>
                            <td class="{{ $transaccion->esIngreso() ? 'text-success' : 'text-danger' }}">
                                <span class="fw-semibold">
                                    {{ $transaccion->esIngreso() ? '+' : '-' }} Bs. {{ number_format($transaccion->monto_bs, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="{{ $transaccion->esIngreso() ? 'text-success' : 'text-danger' }}">
                                <span class="fw-semibold">
                                    {{ $transaccion->esIngreso() ? '+' : '-' }} ${{ number_format($transaccion->monto, 2, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary">
                                    {{ $transaccion->metodo_pago_label }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" 
                                            class="btn p-0 dropdown-toggle hide-arrow" 
                                            data-bs-toggle="dropdown">
                                        <i class="ri ri-more-2-line"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="{{ route('admin.finanzas.show', $transaccion->id) }}" 
                                           class="dropdown-item">
                                            <i class="ri ri-eye-line me-1"></i> Ver detalles
                                        </a>
                                        <a href="{{ route('admin.finanzas.edit', $transaccion->id) }}" 
                                           class="dropdown-item">
                                            <i class="ri ri-edit-line me-1"></i> Editar
                                        </a>
                                        <hr class="dropdown-divider">
                                        <button type="button" 
                                                class="dropdown-item text-danger"
                                                onclick="confirm('¿Está seguro de eliminar esta transacción?') && this.closest('form').submit()">
                                            <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="ri ri-file-list-3-line ri-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No hay transacciones registradas en este período</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
            <div class="card-footer">
                {{ $transactions->links('livewire.pagination') }}
            </div>
        @endif
    </div>
</div>
