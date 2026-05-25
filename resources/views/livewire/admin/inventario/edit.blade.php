<div>
    <style>
        .inventario-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    <div class="inventario-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h2 class="fw-semibold mb-2">
                <i class="ri ri-edit-line me-2"></i>Editar Ítem de Inventario
            </h2>
            <p class="mb-0 opacity-75">Actualizar información del bien o activo</p>
        </div>
        <a href="{{ route('admin.inventario.index') }}" class="btn btn-light btn-sm">
            <i class="ri ri-arrow-left-line me-1"></i>Volver al Inventario
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

                    {{-- Nombre --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre del Ítem <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               wire:model="nombre"
                               placeholder="Ej: Sillas plásticas, Micrófono inalámbrico...">
                        @error('nombre') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Categoría --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" wire:model="categoria">
                            <option value="">Seleccionar categoría...</option>
                            @foreach(self::CATEGORIAS_INVENTARIO as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('categoria') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Cantidad --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Cantidad <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control" 
                               wire:model="cantidad"
                               min="1"
                               placeholder="1">
                        @error('cantidad') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Valor --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Valor Unitario</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   class="form-control" 
                                   wire:model="valor"
                                   min="0"
                                   step="0.01"
                                   placeholder="0.00">
                        </div>
                        @error('valor') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Número de Factura --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Número de Factura <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               wire:model="numero_factura"
                               placeholder="Ej: FAC-001234">
                        @error('numero_factura') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Moneda --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Moneda</label>
                        <select class="form-select" wire:model="moneda">
                            <option value="VES">Bolívares (VES)</option>
                            <option value="USD">Dólares (USD)</option>
                            <option value="EUR">Euros (EUR)</option>
                        </select>
                        @error('moneda') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Tipo de Bien --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipo de Bien</label>
                        <select class="form-select" wire:model="tipo_bien">
                            <option value="">Seleccionar tipo...</option>
                            @foreach(self::TIPOS_BIENES as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('tipo_bien') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Tasa BCV --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tasa BCV</label>
                        <div class="input-group">
                            <span class="input-group-text">Bs/</span>
                            <input type="number" 
                                   class="form-control" 
                                   wire:model="tasa_bcv"
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
                        <small class="text-muted">Tasa del Banco Central de Venezuela</small>
                        @error('tasa_bcv') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Valor en Bolívares --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Valor en Bolívares</label>
                        <div class="input-group">
                            <span class="input-group-text">Bs</span>
                            <input type="number" 
                                   class="form-control" 
                                   wire:model="valor_bs"
                                   readonly
                                   placeholder="Calculado automáticamente">
                        </div>
                        <small class="text-muted">Calculado: Valor × Tasa BCV</small>
                        @error('valor_bs') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Condición --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Condición</label>
                        <input type="text" 
                               class="form-control" 
                               wire:model="condicion"
                               placeholder="Ej: Nuevo, Usado, Buen estado...">
                        @error('condicion') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Fecha de Adquisición --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Adquisición</label>
                        <input type="date" 
                               class="form-control" 
                               wire:model="fecha_adquisicion">
                        @error('fecha_adquisicion') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" 
                                  wire:model="descripcion"
                                  rows="3"
                                  placeholder="Descripción detallada del ítem..."></textarea>
                        @error('descripcion') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Notas --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notas Adicionales</label>
                        <textarea class="form-control" 
                                  wire:model="notas"
                                  rows="2"
                                  placeholder="Observaciones o notas especiales..."></textarea>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.inventario.index') }}" class="btn btn-outline-secondary">
                        <i class="ri ri-close-line me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri ri-save-line me-1"></i>Actualizar Ítem
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
