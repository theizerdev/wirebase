<div>
    <style>
        /* Mejoras responsive para inventario */
        @media (max-width: 768px) {
            .card-header h5 {
                font-size: 1.1rem;
            }

            .avatar-initial i {
                font-size: 16px !important;
            }

            .btn-group-vertical .btn {
                touch-action: manipulation;
            }
        }

        /* Mejorar tap targets en móvil */
        @media (hover: none) and (pointer: coarse) {
            .btn-icon {
                min-width: 44px;
                min-height: 44px;
            }
        }

        /* Transiciones suaves */
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        }
    </style>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri ri-checkbox-circle-line me-2"></i>
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri ri-error-warning-line me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($mode === 'list')
        <!-- Vista de Lista -->
        <div class="card">
            <div class="card-header border-bottom">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                    <div>
                        <h5 class="card-title mb-1">
                            <i class="ri ri-archive-line me-2"></i>Inventario de la Iglesia
                        </h5>
                        <p class="mb-0 text-muted small">Gestione los bienes y activos del templo</p>
                    </div>
                    <button type="button" wire:click="setMode('create')" class="btn btn-primary w-100 w-sm-auto">
                        <i class="ri ri-add-line me-1"></i> Agregar Ítem
                    </button>
                </div>
            </div>

            @if($stats['total_items'] > 0)
                <!-- Estadísticas Rápidas - Responsive -->
                <div class="card-body border-bottom">
                    <div class="row g-2 g-md-3">
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center p-2 p-md-3 rounded bg-light">
                                <div class="avatar flex-shrink-0 me-2 me-md-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ri ri-archive-line ri-18px ri-md-20px"></i>
                                    </span>
                                </div>
                                <div class="min-width-0">
                                    <span class="text-muted d-block small text-truncate">Total Ítems</span>
                                    <h6 class="mb-0 fw-bold">{{ $stats['total_items'] }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center p-2 p-md-3 rounded bg-light">
                                <div class="avatar flex-shrink-0 me-2 me-md-3">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="ri ri-money-dollar-circle-line ri-18px ri-md-20px"></i>
                                    </span>
                                </div>
                                <div class="min-width-0">
                                    <span class="text-muted d-block small text-truncate">Valor Total</span>
                                    <h6 class="mb-0 fw-bold">${{ number_format($stats['total_valor'], 2) }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center p-2 p-md-3 rounded bg-light">
                                <div class="avatar flex-shrink-0 me-2 me-md-3">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="ri ri-folder-line ri-18px ri-md-20px"></i>
                                    </span>
                                </div>
                                <div class="min-width-0">
                                    <span class="text-muted d-block small text-truncate">Categorías</span>
                                    <h6 class="mb-0 fw-bold">{{ $stats['categorias_unicas'] }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center p-2 p-md-3 rounded bg-light">
                                <div class="avatar flex-shrink-0 me-2 me-md-3">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="ri ri-sparkling-line ri-18px ri-md-20px"></i>
                                    </span>
                                </div>
                                <div class="min-width-0">
                                    <span class="text-muted d-block small text-truncate">Nuevos</span>
                                    <h6 class="mb-0 fw-bold">{{ $stats['items_nuevos'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card-body p-2 p-md-4">
                @if($items->count() > 0)
                    <!-- Vista Desktop: Tabla -->
                    <div class="d-none d-lg-block">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 200px;">Nombre</th>
                                        <th>Categoría</th>
                                        <th class="text-center">Cantidad</th>
                                        <th>Valor</th>
                                        <th>Tipo</th>
                                        <th>Condición</th>
                                        <th class="text-end" style="min-width: 120px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong>{{ $item->nombre }}</strong>
                                                    @if($item->descripcion)
                                                        <small class="text-muted text-truncate" style="max-width: 250px;" title="{{ $item->descripcion }}">
                                                            {{ Str::limit($item->descripcion, 50) }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($item->categoria)
                                                    <span class="badge bg-label-primary">{{ $item->categoria }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-label-secondary">{{ $item->cantidad }}</span>
                                            </td>
                                            <td>
                                                @if($item->valor_bs)
                                                    <strong class="text-success">Bs {{ number_format($item->valor_bs, 2) }}</strong>
                                                @elseif($item->valor)
                                                    <span class="text-primary">${{ number_format($item->valor, 2) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->tipo_bien)
                                                    @php
                                                        $tipos = \App\Livewire\Public\Iglesias\InventarioManager::TIPOS_BIENES;
                                                        $badgeClass = match($item->tipo_bien) {
                                                            'Inmuebles' => 'bg-label-success',
                                                            'Equipos' => 'bg-label-primary',
                                                            'Mobiliario' => 'bg-label-info',
                                                            'Enseres' => 'bg-label-warning',
                                                            default => 'bg-label-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $tipos[$item->tipo_bien] ?? $item->tipo_bien }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->condicion)
                                                    @php
                                                        $emoji = match($item->condicion) {
                                                            'Nuevo' => '✨',
                                                            'Como Nuevo' => '⭐',
                                                            'Buen Estado' => '👍',
                                                            'Usado' => '✓',
                                                            'Regular' => '⚠️',
                                                            'Malo' => '❌',
                                                            'Para Reparar' => '🔧',
                                                            default => ''
                                                        };
                                                    @endphp
                                                    <span>{{ $emoji }} {{ $item->condicion }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <button type="button" wire:click="setMode('edit', {{ $item->id }})"
                                                            class="btn btn-sm btn-icon btn-outline-primary"
                                                            title="Editar">
                                                        <i class="ri ri-edit-line"></i>
                                                    </button>
                                                    <button type="button" wire:click="delete({{ $item->id }})"
                                                            class="btn btn-sm btn-icon btn-outline-danger"
                                                            title="Eliminar"
                                                            onclick="return confirm('¿Está seguro de eliminar este ítem?')">
                                                        <i class="ri ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Vista Mobile/Tablet: Cards -->
                    <div class="d-lg-none">
                        <div class="row g-3">
                            @foreach($items as $item)
                                <div class="col-12">
                                    <div class="card border shadow-sm h-100">
                                        <div class="card-body p-3">
                                            <!-- Header con nombre y acciones -->
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1 me-2">
                                                    <h6 class="mb-1 fw-bold">{{ $item->nombre }}</h6>
                                                    @if($item->descripcion)
                                                        <p class="mb-0 text-muted small text-truncate">{{ Str::limit($item->descripcion, 80) }}</p>
                                                    @endif
                                                </div>
                                                <div class="btn-group-vertical" role="group">
                                                    <button type="button" wire:click="setMode('edit', {{ $item->id }})"
                                                            class="btn btn-sm btn-icon btn-outline-primary mb-1"
                                                            style="width: 40px; height: 40px;">
                                                        <i class="ri ri-edit-line"></i>
                                                    </button>
                                                    <button type="button" wire:click="delete({{ $item->id }})"
                                                            class="btn btn-sm btn-icon btn-outline-danger"
                                                            style="width: 40px; height: 40px;"
                                                            onclick="return confirm('¿Está seguro de eliminar este ítem?')">
                                                        <i class="ri ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Información en grid -->
                                            <div class="row g-2 mt-2">
                                                @if($item->categoria)
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Categoría</small>
                                                    <span class="badge bg-label-primary">{{ $item->categoria }}</span>
                                                </div>
                                                @endif

                                                @if($item->tipo_bien)
                                                    @php
                                                        $tipos = \App\Livewire\Public\Iglesias\InventarioManager::TIPOS_BIENES;
                                                        $badgeClass = match($item->tipo_bien) {
                                                            'Inmuebles' => 'bg-label-success',
                                                            'Equipos' => 'bg-label-primary',
                                                            'Mobiliario' => 'bg-label-info',
                                                            'Enseres' => 'bg-label-warning',
                                                            default => 'bg-label-secondary'
                                                        };
                                                    @endphp
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Tipo</small>
                                                    <span class="badge {{ $badgeClass }}">{{ $tipos[$item->tipo_bien] ?? $item->tipo_bien }}</span>
                                                </div>
                                                @endif

                                                <div class="col-6">
                                                    <small class="text-muted d-block">Cantidad</small>
                                                    <span class="badge bg-label-secondary fs-6">{{ $item->cantidad }}</span>
                                                </div>

                                                @if($item->condicion)
                                                    @php
                                                        $emoji = match($item->condicion) {
                                                            'Nuevo' => '✨',
                                                            'Como Nuevo' => '⭐',
                                                            'Buen Estado' => '👍',
                                                            'Usado' => '✓',
                                                            'Regular' => '⚠️',
                                                            'Malo' => '❌',
                                                            'Para Reparar' => '🔧',
                                                            default => ''
                                                        };
                                                    @endphp
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Condición</small>
                                                    <span class="small">{{ $emoji }} {{ $item->condicion }}</span>
                                                </div>
                                                @endif

                                                <div class="col-12">
                                                    <small class="text-muted d-block">Valor</small>
                                                    @if($item->valor_bs)
                                                        <strong class="text-success fs-5">Bs {{ number_format($item->valor_bs, 2) }}</strong>
                                                    @elseif($item->valor)
                                                        <span class="text-primary fs-5">${{ number_format($item->valor, 2) }}</span>
                                                    @else
                                                        <span class="text-muted">No especificado</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($stats['total_items'] > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.iglesias.inventario.index', $iglesia->id) }}"
                               class="btn btn-outline-primary">
                                <i class="ri ri-eye-line me-1"></i> Ver todos los {{ $stats['total_items'] }} ítems
                            </a>
                        </div>
                    @endif
                @else
                    <!-- Estado Vacío -->
                    <div class="text-center py-5">
                        <i class="ri ri-archive-line ri-5x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay ítems en el inventario</h5>
                        <p class="text-muted mb-4">Comience agregando el primer bien o activo de la iglesia</p>
                        <button type="button" wire:click="setMode('create')" class="btn btn-primary">
                            <i class="ri ri-add-line me-1"></i> Agregar Primer Ítem
                        </button>
                    </div>
                @endif
            </div>
        </div>

    @else
        <!-- Formulario de Creación/Edición -->
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ri ri-add-circle-line me-2"></i>
                    {{ $mode === 'create' ? 'Nuevo Ítem' : 'Editar Ítem' }}
                </h5>
                <button type="button" wire:click="setMode('list')" class="btn btn-label-secondary">
                    <i class="ri ri-arrow-left-line me-1"></i> Volver al listado
                </button>
            </div>

            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <!-- Nombre -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                <i class="ri ri-file-text-line me-1"></i>Nombre del Ítem <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" wire:model="nombre" placeholder="Ej: Proyector Epson">
                            @error('nombre')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                <i class="ri ri-sticky-note-line me-1"></i>Descripción
                            </label>
                            <textarea class="form-control" wire:model="descripcion" rows="2" placeholder="Descripción detallada del ítem"></textarea>
                            @error('descripcion')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ri ri-folder-line me-1"></i>Categoría
                            </label>
                            <select class="form-select" wire:model="categoria">
                                <option value="">Seleccione una categoría</option>
                                @foreach($categories as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('categoria')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Condición -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ri ri-star-line me-1"></i>Condición
                            </label>
                            <select class="form-select" wire:model="condicion">
                                <option value="">Seleccione condición</option>
                                @foreach($conditions as $cond)
                                    <option value="{{ $cond }}">{{ $cond }}</option>
                                @endforeach
                            </select>
                            @error('condicion')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tipo de Bien -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ri ri-building-line me-1"></i>Tipo de Bien
                            </label>
                            <select class="form-select" wire:model="tipo_bien">
                                <option value="">Seleccione tipo</option>
                                @foreach($tipos_bienes as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('tipo_bien')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Cantidad -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ri ri-number-1 me-1"></i>Cantidad <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" wire:model.live="cantidad" min="1">
                            @error('cantidad')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Número de Factura -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ri ri-receipt-line me-1"></i>Número de Factura <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" wire:model="numero_factura" placeholder="Ej: FAC-001-2026" maxlength="100">
                            @error('numero_factura')
                                <div class="text-danger small mt-1">
                                    <i class="ri ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted">Número de factura de compra (obligatorio)</small>
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
                            <div class="col-md-3">
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

                             <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri ri-shopping-cart-line me-1"></i>Cantidad <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" wire:model.live="cantidad" min="1" placeholder="1">
                                @error('cantidad')
                                    <div class="text-danger small mt-1">
                                        <i class="ri ri-error-warning-line"></i> {{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted">Número de unidades</small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri ri-exchange-line me-1"></i>Tasa BCV
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs/USD</span>
                                    <input type="number" step="0.0001" class="form-control {{ !$tasa_bcv ? 'is-warning' : '' }}" wire:model.live="tasa_bcv" min="0" placeholder="Ej: 36.5000" {{ $loadingRate ?? false ? 'disabled' : '' }}>
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
                                <small class="text-muted d-block mt-1">
                                    @if($loadingRate ?? false)
                                        <i class="ri ri-loader-4-line ri-spin"></i> Obteniendo tasa BCV...
                                    @elseif(!$tasa_bcv && $fecha_adquisicion)
                                        <i class="ri ri-alert-line text-warning"></i>
                                        No se encontró tasa para esta fecha. Por favor ingrésela manualmente.
                                    @else
                                        <i class="ri ri-information-line"></i>
                                        Se carga automáticamente al cambiar la fecha o puede actualizarla manualmente
                                    @endif
                                </small>
                            </div>

                            <div class="col-md-3">
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
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" wire:click="setMode('list')" class="btn btn-label-secondary">
                            <i class="ri ri-close-line me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri ri-save-line me-1"></i> {{ $mode === 'create' ? 'Crear Ítem' : 'Guardar Cambios' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
