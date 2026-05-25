<div>
    <!-- Resumen Financiero -->
    <div class="row g-3 mb-4">
        <!-- Ingresos VES -->
        <div class="col-sm-6 col-xl-4">
            <div class="card h-100">
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
                        <h3 class="card-title mb-2 fw-bold text-success">Bs. {{ number_format($totales['ingresos_ves'], 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Egresos VES -->
        <div class="col-sm-6 col-xl-4">
            <div class="card h-100">
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
                        <h3 class="card-title mb-2 fw-bold text-danger">Bs. {{ number_format($totales['egresos_ves'], 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance VES -->
        <div class="col-sm-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded {{ $totales['balance_ves'] >= 0 ? 'bg-label-primary' : 'bg-label-warning' }}">
                                <i class="ri ri-bank-card-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="fw-semibold d-block mb-1 text-muted">Balance (VES)</span>
                        <h3 class="card-title mb-2 fw-bold {{ $totales['balance_ves'] >= 0 ? 'text-primary' : 'text-warning' }}">
                            Bs. {{ number_format($totales['balance_ves'], 2, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Búsqueda -->
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                           placeholder="Descripción, referencia...">
                </div>

                <!-- Tipo de transacción -->
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" wire:model.live="tipoFiltro">
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

                <!-- Método de pago -->
                <div class="col-md-2">
                    <label class="form-label">Método de Pago</label>
                    <select class="form-select" wire:model.live="metodoPagoFiltro">
                        <option value="">Todos</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="punto_venta">Punto de Venta</option>
                        <option value="cheque">Cheque</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                <!-- Fecha inicio -->
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" wire:model.live="fechaInicio">
                </div>

                <!-- Fecha fin -->
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" wire:model.live="fechaFin">
                </div>

                <!-- Botón resetear filtros -->
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-label-secondary w-100" wire:click="resetFilters">
                        <i class="ri ri-refresh-line"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de transacciones -->
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Monto VES</th>
                        <th>Monto USD</th>
                        <th>Método</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transacciones as $transaccion)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaccion->fecha)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $transaccion->esIngreso() ? 'bg-success' : 'bg-danger' }}">
                                    {{ $transaccion->tipo_label }}
                                </span>
                            </td>
                            <td>{{ $transaccion->descripcion ?? '-' }}</td>
                            <td class="{{ $transaccion->esIngreso() ? 'text-success' : 'text-danger' }}">
                                {{ $transaccion->esIngreso() ? '+' : '-' }} Bs. {{ number_format($transaccion->monto_ves, 2, ',', '.') }}
                            </td>
                            <td class="{{ $transaccion->esIngreso() ? 'text-success' : 'text-danger' }}">
                                {{ $transaccion->esIngreso() ? '+' : '-' }} ${{ number_format($transaccion->monto_usd, 2, ',', '.') }}
                            </td>
                            <td>{{ $transaccion->metodo_pago_label }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ri ri-more-2-line"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="#" class="dropdown-item">
                                            <i class="ri ri-eye-line me-1"></i> Ver detalles
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <i class="ri ri-file-list-3-line ri-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay transacciones registradas en este período</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="card-footer">
            {{ $transacciones->links('livewire.pagination') }}
        </div>
    </div>
</div>
