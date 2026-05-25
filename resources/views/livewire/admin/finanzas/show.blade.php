<div>
    <style>
        .finanzas-hero {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
    </style>

    {{-- Hero Section --}}
    <div class="finanzas-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h2 class="fw-semibold mb-2">
                <i class="ri ri-file-list-3-line me-2"></i>Detalle de Transacción
            </h2>
            <p class="mb-0 opacity-75">Información completa de la transacción financiera</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.finanzas.edit', $transaction->id) }}" class="btn btn-light btn-sm">
                <i class="ri ri-edit-line me-1"></i>Editar
            </a>
            <a href="{{ route('admin.finanzas.index') }}" class="btn btn-light btn-sm">
                <i class="ri ri-arrow-left-line me-1"></i>Volver a Finanzas
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Transaction Details Card --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="ri ri-information-line me-2"></i>Información de la Transacción
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Extensión --}}
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Extensión</label>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ri ri-church-line"></i>
                                    </span>
                                </div>
                                <div>
                                    <strong>{{ $transaction->iglesia ? $transaction->iglesia->nombre : '-' }}</strong>
                                </div>
                            </div>
                        </div>

                        {{-- Tipo --}}
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Tipo de Transacción</label>
                            <div>
                                <span class="badge {{ $transaction->esIngreso() ? 'bg-success' : 'bg-danger' }} fs-6">
                                    {{ $transaction->tipo_label }}
                                </span>
                            </div>
                        </div>

                        {{-- Fecha --}}
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Fecha</label>
                            <div>
                                <i class="ri ri-calendar-line me-1"></i>
                                <strong>{{ \Carbon\Carbon::parse($transaction->fecha)->format('d/m/Y') }}</strong>
                            </div>
                        </div>

                        {{-- Método de Pago --}}
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Método de Pago</label>
                            <div>
                                <span class="badge bg-label-secondary">
                                    {{ $transaction->metodo_pago_label }}
                                </span>
                            </div>
                        </div>

                        {{-- Monto VES --}}
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Monto en Bolívares</label>
                            <div>
                                <h4 class="mb-0 {{ $transaction->esIngreso() ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->esIngreso() ? '+' : '-' }} Bs. {{ number_format($transaction->monto_bs, 2, ',', '.') }}
                                </h4>
                            </div>
                        </div>

                        {{-- Monto USD --}}
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Monto en Dólares</label>
                            <div>
                                <h4 class="mb-0 {{ $transaction->esIngreso() ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->esIngreso() ? '+' : '-' }} ${{ number_format($transaction->monto, 2, ',', '.') }}
                                </h4>
                            </div>
                        </div>

                        {{-- Tasa de Cambio --}}
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Tasa de Cambio</label>
                            <div>
                                <strong>Bs. {{ number_format($transaction->tasa_cambio ?? 0, 4, ',', '.') }}</strong>
                                <small class="text-muted ms-2">(BCV)</small>
                            </div>
                        </div>

                        {{-- Referencia Bancaria --}}
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Referencia Bancaria</label>
                            <div>
                                <strong>{{ $transaction->referencia_bancaria ?? '-' }}</strong>
                            </div>
                        </div>

                        {{-- Descripción --}}
                        <div class="col-12">
                            <label class="text-muted small mb-1">Descripción</label>
                            <div class="p-3 bg-light rounded">
                                {{ $transaction->descripcion ?? 'Sin descripción' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Accounting Entry & Actions Card --}}
        <div class="col-lg-4">
            {{-- Asiento Contable --}}
            @if($transaction->asientoContable)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="ri ri-calculator-line me-2"></i>Asiento Contable
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Número de Asiento</label>
                            <div>
                                <strong>#{{ $transaction->asientoContable->id }}</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Fecha de Registro</label>
                            <div>
                                <i class="ri ri-time-line me-1"></i>
                                {{ $transaction->asientoContable->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <a href="{{ route('admin.contabilidad.asientos') }}" 
                           class="btn btn-outline-primary btn-sm w-100">
                            <i class="ri ri-eye-line me-1"></i>Ver Asiento Contable
                        </a>
                    </div>
                </div>
            @endif

            {{-- Acciones --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="ri ri-tools-line me-2"></i>Acciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.finanzas.edit', $transaction->id) }}" 
                           class="btn btn-primary">
                            <i class="ri ri-edit-line me-1"></i>Editar Transacción
                        </a>
                        
                        <form wire:submit="deleteTransaction" 
                              onsubmit="return confirm('¿Está seguro de eliminar esta transacción? Esta acción no se puede deshacer.')">
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="ri ri-delete-bin-line me-1"></i>Eliminar Transacción
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
