<div>
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Historial de Movimientos</h5>
                <small class="text-muted">Unidad: {{ $unidad->moto->titulo ?? 'Moto' }} • VIN: {{ $unidad->vin }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.inventario.unidades.index') }}" class="btn btn-label-secondary">
                    <i class="ri ri-arrow-left-line me-1"></i> Volver
                </a>
                <button class="btn btn-label-success" wire:click="export">
                    <i class="mdi mdi-file-excel me-1"></i> Exportar Excel
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" wire:model.lazy="dateFrom">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" wire:model.lazy="dateTo">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" wire:model.live="tipo">
                        <option value="">Todos</option>
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Vista</label>
                    <select class="form-select" wire:model.live="viewMode">
                        <option value="table">Tabla</option>
                        <option value="timeline">Timeline</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($viewMode === 'table')
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Tipo</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Responsable</th>
                            <th class="text-end">Cantidad</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $mov)
                            <tr>
                                <td>{{ optional($mov->occurred_at)->format('d/m/Y H:i') }}</td>
                                <td><span class="badge bg-label-{{ $mov->tipo === 'entrada' ? 'success' : ($mov->tipo === 'salida' ? 'danger' : 'info') }}">{{ ucfirst($mov->tipo) }}</span></td>
                                <td>{{ $mov->origenSucursal->nombre ?? '-' }}</td>
                                <td>{{ $mov->destinoSucursal->nombre ?? '-' }}</td>
                                <td>{{ $mov->responsable->name ?? '-' }}</td>
                                <td class="text-end">{{ number_format($mov->cantidad, 2) }}</td>
                                <td>{{ $mov->observaciones ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No hay movimientos para los filtros seleccionados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @else
            <ul class="timeline">
                @forelse($movimientos as $mov)
                <li class="timeline-item timeline-item-transparent">
                    <span class="timeline-point timeline-point-{{ $mov->tipo === 'entrada' ? 'success' : ($mov->tipo === 'salida' ? 'danger' : 'info') }}"></span>
                    <div class="timeline-event">
                        <div class="timeline-header mb-2">
                            <h6 class="mb-0">{{ ucfirst($mov->tipo) }}</h6>
                            <small class="text-muted">{{ optional($mov->occurred_at)->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="d-flex flex-column">
                            <small>Origen: <strong>{{ $mov->origenSucursal->nombre ?? '-' }}</strong></small>
                            <small>Destino: <strong>{{ $mov->destinoSucursal->nombre ?? '-' }}</strong></small>
                            <small>Responsable: <strong>{{ $mov->responsable->name ?? '-' }}</strong></small>
                            <small>Cantidad: <strong>{{ number_format($mov->cantidad, 2) }}</strong></small>
                            <small>Notas: {{ $mov->observaciones ?? '-' }}</small>
                        </div>
                    </div>
                </li>
                @empty
                <li class="timeline-item timeline-item-transparent">
                    <div class="timeline-event">
                        <div class="text-muted">No hay movimientos para los filtros seleccionados</div>
                    </div>
                </li>
                @endforelse
            </ul>
            @endif
        </div>
        <div class="card-footer">
            {{ $movimientos->links('livewire.pagination') }}
        </div>
    </div>
</div>
