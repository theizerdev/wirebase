<div>
    <style>
        .finanzas-hero {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .iglesia-search-results {
            position: absolute;
            width: 100%;
            z-index: 1050;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            border-radius: 0.5rem;
        }

        .iglesia-search-results .list-group-item {
            cursor: pointer;
            transition: all 0.2s;
        }

        .iglesia-search-results .list-group-item:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
        }
    </style>

    {{-- Hero Section --}}
    <div class="finanzas-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h2 class="fw-semibold mb-2">
                <i class="ri ri-edit-line me-2"></i>Editar Transacción Financiera
            </h2>
            <p class="mb-0 opacity-75">Actualizar información de la transacción</p>
        </div>
        <a href="{{ route('admin.finanzas.index') }}" class="btn btn-light btn-sm">
            <i class="ri ri-arrow-left-line me-1"></i>Volver a Finanzas
        </a>
    </div>

    {{-- Form --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form wire:submit="update">
                <div class="row">
                    {{-- Extensión (Buscador en tiempo real) --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Extensión <span class="text-danger">*</span></label>
                        
                        <div class="position-relative">
                            <input type="text" 
                                   class="form-control" 
                                   wire:model.live.debounce.300ms="iglesiaSearch"
                                   placeholder="Buscar extensión por nombre o dirección..."
                                   autocomplete="off">
                            
                            @if($iglesia_id)
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger position-absolute top-50 end-0 translate-middle-y me-1"
                                        wire:click="limpiarBusquedaIglesia"
                                        title="Limpiar selección">
                                    <i class="ri ri-close-line"></i>
                                </button>
                            @endif
                        </div>
                        
                        {{-- Resultados de búsqueda --}}
                        @if($mostrarResultadosIglesia && count($iglesiasBuscadas) > 0)
                            <div class="list-group position-absolute w-100 mt-1 shadow-lg border" 
                                 style="z-index: 1050; max-height: 300px; overflow-y: auto; background-color: #ffffff; border-radius: 0.5rem;">
                                @foreach($iglesiasBuscadas as $iglesia)
                                    <button type="button" 
                                            class="list-group-item list-group-item-action"
                                            wire:click="seleccionarIglesia({{ $iglesia->id }})">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $iglesia->nombre }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="ri ri-map-pin-line"></i> {{ $iglesia->direccion ?? 'Sin dirección' }}
                                                </small>
                                            </div>
                                            @if($iglesia->activa)
                                                <span class="badge bg-success">
                                                    <i class="ri ri-check-line"></i> Activa
                                                </span>
                                            @endif
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        
                        @error('iglesia_id') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Tipo de transacción --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de Transacción <span class="text-danger">*</span></label>
                        <select class="form-select" wire:model="tipo">
                            <optgroup label="Ingresos">
                                <option value="{{ \App\Models\TransaccionFinanciera::TIPO_DIEZMO }}">Diezmo</option>
                                <option value="{{ \App\Models\TransaccionFinanciera::TIPO_OFRENDA }}">Ofrenda</option>
                                <option value="{{ \App\Models\TransaccionFinanciera::TIPO_APORTE_ESPECIAL }}">Aporte Especial</option>
                                <option value="{{ \App\Models\TransaccionFinanciera::TIPO_OTRO_INGRESO }}">Otro Ingreso</option>
                            </optgroup>
                            <optgroup label="Egresos">
                                <option value="{{ \App\Models\TransaccionFinanciera::TIPO_GASTO_OPERATIVO }}">Gasto Operativo</option>
                                <option value="{{ \App\Models\TransaccionFinanciera::TIPO_GASTO_MINISTERIO }}">Gasto Ministerio</option>
                                <option value="{{ \App\Models\TransaccionFinanciera::TIPO_OTRO_EGRESO }}">Otro Egreso</option>
                            </optgroup>
                        </select>
                        @error('tipo') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Fecha --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control" 
                               wire:model="fecha">
                        @error('fecha') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Monto VES --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Monto en Bolívares <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Bs</span>
                            <input type="number" 
                                   class="form-control" 
                                   wire:model="montoVes"
                                   min="0"
                                   step="0.01"
                                   placeholder="0.00">
                        </div>
                        @error('montoVes') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Tasa de cambio --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tasa de Cambio</label>
                        <div class="input-group">
                            <span class="input-group-text">Bs/</span>
                            <input type="number" 
                                   class="form-control" 
                                   wire:model="tasaCambio"
                                   min="0"
                                   step="0.0001"
                                   placeholder="0.0000"
                                   {{ $loadingRate ? 'disabled' : '' }}>
                            @if($loadingRate)
                                <span class="input-group-text">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </span>
                            @endif
                        </div>
                        <small class="text-muted">Tasa BCV del día seleccionado</small>
                    </div>

                    {{-- Monto USD (calculado) --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Monto en Dólares</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   class="form-control" 
                                   wire:model="montoUsd"
                                   min="0"
                                   step="0.01"
                                   placeholder="Calculado automáticamente">
                        </div>
                        <small class="text-muted">Calculado: Bs / Tasa</small>
                    </div>

                    {{-- Método de pago --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                        <select class="form-select" wire:model="metodoPago">
                            <option value="{{ \App\Models\TransaccionFinanciera::METODO_EFECTIVO }}">Efectivo</option>
                            <option value="{{ \App\Models\TransaccionFinanciera::METODO_TRANSFERENCIA }}">Transferencia</option>
                            <option value="{{ \App\Models\TransaccionFinanciera::METODO_PUNTO_VENTA }}">Punto de Venta</option>
                            <option value="{{ \App\Models\TransaccionFinanciera::METODO_CHEQUE }}">Cheque</option>
                            <option value="{{ \App\Models\TransaccionFinanciera::METODO_OTRO }}">Otro</option>
                        </select>
                        @error('metodoPago') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Referencia bancaria --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Referencia Bancaria</label>
                        <input type="text" 
                               class="form-control" 
                               wire:model="referencia"
                               placeholder="Número de referencia, comprobante...">
                        @error('referencia') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" 
                                  wire:model="descripcion"
                                  rows="3"
                                  placeholder="Descripción detallada de la transacción..."></textarea>
                        @error('descripcion') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.finanzas.index') }}" class="btn btn-outline-secondary">
                        <i class="ri ri-close-line me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri ri-save-line me-1"></i>Actualizar Transacción
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Click outside handler --}}
    <script>
    document.addEventListener('click', function(e) {
        const iglesiaSearchInput = document.querySelector('[wire\\:model="iglesiaSearch"]');
        const iglesiaResults = document.querySelector('.list-group.position-absolute');
        
        if (iglesiaSearchInput && iglesiaResults) {
            if (!iglesiaSearchInput.contains(e.target) && !iglesiaResults.contains(e.target)) {
                Livewire.dispatch('closeIglesiaDropdown');
            }
        }
    });
    </script>
</div>
