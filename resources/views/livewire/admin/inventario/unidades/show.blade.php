<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 text-primary">Unidad: {{ $unidad->moto->titulo ?? 'Moto' }}</h4>
                <p class="mb-0">VIN: {{ $unidad->vin }} • Motor: {{ $unidad->numero_motor }} • Placa: {{ $unidad->placa ?? 'N/A' }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.inventario.unidades.index') }}" class="btn btn-label-secondary">
                    <i class="ri ri-arrow-left-line me-1"></i> Volver
                </a>
                <a href="{{ route('admin.inventario.unidades.history', $unidad->id) }}" class="btn btn-label-primary">
                    <i class="ri ri-time-line me-1"></i> Historial
                </a>
                <button class="btn btn-primary" wire:click="loadData">
                    <i class="ri ri-bar-chart-line me-1"></i> Cargar detalles
                </button>
            </div>
        </div>
    </div>

    @if(!$lazyLoaded)
        <div class="alert alert-info">Haz clic en “Cargar detalles” para traer contratos, clientes y pagos (lazy load).</div>
    @else
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $estado = $resumen['estado'];
                                $labelClass = $estado === 'mora' ? 'danger' : ($estado === 'pendiente' ? 'warning' : 'success');
                                $texto = $estado === 'mora' ? 'En Mora' : ($estado === 'pendiente' ? 'Pendiente' : 'Al Día');
                            @endphp
                            <h5 class="mb-1"><span class="badge bg-label-{{ $labelClass }}">{{ $texto }}</span></h5>
                            <small class="text-muted">Estado financiero</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-{{ $labelClass }}">
                                <i class="ri ri-information-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">${{ number_format($resumen['total_pagado'], 2) }}</h5>
                            <small class="text-muted">Total Pagado</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-bank-card-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">${{ number_format($resumen['pendiente'], 2) }}</h5>
                            <small class="text-muted">Saldo Pendiente</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ri ri-time-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $resumen['proximo_pago'] ?? 'N/A' }}</h5>
                            <small class="text-muted">Próximo Vencimiento</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-calendar-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Contratos asociados</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>N° Contrato</th>
                                    <th>Cliente</th>
                                    <th>Inicio</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contratos as $c)
                                    <tr>
                                        <td>{{ $c['numero_contrato'] ?? $c['id'] }}</td>
                                        <td>{{ $c['cliente']['nombre'] ?? '' }} {{ $c['cliente']['apellido'] ?? '' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($c['created_at'])->format('d/m/Y') }}</td>
                                        <td><span class="badge bg-label-{{ ($c['estado'] ?? '') === 'mora' ? 'danger' : 'primary' }}">{{ ucfirst($c['estado'] ?? 'borrador') }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted text-center">Sin contratos para esta unidad</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Pagos vinculados a cuotas</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Concepto</th>
                                    <th class="text-end">Monto</th>
                                    <th>Estado</th>
                                    <th>Método</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pagosCuotas as $p)
                                    <tr>
                                        <td>{{ $p['fecha'] }}</td>
                                        <td>{{ $p['concepto'] }}</td>
                                        <td class="text-end">${{ number_format($p['monto'], 2) }}</td>
                                        <td><span class="badge bg-label-{{ $p['estado'] === 'aprobado' ? 'success' : ($p['estado'] === 'pendiente' ? 'warning' : 'danger') }}">{{ ucfirst($p['estado']) }}</span></td>
                                        <td>{{ ucfirst($p['metodo'] ?? '') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-muted text-center">Sin pagos asociados a cuotas</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Pagos del cliente (aprobados)</h6>
                    <a href="{{ route('admin.pagos.index') }}" class="btn btn-sm btn-label-primary">
                        <i class="ri ri-money-dollar-circle-line me-1"></i> Ver Registro de Pagos
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Documento</th>
                                    <th class="text-end">Total</th>
                                    <th>Método</th>
                                    <th>Ref</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pagosCliente as $pc)
                                    <tr>
                                        <td>{{ $pc['fecha'] }}</td>
                                        <td>{{ $pc['documento'] }}</td>
                                        <td class="text-end">${{ number_format($pc['total'], 2) }}</td>
                                        <td>{{ ucfirst($pc['metodo'] ?? '') }}</td>
                                        <td>{{ $pc['referencia'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-muted text-center">Sin pagos del cliente</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
