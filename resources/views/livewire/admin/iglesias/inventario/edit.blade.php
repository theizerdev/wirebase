<div>
    <!-- Header con Información -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="avatar flex-shrink-0 me-3">
                    <span class="avatar-initial rounded bg-label-primary">
                        <i class="ri ri-add-circle-line ri-24px"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1">Editar Ítem de Inventario</h5>
                    <p class="text-muted mb-0">Complete la información para registrar un nuevo bien en el inventario de la iglesia</p>
                </div>
                <a href="{{ route('admin.iglesias.index', $iglesiaId) }}" class="btn btn-label-secondary">
                    <i class="ri ri-arrow-left-line"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save">
        <!-- Sección 1: Información Básica -->
        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h6 class="mb-0">
                    <i class="ri ri-information-line me-2 text-primary"></i>
                    Información Básica
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-file-text-line me-1"></i>Nombre del Ítem <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" wire:model="nombre" placeholder="Ej: Silla presidencial, Proyector Epson...">
                        @error('nombre')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Ingrese un nombre descriptivo para identificar el ítem</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-number-1 me-1"></i>Cantidad <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" wire:model="cantidad" min="1" placeholder="1">
                        @error('cantidad')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Número de unidades</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-receipt-line me-1"></i>Número de Factura <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" wire:model="numero_factura" placeholder="Ej: FAC-001-2026" maxlength="100">
                        @error('numero_factura')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Factura de compra (obligatorio)</small>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-article-line me-1"></i>Descripción Detallada
                        </label>
                        <textarea class="form-control" wire:model="descripcion" rows="3" placeholder="Describa características, marca, modelo, color, material, etc."></textarea>
                        @error('descripcion')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Agregue detalles que ayuden a identificar el ítem (marca, modelo, color, material, etc.)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 2: Clasificación -->
        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h6 class="mb-0">
                    <i class="ri ri-price-tag-3-line me-2 text-info"></i>
                    Clasificación del Bien
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-folder-line me-1"></i>Categoría
                        </label>
                        <select class="form-select" wire:model="categoria">
                            <option value="">Seleccione una categoría</option>
                            @foreach($this->categorias as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('categoria')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Tipo de artículo o equipo</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-sparkling-line me-1"></i>Condición
                        </label>
                        <select class="form-select" wire:model="condicion">
                            <option value="">Seleccione condición</option>
                            <option value="Nuevo">✨ Nuevo</option>
                            <option value="Como Nuevo">⭐ Como Nuevo</option>
                            <option value="Buen Estado">✅ Buen Estado</option>
                            <option value="Usado">📦 Usado</option>
                            <option value="Regular">⚠️ Regular</option>
                            <option value="Malo">❌ Malo</option>
                            <option value="Para Reparar">🔧 Para Reparar</option>
                        </select>
                        @error('condicion')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Estado actual del ítem</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-building-line me-1"></i>Tipo de Bien
                        </label>
                        <select class="form-select" wire:model="tipo_bien">
                            <option value="">Seleccione tipo de bien</option>
                            @foreach(\App\Livewire\Admin\Iglesias\Inventario\Index::TIPOS_BIENES as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('tipo_bien')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Clasificación contable del activo</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-calendar-line me-1"></i>Fecha de Adquisición
                        </label>
                        <input type="date" class="form-control" wire:model="fecha_adquisicion">
                        @error('fecha_adquisicion')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Cuándo se adquirió el bien</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 3: Valoración -->
        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h6 class="mb-0">
                    <i class="ri ri-money-dollar-circle-line me-2 text-success"></i>
                    Valoración Económica
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning d-flex align-items-start mb-3">
                    <i class="ri ri-information-line ri-lg me-2"></i>
                    <div>
                        <strong>Importante:</strong> La valoración debe expresarse en <strong>Bolívares (VES)</strong>.
                        Si el bien fue adquirido en dólares, ingrese la tasa BCV del día de adquisición para calcular automáticamente el equivalente en Bolívares.
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-money-dollar-box-line me-1"></i>Valor en USD
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control" wire:model.live="valor" min="0" placeholder="0.00">
                        </div>
                        @error('valor')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Valor en dólares (referencial)</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-exchange-line me-1"></i>Tasa BCV
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Bs/USD</span>
                            <input type="number" step="0.0001" class="form-control" wire:model.live="tasa_bcv" min="0" placeholder="Ej: 36.5000" {{ $loadingRate ? 'disabled' : '' }}>
                            <button type="button" class="btn btn-outline-primary" wire:click="cargarTasaDelDia" wire:loading.attr="disabled" title="Cargar tasa del día desde DolarAPI">
                                <i class="ri ri-refresh-line" wire:loading.remove></i>
                                <i class="ri ri-loader-4-line ri-spin" wire:loading></i>
                            </button>
                        </div>
                        @error('tasa_bcv')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">
                            @if($loadingRate)
                                <i class="ri ri-loader-4-line ri-spin"></i> Obteniendo tasa BCV...
                            @else
                                <i class="ri ri-information-line"></i>
                                Se carga automáticamente al cambiar la fecha o puede actualizarla manualmente
                            @endif
                        </small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-calculator-line me-1"></i>Valor en Bolívares
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white">Bs</span>
                            <input type="number" step="0.01" class="form-control fw-bold" wire:model="valor_bs" readonly style="background-color: #f8f9fa;">
                        </div>
                        @error('valor_bs')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Calculado automáticamente</small>
                    </div>

                    @if($cantidad > 0 && $valor_bs > 0)
                    <div class="col-12">
                        <div class="alert alert-success d-flex align-items-center mb-0">
                            <i class="ri ri-calculator-line ri-lg me-2"></i>
                            <div class="flex-grow-1">
                                <strong>Valor Total en Bolívares:</strong>
                                <span class="fs-5 fw-bold ms-2">Bs {{ number_format($cantidad * $valor_bs, 2, ',', '.') }}</span>
                                <small class="text-muted ms-2">({{ $cantidad }} x Bs {{ number_format($valor_bs, 2, ',', '.') }})</small>
                            </div>
                            @if($tasa_bcv)
                            <div class="ms-3 text-end">
                                <small class="text-muted d-block">Tasa BCV aplicada</small>
                                <strong>Bs {{ number_format($tasa_bcv, 4, ',', '.') }}/USD</strong>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-coins-line me-1"></i>Moneda Principal
                        </label>
                        <select class="form-select" wire:model="moneda">
                            <option value="VES" selected>VES - Bolívar Soberano (Principal)</option>
                            <option value="USD">USD - Dólar Estadounidense</option>
                            <option value="EUR">EUR - Euro</option>
                        </select>
                        @error('moneda')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Moneda base para reportes contables</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-calendar-line me-1"></i>Fecha de Adquisición
                        </label>
                        <input type="date" class="form-control" wire:model.live="fecha_adquisicion">
                        @error('fecha_adquisicion')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Determina la tasa BCV aplicable</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 4: Notas Adicionales -->
        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h6 class="mb-0">
                    <i class="ri ri-sticky-note-line me-2 text-warning"></i>
                    Notas Adicionales
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            <i class="ri ri-chat-quote-line me-1"></i>Notas / Observaciones
                        </label>
                        <textarea class="form-control" wire:model="notas" rows="2" placeholder="Información adicional importante..."></textarea>
                        @error('notas')
                            <div class="text-danger small mt-1">
                                <i class="ri ri-error-warning-line"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Cualquier información adicional relevante (garantía, proveedor, ubicación física, etc.)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.iglesias.index', $iglesiaId) }}" class="btn btn-label-secondary">
                        <i class="ri ri-close-line"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="ri ri-save-3-line"></i> Guardar Ítem
                        </span>
                        <span wire:loading>
                            <i class="ri ri-loader-4-line ri-spin"></i> Guardando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

