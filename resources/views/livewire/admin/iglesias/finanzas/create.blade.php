<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Nueva Transacción - {{ $iglesia->nombre }}</h4>
            <p class="text-muted mb-0">Registre diezmos, ofrendas, aportes especiales o gastos</p>
        </div>
        <a href="{{ route('admin.iglesias.finanzas.index', $iglesia->id) }}" class="btn btn-label-secondary">
            <i class="ri ri-arrow-left-line"></i> Volver
        </a>
    </div>

    <!-- Alertas -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulario -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Datos de la Transacción</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row g-3">

                    <!-- Tipo de Transacción -->
                    <div class="col-12">
                        <label class="form-label">Tipo de Transacción *</label>
                        <select class="form-select" wire:model.live="tipo">
                            <option value="diezmo">Diezmo</option>
                            <option value="ofrenda">Ofrenda</option>
                            <option value="aporte_especial">Aporte Especial</option>
                            <option value="gasto_operativo">Gasto Operativo</option>
                            <option value="gasto_ministerio">Gasto de Ministerio</option>
                        </select>
                    </div>

                    <!-- Fecha -->
                    <div class="col-md-6">
                        <label class="form-label">Fecha *</label>
                        <input type="date" class="form-control" wire:model="fecha" required>
                    </div>

                    <!-- Método de Pago -->
                    <div class="col-md-6">
                        <label class="form-label">Método de Pago *</label>
                        <select class="form-select" wire:model="metodoPago" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="punto_venta">Punto de Venta</option>
                            <option value="cheque">Cheque</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <!-- Monto en VES -->
                    <div class="col-md-4">
                        <label class="form-label">Monto (Bolívares) *</label>
                        <div class="input-group">
                            <span class="input-group-text">Bs.</span>
                            <input type="number" class="form-control" wire:model.live="montoVes" step="0.01" min="0" required>
                        </div>
                    </div>

                    <!-- Tasa de Cambio -->
                    <div class="col-md-4">
                        <label class="form-label">Tasa de Cambio</label>
                        <input type="number" class="form-control" wire:model="tasaCambio" step="0.01" readonly>
                        <small class="text-muted">Tasa automática del BCV</small>
                    </div>

                    <!-- Monto en USD -->
                    <div class="col-md-4">
                        <label class="form-label">Monto (Dólares)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" wire:model.live="montoUsd" step="0.01" min="0">
                        </div>
                        <small class="text-muted">Calculado automáticamente o ingrese manualmente</small>
                    </div>

                    <!-- Referencia -->
                    <div class="col-md-6">
                        <label class="form-label">Referencia/Número</label>
                        <input type="text" class="form-control" wire:model="referencia" maxlength="100"
                               placeholder="Número de comprobante, referencia bancaria...">
                    </div>

                    <!-- Descripción -->
                    <div class="col-12">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" wire:model="descripcion" rows="3" maxlength="500"
                                  placeholder="Detalles adicionales sobre la transacción..."></textarea>
                    </div>

                </div>

                <!-- Botones -->
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.iglesias.finanzas.index', $iglesia->id) }}" class="btn btn-label-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri ri-check-line"></i> Guardar Transacción
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="alert alert-info mt-4" role="alert">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="ri ri-information-line ri-xl"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="alert-heading">Información importante</h6>
                <ul class="mb-0">
                    <li>Los montos se registrarán en ambas monedas (VES y USD)</li>
                    <li>Se generará automáticamente un asiento contable de partida doble</li>
                    <li>La tasa de cambio se obtiene automáticamente del sistema</li>
                    <li>Puede editar manualmente el monto en USD si es necesario</li>
                </ul>
            </div>
        </div>
    </div>
</div>
