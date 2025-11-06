<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">{{ $pago->numero_completo }}</h4>
            <p class="text-muted mb-0">Detalles del pago realizado</p>
        </div>
        <div>
            <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Datos del Estudiante</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-4"><strong>Nombre:</strong></div>
                        <div class="col-sm-8">{{ $pago->matricula->student->nombres ?? '' }} {{ $pago->matricula->student->apellidos ?? '' }}</div>

                        <div class="col-sm-4"><strong>Documento:</strong></div>
                        <div class="col-sm-8">{{ $pago->matricula->student->documento_identidad ?? '' }}</div>

                        <div class="col-sm-4"><strong>Programa:</strong></div>
                        <div class="col-sm-8">{{ $pago->matricula->programa->nombre ?? '' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información del Pago</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-4"><strong>Tipo:</strong></div>
                        <div class="col-sm-8">{{ ucfirst($pago->tipo_pago) }}</div>

                        <div class="col-sm-4"><strong>Fecha:</strong></div>
                        <div class="col-sm-8">{{ format_date($pago->fecha) }}</div>

                        <div class="col-sm-4"><strong>Método:</strong></div>
                        <div class="col-sm-8">{{ $pago->metodo_pago }}</div>

                        @if($pago->referencia)
                        <div class="col-sm-4"><strong>Referencia:</strong></div>
                        <div class="col-sm-8">{{ $pago->referencia }}</div>
                        @endif

                        @if($pago->tasa_cambio)
                        <div class="col-sm-4"><strong>Tasa de Cambio:</strong></div>
                        <div class="col-sm-8">{{ format_money($pago->tasa_cambio, false) }} Bs/{{ $pago->tasa_cambio > 1 ? '$' : 'Bs' }}</div>
                        @endif

                        <div class="col-sm-4"><strong>Estado:</strong></div>
                        <div class="col-sm-8">
                            @if($pago->estado === 'pendiente')
                                <span class="badge bg-warning">Pendiente</span>
                            @elseif($pago->estado === 'aprobado')
                                <span class="badge bg-success">Aprobado</span>
                            @elseif($pago->estado === 'cancelado')
                                <span class="badge bg-danger">Cancelado</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles del Pago -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Detalles del Pago</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>Descripción</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Precio Unit.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pago->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->conceptoPago->nombre ?? 'N/A' }}</td>
                            <td>{{ $detalle->descripcion }}</td>
                            <td class="text-center">{{ $detalle->cantidad }}</td>
                            <td class="text-end"><x-dual-currency :amount="$detalle->precio_unitario" /></td>
                            <td class="text-end"><x-dual-currency :amount="$detalle->subtotal" /></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pago Mixto -->
    @if($pago->es_pago_mixto && $pago->detalles_pago_mixto)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Detalles del Pago Mixto</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Método de Pago</th>
                            <th class="text-end">Monto ($)</th>
                            <th class="text-end">Monto (Bs.)</th>
                            <th>Referencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pago->detalles_pago_mixto as $metodo)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $metodo['metodo'])) }}</td>
                            <td class="text-end"><x-dual-currency :amount="$metodo['monto']" /></td>
                            <td class="text-end">
                                @if(in_array($metodo['metodo'], ['transferencia', 'pago_movil', 'efectivo_bolivares']) && $pago->tasa_cambio)
                                    {{ format_money($metodo['monto'] * $pago->tasa_cambio, false) }} Bs
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $metodo['referencia'] ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Totales -->
    <div class="row">
        <div class="col-md-6 ms-auto">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span><x-dual-currency :amount="$pago->subtotal" /></span>
                    </div>
                    @if($pago->descuento > 0)
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Descuento:</span>
                        <span>-<x-dual-currency :amount="$pago->descuento" /></span>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total:</span>
                        <span class="text-primary"><x-dual-currency :amount="$pago->total" /></span>
                    </div>
                    @if($pago->tasa_cambio && $pago->total_bolivares)
                    <div class="d-flex justify-content-between fw-bold text-success mt-2">
                        <span>Total Bs:</span>
                        <span>{{ format_money($pago->total_bolivares, false) }} Bs</span>
                    </div>
                    <div class="text-muted small mt-1">
                        Tasa: {{ format_money($pago->tasa_cambio, false) }} Bs/{{ $pago->tasa_cambio > 1 ? '$' : 'Bs' }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($pago->observaciones)
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Observaciones</h5>
        </div>
        <div class="card-body">
            <p class="mb-0">{{ $pago->observaciones }}</p>
        </div>
    </div>
    @endif
</div>
