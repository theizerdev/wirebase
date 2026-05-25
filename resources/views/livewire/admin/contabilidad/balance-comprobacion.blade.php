<div>
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Church search card with integrated empty state --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="text-center mb-3">
                            <i class="ri ri-hospital-line text-primary" style="font-size: 2.5rem;"></i>
                            <h5 class="fw-bold mb-1">Seleccione una Extension</h5>
                            <p class="text-muted mb-0 small">Busque y seleccione la extension para ver el balance de comprobación</p>
                        </div>
                        
                        <div class="position-relative">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="ri ri-search-line text-muted"></i>
                                </span>
                                <input type="text" 
                                       class="form-control border-start-0 ps-0" 
                                       wire:model.live.debounce.300ms="iglesiaSearch"
                                       placeholder="Escriba el nombre o dirección de la extension..."
                                       autocomplete="off"
                                       @click.away="$dispatch('closeIglesiaDropdown')">
                                
                                @if($iglesia_id)
                                    <button type="button" 
                                            class="btn btn-outline-danger"
                                            wire:click="limpiarBusquedaIglesia"
                                            title="Limpiar busqueda">
                                        <i class="ri ri-close-line"></i>
                                    </button>
                                @endif
                            </div>
                            
                            {{-- Results dropdown --}}
                            @if($mostrarResultadosIglesia && count($iglesiasBuscadas) > 0)
                                <div class="list-group position-absolute w-100 mt-2 shadow-lg border" 
                                     style="z-index: 1050; max-height: 300px; overflow-y: auto; background-color: #ffffff;">
                                    @foreach($iglesiasBuscadas as $iglesia)
                                        <button type="button" 
                                                class="list-group-item list-group-item-action py-3"
                                                wire:click="seleccionarIglesia({{ $iglesia->id }})">
                                            <strong>{{ $iglesia->nombre }}</strong>
                                            @if($iglesia->direccion)
                                                <br><small class="text-muted">
                                                    <i class="ri ri-map-pin-line"></i> {{ $iglesia->direccion }}
                                                </small>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        {{-- Selected church alert --}}
                        @if($iglesia_id)
                            @php
                                $iglesiaSeleccionada = \App\Models\Iglesia::find($iglesia_id);
                            @endphp
                            <div class="mt-3">
                                <div class="alert alert-success d-flex align-items-center py-3 px-4 mb-0">
                                    <i class="ri ri-checkbox-circle-fill me-2 fs-5"></i>
                                    <div>
                                        <strong>Extension seleccionada:</strong> {{ $iglesiaSeleccionada?->nombre ?? 'Extension' }}
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Empty state with 3-step guide (inside same card) --}}
                            <div class="mt-4 pt-3 border-top">
                                <div class="text-center mb-3">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light" 
                                         style="width: 80px; height: 80px;">
                                        <i class="ri ri-hospital-line text-muted" style="font-size: 2.5rem; opacity: 0.4;"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-3 small">Use el buscador de arriba para encontrar y seleccionar la extension que desea consultar</p>
                                
                                <div class="row g-2 text-start">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-start">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2" style="flex: 0 0 auto;">
                                                <i class="ri ri-search-line text-primary" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-semibold mb-0" style="font-size: 0.8rem;">1. Busque</h6>
                                                <small class="text-muted" style="font-size: 0.7rem;">Escriba el nombre</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-start">
                                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2" style="flex: 0 0 auto;">
                                                <i class="ri ri-check-line text-success" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-semibold mb-0" style="font-size: 0.8rem;">2. Seleccione</h6>
                                                <small class="text-muted" style="font-size: 0.7rem;">Haga clic en la extension</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-start">
                                            <div class="bg-info bg-opacity-10 rounded-circle p-2 me-2" style="flex: 0 0 auto;">
                                                <i class="ri ri-file-list-3-line text-info" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-semibold mb-0" style="font-size: 0.8rem;">3. Consulte</h6>
                                                <small class="text-muted" style="font-size: 0.7rem;">Vea el balance</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Balance table (only visible when church selected) --}}
        @if($iglesia_id)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="ri-file-list-3-line me-2"></i>Balance de Comprobación</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.contabilidad.balance-comprobacion.excel', ['desde' => $fecha_desde, 'hasta' => $fecha_hasta, 'iglesia_id' => $iglesia_id]) }}" class="btn btn-sm btn-outline-success">
                                <i class="ri-file-excel-2-line me-1"></i>Excel
                            </a>
                            <a href="{{ route('admin.contabilidad.balance-comprobacion.pdf', ['desde' => $fecha_desde, 'hasta' => $fecha_hasta, 'iglesia_id' => $iglesia_id]) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                <i class="ri-file-pdf-2-line me-1"></i>PDF
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3 g-3">
                            <div class="col-md-3">
                                <label class="form-label">Desde</label>
                                <input type="date" wire:model.live="fecha_desde" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hasta</label>
                                <input type="date" wire:model.live="fecha_hasta" class="form-control">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th rowspan="2" class="align-middle">Código</th>
                                        <th rowspan="2" class="align-middle">Cuenta</th>
                                        <th colspan="2" class="text-center">Movimientos del período</th>
                                        <th colspan="2" class="text-center">Saldos finales</th>
                                    </tr>
                                    <tr>
                                        <th class="text-end">Debe</th>
                                        <th class="text-end">Haber</th>
                                        <th class="text-end">Deudor</th>
                                        <th class="text-end">Acreedor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php
                                    // Obtener separadores según configuración regional
                                    $decimalSep = $regionalConfig['decimal_separator'] ?? ',';
                                    $thousandsSep = $regionalConfig['thousands_separator'] ?? '.';
                                @endphp
                                    @forelse($cuentas as $cuenta)
                                        <tr>
                                            <td><strong>{{ $cuenta->codigo }}</strong></td>
                                            <td>{{ $cuenta->nombre }}</td>
                                            <td class="text-end">{{ format_money($cuenta->debe_periodo, 2, $decimalSep, $thousandsSep) }}</td>
                                            <td class="text-end">{{ format_money($cuenta->haber_periodo, 2, $decimalSep, $thousandsSep) }}</td>
                                            <td class="text-end">
                                                @if($cuenta->saldo_deudor > 0)
                                                    {{ format_money($cuenta->saldo_deudor, 2, $decimalSep, $thousandsSep) }}
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($cuenta->saldo_acreedor > 0)
                                                    {{ format_money($cuenta->saldo_acreedor, 2, $decimalSep, $thousandsSep) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No hay movimientos en el período seleccionado</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($cuentas->count() > 0)
                                    <tfoot class="bg-primary text-white fw-bold">
                                        <tr>
                                            <td colspan="2" class="text-end">TOTALES</td>
                                            <td class="text-end">{{ format_money($totales->debe_periodo, 2, $decimalSep, $thousandsSep) }}</td>
                                            <td class="text-end">{{ format_money($totales->haber_periodo, 2, $decimalSep, $thousandsSep) }}</td>
                                            <td class="text-end">{{ format_money($totales->saldo_final_deudor, 2, $decimalSep, $thousandsSep) }}</td>
                                            <td class="text-end">{{ format_money($totales->saldo_final_acreedor, 2, $decimalSep, $thousandsSep) }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>

                        @if($cuentas->count() > 0)
                            <div class="mt-3 text-center">
                                @if(round($totales->saldo_final_deudor, 2) === round($totales->saldo_final_acreedor, 2))
                                    <span class="badge bg-success fs-6"><i class="ri-check-line me-1"></i> Balance Cuadrado</span>
                                @else
                                    <span class="badge bg-danger fs-6"><i class="ri-error-warning-line me-1"></i> Descuadre: {{ auth()->user()->empresa->pais->codigo_moneda ?? 'Bs' }} {{ format_money(abs($totales->saldo_final_deudor - $totales->saldo_final_acreedor), 2, $decimalSep, $thousandsSep) }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>