<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Registrar Pago - Contrato #{{ $contrato->numero_contrato }}</h5>
                    <a href="{{ route('admin.contratos.show', $contrato->id) }}" class="btn btn-label-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Volver
                    </a>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Selección de Cuotas -->
                        <div class="col-md-7 border-end">
                            <h6 class="fw-bold mb-3">Seleccione las cuotas a pagar</h6>
                            
                            @if($cuotas_pendientes->count() > 0)
                                <div class="table-responsive border rounded" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-hover table-sm">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th width="50">
                                                    <i class="ri ri-checkbox-multiple-line"></i>
                                                </th>
                                                <th>#</th>
                                                <th>Vencimiento</th>
                                                <th class="text-end">Monto</th>
                                                <th class="text-end">Saldo</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cuotas_pendientes as $cuota)
                                            <tr class="{{ in_array($cuota->id, $cuotas_seleccionadas) ? 'table-primary' : '' }}" 
                                                style="cursor: pointer;"
                                                wire:click="toggleCuota({{ $cuota->id }})">
                                                <td>
                                                    <input type="checkbox" class="form-check-input" 
                                                           value="{{ $cuota->id }}" 
                                                           wire:model.live="cuotas_seleccionadas">
                                                </td>
                                                <td>{{ $cuota->numero_cuota }}</td>
                                                <td>
                                                    {{ $cuota->fecha_vencimiento->format('d/m/Y') }}
                                                    @if($cuota->fecha_vencimiento < now())
                                                        <span class="text-danger small">(Vencida)</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">${{ number_format($cuota->monto_total, 2) }}</td>
                                                <td class="text-end fw-bold text-danger">${{ number_format($cuota->saldo_pendiente, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-label-{{ $cuota->estado == 'vencido' ? 'danger' : 'secondary' }}">
                                                        {{ ucfirst($cuota->estado) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-success">
                                    <i class="ri ri-checkbox-circle-line me-2"></i>
                                    Este contrato no tiene cuotas pendientes de pago.
                                </div>
                            @endif
                        </div>

                        <!-- Formulario de Pago -->
                        <div class="col-md-5 ps-md-4 mt-4 mt-md-0">
                            <h6 class="fw-bold mb-3">Detalle del Pago</h6>
                            
                            <form wire:submit="save">
                                <div class="mb-3">
                                    <div class="card bg-label-primary">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Cuotas seleccionadas:</span>
                                                <span class="fw-bold">{{ count($cuotas_seleccionadas) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between fs-4">
                                                <span class="fw-bold text-primary">Total a Pagar:</span>
                                                <span class="fw-bold text-primary">${{ number_format($total_a_pagar, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Fecha de Pago</label>
                                    <input type="date" class="form-control @error('fecha_pago') is-invalid @enderror" 
                                           wire:model="fecha_pago">
                                    @error('fecha_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Método de Pago</label>
                                    <select class="form-select @error('metodo_pago') is-invalid @enderror" 
                                            wire:model="metodo_pago">
                                        <option value="efectivo">Efectivo</option>
                                        <option value="transferencia">Transferencia Bancaria</option>
                                        <option value="pago_movil">Pago Móvil</option>
                                        <option value="tarjeta">Tarjeta de Débito/Crédito</option>
                                        <option value="zelle">Zelle</option>
                                    </select>
                                    @error('metodo_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Referencia / N° Recibo</label>
                                    <input type="text" class="form-control @error('referencia') is-invalid @enderror" 
                                           wire:model="referencia" placeholder="Ej: 123456">
                                    @error('referencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Monto Recibido ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control @error('monto_recibido') is-invalid @enderror" 
                                               wire:model="monto_recibido">
                                    </div>
                                    @error('monto_recibido') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                              wire:model="observaciones" rows="2"></textarea>
                                    @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg" 
                                            {{ count($cuotas_seleccionadas) == 0 ? 'disabled' : '' }}
                                            wire:confirm="¿Confirmar el registro de este pago?">
                                        <i class="ri ri-secure-payment-line me-2"></i> Procesar Pago
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
