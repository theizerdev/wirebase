<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Vista previa del recibo -->
    @if($showPreview)
        @php
            $pago = \App\Models\Pago::find($previewPagoId);
            $exchangeRate = \App\Models\ExchangeRate::getLatestRate('USD');
        @endphp
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Vista Previa del Recibo</h5>
                        <button type="button" class="btn-close" wire:click="closePreview"></button>
                    </div>
                    <div class="modal-body">
                        <div class="border rounded p-3">
                            <div class="text-center mb-4">
                                <h5>RECIBO DE PAGO - ORIGINAL</h5>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Nro. Recibo:</strong> {{ $pago->numero_completo }}</p>
                                    <p class="mb-1"><strong>Fecha:</strong> {{ $pago->fecha->format('d/m/Y') }}</p>

                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Estudiante:</strong> {{ $pago->matricula->student->nombres }} {{ $pago->matricula->student->apellidos }}</p>
                                    <p class="mb-1"><strong>Documento:</strong> {{ $pago->matricula->student->documento_identidad }}</p>
                                </div>
                            </div>

                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-end">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pago->detalles as $detalle)
                                            <tr>
                                                <td>{{ $detalle->descripcion }}</td>
                                                <td class="text-center">{{ number_format($detalle->cantidad, 2, ',', '.') }}</td>
                                                <td class="text-end">
                                                    @if($exchangeRate)
                                                        Bs. {{ number_format(($detalle->precio_unitario * $detalle->cantidad) * $exchangeRate, 2, ',', '.') }}
                                                    @else
                                                        ${{ number_format($detalle->precio_unitario * $detalle->cantidad, 2, ',', '.') }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <table class="table table-bordered table-sm">
                                        <tr>
                                            <td class="text-end"><strong>Subtotal:</strong></td>
                                            <td class="text-end">
                                                @if($exchangeRate)
                                                    Bs. {{ number_format($pago->subtotal * $exchangeRate, 2, ',', '.') }}
                                                @else
                                                    ${{ number_format($pago->subtotal, 2, ',', '.') }}
                                                @endif
                                            </td>
                                        </tr>
                                        @if($pago->descuento > 0)
                                            <tr>
                                                <td class="text-end"><strong>Descuento:</strong></td>
                                                <td class="text-end">
                                                    @if($exchangeRate)
                                                        Bs. {{ number_format($pago->descuento * $exchangeRate, 2, ',', '.') }}
                                                    @else
                                                        ${{ number_format($pago->descuento, 2, ',', '.') }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="text-end"><strong>Total:</strong></td>
                                            <td class="text-end">
                                                <strong>
                                                    @if($exchangeRate)
                                                        Bs. {{ number_format($pago->total * $exchangeRate, 2, ',', '.') }}
                                                    @else
                                                        ${{ number_format($pago->total, 2, ',', '.') }}
                                                    @endif
                                                </strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Método:</strong> {{ ucfirst($pago->metodo_pago) }}</p>
                                    @if($pago->referencia)
                                        <p class="mb-1"><strong>Referencia:</strong> {{ $pago->referencia }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6 text-center">
                                    <p class="mb-0">__________________________</p>
                                    <p class="mb-0">Firma y Sello</p>
                                </div>
                            </div>

                            <hr class="my-5">

                            <div class="text-center mb-4">
                                <h5>RECIBO DE PAGO - COPIA</h5>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Nro. Recibo:</strong> {{ $pago->numero_completo }}</p>
                                    <p class="mb-1"><strong>Fecha:</strong> {{ $pago->fecha->format('d/m/Y') }}</p>
                                    @if($exchangeRate)
                                        <p class="mb-1"><strong>Tasa del día:</strong> Bs. {{ number_format($exchangeRate, 2, ',', '.') }}</p>
                                        <p class="mb-1"><strong>Total Bs.:</strong> Bs. {{ number_format($pago->total * $exchangeRate, 2, ',', '.') }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Estudiante:</strong> {{ $pago->matricula->student->nombres }} {{ $pago->matricula->student->apellidos }}</p>
                                    <p class="mb-1"><strong>Documento:</strong> {{ $pago->matricula->student->documento_identidad }}</p>
                                </div>
                            </div>

                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-end">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pago->detalles as $detalle)
                                            <tr>
                                                <td>{{ $detalle->descripcion }}</td>
                                                <td class="text-center">{{ number_format($detalle->cantidad, 2, ',', '.') }}</td>
                                                <td class="text-end">
                                                    @if($exchangeRate)
                                                        Bs. {{ number_format(($detalle->precio_unitario * $detalle->cantidad) * $exchangeRate, 2, ',', '.') }}
                                                    @else
                                                        ${{ number_format($detalle->precio_unitario * $detalle->cantidad, 2, ',', '.') }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <table class="table table-bordered table-sm">
                                        <tr>
                                            <td class="text-end"><strong>Subtotal:</strong></td>
                                            <td class="text-end">
                                                @if($exchangeRate)
                                                    Bs. {{ number_format($pago->subtotal * $exchangeRate, 2, ',', '.') }}
                                                @else
                                                    ${{ number_format($pago->subtotal, 2, ',', '.') }}
                                                @endif
                                            </td>
                                        </tr>
                                        @if($pago->descuento > 0)
                                            <tr>
                                                <td class="text-end"><strong>Descuento:</strong></td>
                                                <td class="text-end">
                                                    @if($exchangeRate)
                                                        Bs. {{ number_format($pago->descuento * $exchangeRate, 2, ',', '.') }}
                                                    @else
                                                        ${{ number_format($pago->descuento, 2, ',', '.') }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="text-end"><strong>Total:</strong></td>
                                            <td class="text-end">
                                                <strong>
                                                    @if($exchangeRate)
                                                        Bs. {{ number_format($pago->total * $exchangeRate, 2, ',', '.') }}
                                                    @else
                                                        ${{ number_format($pago->total, 2, ',', '.') }}
                                                    @endif
                                                </strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Método:</strong> {{ ucfirst($pago->metodo_pago) }}</p>
                                    @if($pago->referencia)
                                        <p class="mb-1"><strong>Referencia:</strong> {{ $pago->referencia }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6 text-center">
                                    <p class="mb-0">__________________________</p>
                                    <p class="mb-0">Firma y Sello</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closePreview">
                            <i class="ri ri-close-line me-1"></i> Cerrar
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="downloadReceipt({{ $pago->id }})">
                            <i class="ri ri-download-line me-1"></i> Descargar PDF
                        </button>
                        <button type="button" class="btn btn-success" onclick="window.print()">
                            <i class="ri ri-printer-line me-1"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Pagos</h6>
                            <h2 class="mb-0">{{ number_format($this->stats['total']) }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="ri ri-file-list-3-line text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Aprobados</h6>
                            <h2 class="mb-0">{{ number_format($this->stats['aprobados']) }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="ri ri-check-double-line text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Pendientes</h6>
                            <h2 class="mb-0">{{ number_format($this->stats['pendientes']) }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="ri ri-time-line text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Ingresos Totales</h6>
                            <h2 class="mb-0"><x-dual-currency :amount="$this->stats['ingresos_totales']" /></h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="ri ri-money-dollar-circle-line text-info" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Lista de Pagos</h5>
                            <p class="mb-0">Administra los pagos registrados en el sistema</p>
                        </div>
                        @can('create pagos')
                        <div>
                            <a href="{{ route('admin.pagos.create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo Pago
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Número, estudiante, referencia..."
                                   wire:model.live.debounce.300ms="search">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="status">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="aprobado">Aprobado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mostrar</label>
                            <select class="form-select" wire:model.live="perPage">
                                <option value="10">10 por página</option>
                                <option value="25">25 por página</option>
                                <option value="50">50 por página</option>
                                <option value="100">100 por página</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-label-secondary" wire:click="clearFilters">
                                <i class="ri ri-eraser-line"></i> Limpiar
                            </button>
                            <button type="button" class="btn btn-label-success" wire:click="export">
                                <i class="mdi mdi-file-excel"></i> Exportar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('serie')" style="cursor: pointer;">
                                    Documento
                                    @if($sortBy === 'serie')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Estudiante</th>
                                <th>Método Pago</th>
                                <th wire:click="sortBy('total')" style="cursor: pointer;">
                                    Total
                                    @if($sortBy === 'total')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('fecha')" style="cursor: pointer;">
                                    Fecha
                                    @if($sortBy === 'fecha')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('estado')" style="cursor: pointer;">
                                    Estado
                                    @if($sortBy === 'estado')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pagos as $pago)
                                <tr>
                                    <td>
                                        <div class="fw-medium text-primary">{{ $pago->numero_completo }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($pago->matricula && $pago->matricula->student)
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded bg-label-primary">{{ substr($pago->matricula->student->nombres ?? '', 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $pago->matricula->student->nombres ?? '' }} {{ $pago->matricula->student->apellidos ?? '' }}</h6>
                                                    <small class="text-muted">{{ $pago->matricula->student->documento_identidad ?? '' }}</small>
                                                </div>
                                            @else
                                                <div class="text-muted">
                                                    <i class="ri ri-user-unfollow-line me-2"></i>
                                                    <small>Estudiante no disponible</small>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $iconClass = match($pago->metodo_pago) {
                                                    'efectivo' => 'ri ri-money-dollar-circle-line text-success',
                                                    'transferencia' => 'ri ri-bank-line text-info',
                                                    'pago_movil' => 'ri ri-smartphone-line text-primary',
                                                    'punto de venta' => 'ri ri-bank-card-line text-primary',
                                                    'tarjeta' => 'ri ri-bank-card-line text-primary',
                                                    default => 'ri ri-question-line text-muted'
                                                };
                                            @endphp
                                            <i class="{{ $iconClass }} me-2" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <div>{{ ucfirst($pago->metodo_pago ?? 'N/A') }}</div>
                                                @if($pago->referencia && in_array($pago->metodo_pago, ['pago_movil', 'transferencia', 'punto de venta']))
                                                    <small class="text-muted">Ref: {{ $pago->referencia }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><x-dual-currency :amount="$pago->total" /></div>
                                        @if($pago->descuento > 0)
                                            <small class="text-muted">Desc: <x-dual-currency :amount="$pago->descuento" /></small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ format_date($pago->fecha) }}</div>
                                        <small class="text-muted">{{ $pago->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   id="statusSwitch{{ $pago->id }}"
                                                   {{ $pago->estado === 'aprobado' ? 'checked' : '' }}
                                                   @can('edit pagos') wire:click="toggleStatus({{ $pago->id }})" @endcan>
                                            <label class="form-check-label" for="statusSwitch{{ $pago->id }}">
                                                {{ ucfirst($pago->estado) }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ri ri-more-2-line"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @can('view pagos')
                                                <a class="dropdown-item" href="{{ route('admin.pagos.show', $pago) }}">
                                                    <i class="ri ri-eye-line me-1"></i> Ver
                                                </a>
                                                @endcan
                                                @can('edit pagos')
                                               
                                                <a class="dropdown-item" href="{{ route('admin.pagos.print', $pago->id) }}" target="_blank">
                                                    <i class="ri ri-printer-line me-1"></i> Imprimir
                                                </a>
                                                @endcan
                                                @can('delete pagos')
                                                <button type="button" class="dropdown-item text-danger"
                                                        wire:click="delete({{ $pago->id }})"
                                                        wire:confirm="¿Estás seguro de eliminar este pago?">
                                                    <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                                </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No se encontraron pagos que coincidan con los filtros</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                   {{ $pagos->links('livewire.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>