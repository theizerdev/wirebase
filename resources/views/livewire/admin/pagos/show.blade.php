<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri ri-check-line me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Pago {{ $pago->numero_completo ?? ($pago->serie . '-' . str_pad($pago->numero, 8, '0', STR_PAD_LEFT)) }}</h5>
                        <small class="text-muted">Fecha: {{ optional($pago->fecha)->format('d/m/Y') }}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-label-{{ $pago->estado === 'aprobado' ? 'success' : ($pago->estado === 'pendiente' ? 'warning' : 'danger') }}">
                            {{ ucfirst($pago->estado) }}
                        </span>
                        <a href="{{ route('admin.pagos.print', $pago->id) }}" target="_blank" class="btn btn-outline-primary">
                            <i class="ri ri-printer-line me-1"></i> Imprimir
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ri ri-user-line ri-20px"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $pago->cliente->nombre_completo ?? 'Cliente' }}</h6>
                                    <small class="text-muted">{{ $pago->cliente->documento ?? '' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="ri ri-money-dollar-circle-line ri-20px"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-1">${{ number_format($pago->total, 2) }}</h6>
                                    <small class="text-muted">{{ number_format($pago->total_bolivares ?? 0, 2) }} Bs</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <span>Subtotal</span>
                                        <strong>${{ number_format($pago->subtotal, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Descuento</span>
                                        <strong>${{ number_format($pago->descuento, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Tasa Cambio</span>
                                        <strong>{{ number_format($pago->tasa_cambio ?? 0, 4) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <span>Método de Pago</span>
                                        <strong>{{ ucfirst($pago->metodo_pago ?? 'N/A') }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Referencia</span>
                                        <strong>{{ $pago->referencia ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Registrado por</span>
                                        <strong>{{ $pago->user->name ?? 'Sistema' }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-2">Detalles</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Concepto</th>
                                    <th class="text-end">Cantidad</th>
                                    <th class="text-end">Precio Unitario</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pago->detalles as $detalle)
                                    <tr>
                                        <td>{{ $detalle->conceptoPago->nombre ?? $detalle->descripcion }}</td>
                                        <td class="text-end">{{ number_format($detalle->cantidad, 2) }}</td>
                                        <td class="text-end">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                        <td class="text-end">${{ number_format($detalle->subtotal, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Sin detalles asociados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Información</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>Empresa:</strong> {{ $pago->empresa->razon_social ?? 'N/A' }}
                        </li>
                        <li class="mb-2">
                            <strong>Sucursal:</strong> {{ $pago->sucursal->nombre ?? 'N/A' }}
                        </li>
                        <li class="mb-2">
                            <strong>Serie:</strong> {{ $pago->serieModel->serie ?? $pago->serie }}
                        </li>
                        <li class="mb-2">
                            <strong>Número:</strong> {{ $pago->numero }}
                        </li>
                        <li class="mb-2">
                            <strong>Estado:</strong> {{ ucfirst($pago->estado) }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="d-grid mt-3 gap-2">
                <a href="{{ route('admin.pagos.index') }}" class="btn btn-label-secondary">
                    <i class="ri ri-arrow-left-line me-1"></i> Volver
                </a>
                <a href="{{ route('admin.pagos.print', $pago->id) }}" target="_blank" class="btn btn-primary">
                    <i class="ri ri-printer-line me-1"></i> Imprimir Recibo
                </a>
            </div>
        </div>
    </div>
</div>
