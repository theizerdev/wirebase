<div>
    @php
        $contrato = $sorteo->ganador?->contrato;
        $cliente = $contrato?->cliente;
        $unidad = $contrato?->unidad;
        $badges = [
            'borrador' => 'bg-label-secondary',
            'activo' => 'bg-label-success',
            'completado' => 'bg-label-info',
            'cancelado' => 'bg-label-dark',
            'mora' => 'bg-label-warning',
            'reposicion' => 'bg-label-danger',
        ];
    @endphp

    <div class="row">
        <!-- Header -->
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">
                    <i class="ri ri-trophy-line text-warning me-1"></i>
                    Sorteo #{{ $sorteo->id }}
                    @if($sorteo->estado === 'completado')
                        <span class="badge bg-label-success ms-2">Completado</span>
                    @else
                        <span class="badge bg-label-danger ms-2">Anulado</span>
                    @endif
                </h5>
                <a href="{{ route('admin.sorteo.index') }}" class="btn btn-label-secondary">
                    <i class="ri ri-arrow-left-line me-1"></i> Volver a Sorteos
                </a>
            </div>
        </div>

        <!-- Contrato Ganador Destacado -->
        <div class="col-12 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-4">
                    <h6 class="text-white-50 mb-2">🏆 CONTRATO GANADOR</h6>
                    <h1 class="display-4 fw-bold mb-2" style="letter-spacing: 8px; font-family: monospace;">
                        {{ $sorteo->numero_contrato_ganador }}
                    </h1>
                    @if($cliente)
                        <p class="mb-0 fs-5">{{ $cliente->nombre }} {{ $cliente->apellido }}</p>
                    @endif
                    <p class="text-white-50 mt-2 mb-0">
                        <small>{{ $sorteo->fecha_sorteo->format('d/m/Y h:i A') }} · {{ $sorteo->total_contratos_elegibles }} contratos participaron</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Info del Sorteo -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="ri ri-gift-line me-1"></i> Información del Sorteo</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <span class="fw-bold">Nombre:</span> {{ $sorteo->nombre ?? 'Sin nombre' }}
                        </div>
                        <div>
                            <span class="fw-bold">Fecha:</span> {{ $sorteo->fecha_sorteo->format('d/m/Y h:i:s A') }}
                        </div>
                        <div>
                            <span class="fw-bold">Contratos Elegibles:</span>
                            <span class="badge bg-label-info">{{ $sorteo->total_contratos_elegibles }}</span>
                        </div>
                        <div>
                            <span class="fw-bold">Ejecutado por:</span> {{ $sorteo->ejecutadoPor->name ?? 'Sistema' }}
                        </div>
                        <div>
                            <span class="fw-bold">Empresa:</span> {{ $sorteo->empresa->razon_social ?? 'N/A' }}
                        </div>
                        <div>
                            <span class="fw-bold">Hash de Validación:</span>
                            <br>
                            <code class="text-wrap" style="font-size: 11px; word-break: break-all;">{{ $sorteo->hash_validacion }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info del Cliente -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="ri ri-user-line me-1"></i> Información del Cliente Ganador</h6>
                </div>
                <div class="card-body">
                    @if($cliente)
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <span class="fw-bold">Nombre:</span> {{ $cliente->nombre }} {{ $cliente->apellido }}
                        </div>
                        <div>
                            <span class="fw-bold">Documento:</span> {{ $cliente->tipo_documento ?? 'CI' }} {{ $cliente->documento }}
                        </div>
                        <div>
                            <span class="fw-bold">Teléfono:</span> {{ $cliente->telefono ?? 'N/A' }}
                        </div>
                        <div>
                            <span class="fw-bold">Email:</span> {{ $cliente->email ?? 'N/A' }}
                        </div>
                        <div>
                            <span class="fw-bold">Dirección:</span> {{ $cliente->direccion ?? 'N/A' }}
                        </div>
                    </div>
                    @else
                        <p class="text-muted mb-0">Información del cliente no disponible.</p>
                    @endif
                </div>
            </div>
        </div>

        @if($unidad)
        <!-- Detalle de la Unidad -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="ri ri-motorbike-line me-1"></i> Detalle de la Unidad</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <span class="fw-bold">Modelo:</span> {{ $unidad->moto->titulo ?? 'N/A' }}
                        </div>
                        <div>
                            <span class="fw-bold">Color:</span> {{ $unidad->color_especifico ?? 'N/A' }}
                        </div>
                        <div>
                            <span class="fw-bold">VIN / Chasis:</span> {{ $unidad->vin ?? 'N/A' }}
                        </div>
                        <div>
                            <span class="fw-bold">Motor:</span> {{ $unidad->numero_motor ?? 'N/A' }}
                        </div>
                        <div>
                            <span class="fw-bold">Placa:</span>
                            <span class="badge bg-label-primary">{{ $unidad->placa ?? 'Por Asignar' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estado del Contrato -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="ri ri-file-text-line me-1"></i> Estado del Contrato</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <span class="fw-bold">N° Contrato:</span>
                            <span class="text-primary fw-bold">#{{ $contrato->numero_contrato }}</span>
                        </div>
                        <div>
                            <span class="fw-bold">Estado:</span>
                            <span class="badge {{ $badges[$contrato->estado] ?? 'bg-label-primary' }}">
                                {{ ucfirst($contrato->estado) }}
                            </span>
                        </div>
                        <div>
                            <span class="fw-bold">Fecha Inicio:</span> {{ $contrato->fecha_inicio->format('d/m/Y') }}
                        </div>
                        <div>
                            <span class="fw-bold">Frecuencia:</span> {{ ucfirst($contrato->frecuencia_pago ?? 'mensual') }}
                        </div>
                        <div>
                            <span class="fw-bold">Vendedor:</span> {{ $contrato->vendedor->name ?? 'N/A' }}
                        </div>
                        <div>
                            <span class="fw-bold">Sucursal:</span> {{ $contrato->sucursal->nombre ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($contrato)
        <!-- Resumen Financiero -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="ri ri-money-dollar-circle-line me-1"></i> Resumen Financiero del Contrato</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="d-flex flex-column">
                                <span class="text-muted mb-1">Precio Venta</span>
                                <h4 class="mb-0 text-primary">${{ number_format($contrato->precio_venta_final, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-flex flex-column">
                                <span class="text-muted mb-1">Cuota Inicial</span>
                                <h4 class="mb-0 text-success">${{ number_format($contrato->cuota_inicial, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-flex flex-column">
                                <span class="text-muted mb-1">Monto Financiado</span>
                                <h4 class="mb-0 text-info">${{ number_format($contrato->monto_financiado, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-flex flex-column">
                                <span class="text-muted mb-1">Saldo Pendiente</span>
                                <h4 class="mb-0 text-danger">${{ number_format($contrato->saldo_pendiente, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <span class="fw-bold">Plazo:</span> {{ $contrato->plazo_semanas }} semanas ({{ round($contrato->plazo_semanas / 4, 1) }} meses)
                        </div>
                        <div class="col-md-4">
                            <span class="fw-bold">Tasa Anual:</span> {{ $contrato->tasa_interes_anual }}%
                        </div>
                        <div class="col-md-4">
                            <span class="fw-bold">Progreso:</span> {{ $contrato->cuotas_pagadas }} / {{ $contrato->cuotas_totales }} cuotas
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plan de Pagos -->
        @if($contrato->planPagos && $contrato->planPagos->count() > 0)
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="ri ri-calendar-todo-line me-1"></i> Plan de Pagos</h6>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vencimiento</th>
                                <th>Tipo</th>
                                <th class="text-end">Monto Cuota</th>
                                <th class="text-end">Pagado</th>
                                <th class="text-end">Saldo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contrato->planPagos as $cuota)
                            <tr>
                                <td>{{ $cuota->numero_cuota == 0 ? 'Inicial' : $cuota->numero_cuota }}</td>
                                <td>
                                    {{ $cuota->fecha_vencimiento->format('d/m/Y') }}
                                    @if($cuota->estado == 'pendiente' && $cuota->fecha_vencimiento < now())
                                        <span class="badge bg-label-danger ms-1">Vencido</span>
                                    @endif
                                </td>
                                <td>{{ ucfirst($cuota->tipo_cuota) }}</td>
                                <td class="text-end fw-bold">${{ number_format($cuota->monto_total, 2) }}</td>
                                <td class="text-end text-success">${{ number_format($cuota->monto_pagado, 2) }}</td>
                                <td class="text-end text-danger">${{ number_format($cuota->saldo_pendiente, 2) }}</td>
                                <td>
                                    @php
                                        $statusClass = match($cuota->estado) {
                                            'pagado' => 'bg-label-success',
                                            'parcial' => 'bg-label-info',
                                            'pendiente' => 'bg-label-secondary',
                                            'vencido' => 'bg-label-danger',
                                            default => 'bg-label-primary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($cuota->estado) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        @endif

        <!-- Auditoría -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="ri ri-shield-check-line me-1"></i> Auditoría del Sorteo</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Acción</th>
                                <th>Detalle</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sorteo->auditorias as $auditoria)
                            <tr>
                                <td>
                                    {{ $auditoria->created_at->format('d/m/Y h:i:s A') }}
                                </td>
                                <td>
                                    <span class="badge bg-label-primary">{{ $auditoria->accion }}</span>
                                </td>
                                <td>
                                    @if($auditoria->detalle)
                                        <small>
                                            @foreach($auditoria->detalle as $key => $value)
                                                <span class="d-block"><strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</span>
                                            @endforeach
                                        </small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $auditoria->ip_address ?? '—' }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3">No hay registros de auditoría</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
