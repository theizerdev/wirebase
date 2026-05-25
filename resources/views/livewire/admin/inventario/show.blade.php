<div>
    <style>
        .inventario-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .detail-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #e8e8e8;
        }

        .detail-label {
            font-size: 13px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 16px;
            color: #2d3748;
            font-weight: 500;
        }
    </style>

    {{-- Hero Section --}}
    <div class="inventario-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h2 class="fw-semibold mb-2">
                <i class="ri ri-archive-line me-2"></i>Detalle del Ítem
            </h2>
            <p class="mb-0 opacity-75">{{ $item->nombre }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.inventario.index') }}" class="btn btn-light btn-sm">
                <i class="ri ri-arrow-left-line me-1"></i>Volver
            </a>
            @can('access iglesias')
                <a href="{{ route('admin.inventario.edit', $item->id) }}" class="btn btn-light btn-sm">
                    <i class="ri ri-edit-line me-1"></i>Editar
                </a>
                <button type="button" 
                        class="btn btn-light btn-sm text-danger"
                        wire:click="deleteItem"
                        wire:confirm="¿Estás seguro de eliminar este ítem?">
                    <i class="ri ri-delete-bin-line me-1"></i>Eliminar
                </button>
            @endcan
        </div>
    </div>

    <div class="row g-4">
        {{-- Información Principal --}}
        <div class="col-md-8">
            <div class="detail-card mb-4">
                <h5 class="fw-bold mb-4">
                    <i class="ri ri-information-line me-2 text-primary"></i>Información del Ítem
                </h5>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="detail-label">Nombre</div>
                        <div class="detail-value">{{ $item->nombre }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-label">Extensión</div>
                        <div class="detail-value">
                            @if($item->iglesia)
                                <a href="{{ route('admin.iglesias.show', $item->iglesia_id) }}" class="text-decoration-none">
                                    <span class="badge bg-label-secondary">{{ $item->iglesia->nombre }}</span>
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-label">Categoría</div>
                        <div class="detail-value">
                            @if($item->categoria)
                                <span class="badge bg-label-info">{{ $item->categoria }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-label">Condición</div>
                        <div class="detail-value">
                            @if($item->condicion)
                                <span class="badge bg-label-warning">{{ $item->condicion }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="detail-label">Cantidad</div>
                        <div class="detail-value">
                            <span class="fw-bold">{{ $item->cantidad }}</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="detail-label">Valor Unitario</div>
                        <div class="detail-value">
                            <span class="fw-bold text-success">${{ number_format($item->valor ?? 0, 2) }}</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="detail-label">Valor Total</div>
                        <div class="detail-value">
                            <span class="fw-bold text-primary">${{ number_format(($item->valor ?? 0) * $item->cantidad, 2) }}</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-label">Tipo de Bien</div>
                        <div class="detail-value">
                            @if($item->tipo_bien)
                                @php
                                    $tipos = \App\Livewire\Admin\Inventario\Index::TIPOS_BIENES;
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
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-label">Fecha de Adquisición</div>
                        <div class="detail-value">
                            @if($item->fecha_adquisicion)
                                <span class="text-muted">{{ \Carbon\Carbon::parse($item->fecha_adquisicion)->format('d/m/Y') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>

                    @if($item->descripcion)
                        <div class="col-12">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value">{{ $item->descripcion }}</div>
                        </div>
                    @endif

                    @if($item->notas)
                        <div class="col-12">
                            <div class="detail-label">Notas Adicionales</div>
                            <div class="detail-value">{{ $item->notas }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Información de la Extensión --}}
        <div class="col-md-4">
            @if($item->iglesia)
                <div class="detail-card">
                    <h5 class="fw-bold mb-4">
                        <i class="ri ri-building-line me-2 text-primary"></i>Extensión
                    </h5>
                    
                    <div class="text-center mb-4">
                        <div class="avatar avatar-lg mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-primary border border-2 border-primary" 
                                  style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                                <i class="ri ri-building-line"></i>
                            </span>
                        </div>
                        <h5 class="fw-bold mb-1">{{ $item->iglesia->nombre }}</h5>
                        @if($item->iglesia->direccion)
                            <p class="text-muted mb-0">
                                <i class="ri ri-map-pin-line"></i> {{ $item->iglesia->direccion }}
                            </p>
                        @endif
                    </div>

                    <hr>

                    <div class="mb-3">
                        <div class="detail-label">ID de Extensión</div>
                        <div class="detail-value">#{{ $item->iglesia->id }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="detail-label">Estado</div>
                        <div class="detail-value">
                            @if($item->iglesia->activa)
                                <span class="badge bg-success">
                                    <i class="ri ri-check-line"></i> Activa
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="ri ri-close-line"></i> Inactiva
                                </span>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('admin.iglesias.show', $item->iglesia_id) }}" class="btn btn-outline-primary w-100 mt-3">
                        <i class="ri ri-external-link-line me-1"></i>Ver Extensión Completa
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
