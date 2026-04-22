<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri ri-check-line me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">Procesar Nómina</h5>
                    <p class="mb-0">Selecciona el periodo y empleados</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-label-secondary" wire:click="precalcular"><i class="ri ri-calculator-line me-1"></i> Precalcular</button>
                    <button class="btn btn-primary" wire:click="aprobar"><i class="ri ri-check-line me-1"></i> Aprobar</button>
                    <button class="btn btn-label-success" wire:click="exportExcel"><i class="mdi mdi-file-excel me-1"></i> Exportar</button>
                    <button class="btn btn-label-secondary" wire:click="generarRecibosPdf"><i class="ri ri-file-pdf-2-line me-1"></i> Generar Recibos</button>
                    <button class="btn btn-label-info" wire:click="enviarWhatsAppRecibos"><i class="ri ri-whatsapp-line me-1"></i> WhatsApp Recibos</button>
                </div>
            </div>
        </div>
        <div class="card-header border-bottom">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" wire:model.lazy="dateFrom">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" wire:model.lazy="dateTo">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Frecuencia</label>
                    <select class="form-select" wire:model.live="frecuencia">
                        <option value="semanal">Semanal</option>
                        <option value="quincenal">Quincenal</option>
                        <option value="mensual">Mensual</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Horas Extra (global)</label>
                    <input type="number" step="0.5" class="form-control" wire:model.lazy="horas_extra" placeholder="0">
                    <small class="text-muted">Se aplica a todos los seleccionados</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Factor Horas Extra</label>
                    <input type="number" step="0.1" class="form-control" wire:model.lazy="extra_rate" placeholder="1.5">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Empleado</th>
                            <th>Documento</th>
                            <th>Puesto</th>
                            <th class="text-end">Salario Base</th>
                            <th class="text-end">Horas Extra</th>
                            <th class="text-end">Bono</th>
                            <th class="text-end">Comisión</th>
                            <th class="text-center">Seleccionar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empleados as $emp)
                        <tr>
                            <td>{{ $emp->nombre }} {{ $emp->apellido }}</td>
                            <td>{{ $emp->documento }}</td>
                            <td>{{ $emp->puesto }}</td>
                            <td class="text-end">${{ number_format($emp->salario_base, 2) }}</td>
                            <td class="text-end">
                                <input type="number" step="0.5" class="form-control form-control-sm text-end" 
                                       wire:model.lazy="extras.{{ $emp->id }}" placeholder="0">
                            </td>
                            <td class="text-end">
                                <input type="number" step="0.01" class="form-control form-control-sm text-end" 
                                       wire:model.lazy="bonos.{{ $emp->id }}" placeholder="0.00">
                            </td>
                            <td class="text-end">
                                <input type="number" step="0.01" class="form-control form-control-sm text-end" 
                                       wire:model.lazy="comisiones.{{ $emp->id }}" placeholder="0.00">
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input" wire:model="selectedEmpleadoIds" value="{{ $emp->id }}">
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">No hay empleados activos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Resultado (precalculo)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Empleado</th>
                            <th>Concepto</th>
                            <th>Tipo</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($result['items'] ?? []) as $it)
                        <tr>
                            <td>{{ $it['empleado'] }}</td>
                            <td>{{ $it['concepto_nombre'] }}</td>
                            <td>{{ ucfirst($it['tipo']) }}</td>
                            <td class="text-end">${{ number_format($it['subtotal'], 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">Sin datos</td></tr>
                        @endforelse
                    </tbody>
                    @if(!empty($result['items']))
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-end">${{ number_format($result['total'] ?? 0, 2) }}</th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
