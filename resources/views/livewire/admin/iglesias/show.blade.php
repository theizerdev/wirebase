<div>
    <!-- Estadísticas Rápidas -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-archive-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <span class="text-muted d-block">Total Ítems</span>
                            <h4 class="mb-0 fw-bold">{{ $stats['total_items'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-money-dollar-circle-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <span class="text-muted d-block">Valor Total</span>
                            <h4 class="mb-0 fw-bold text-success">${{ number_format($stats['total_valor'] ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-price-tag-3-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <span class="text-muted d-block">Categorías</span>
                            <h4 class="mb-0 fw-bold text-info">{{ $stats['categorias_unicas'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ri ri-sparkling-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <span class="text-muted d-block">Ítems Nuevos</span>
                            <h4 class="mb-0 fw-bold text-warning">{{ $stats['items_nuevos'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs de Navegación -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <ul class="nav nav-tabs card-header-tabs" id="iglesiaTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                                <i class="ri ri-information-line me-1"></i> Información General
                            </button>
                        </li>
                       
                       
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="iglesiaTabsContent">
                        <!-- Tab: Información General -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="card-title mb-0">Detalles de la Extensión</h5>
                                <div>
                                    <a href="{{ route('admin.iglesias.index') }}" class="btn btn-label-secondary">
                                        <i class="ri ri-arrow-left-line"></i> Volver
                                    </a>
                                    @can('edit extensiones')
                                    <a href="{{ route('admin.iglesias.edit', $iglesia->id) }}" class="btn btn-primary">
                                        <i class="ri ri-edit-line"></i> Editar
                                    </a>
                                    @endcan
                                </div>
                            </div>

                            <div class="row">
                        <!-- Datos básicos -->
                        <div class="col-12 mb-4">
                            <h6 class="text-muted mb-3">Datos Básicos</h6>
                            <hr>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombre:</label>
                            <p>{{ $iglesia->nombre }}</p>
                        </div>

                       

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Pastor Principal:</label>
                            <p>
                                @if($iglesia->pastor)
                                    {{ $iglesia->pastor->nombres }} {{ $iglesia->pastor->apellidos }}
                                @else
                                    <span class="text-muted">Sin asignar</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Dirección:</label>
                            <p>{{ $iglesia->direccion ?: 'No especificado' }}</p>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Teléfono:</label>
                            <p>{{ $iglesia->telefono ?: 'No especificado' }}</p>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Email:</label>
                            <p>{{ $iglesia->email ?: 'No especificado' }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Fecha de Fundación:</label>
                            <p>{{ $iglesia->fecha_fundacion ? \Carbon\Carbon::parse($iglesia->fecha_fundacion)->format('d/m/Y') : 'No especificado' }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Años Activa:</label>
                            <p>{{ intval($iglesia->anios_activa_formateado) }}</p>
                        </div>

                        <!-- Información geográfica -->
                        <div class="col-12 mb-4 mt-4">
                            <h6 class="text-muted mb-3">Información Geográfica</h6>
                            <hr>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Estado:</label>
                            <p>{{ $iglesia->estado ? $iglesia->estado->nombre : 'No especificado' }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Ciudad:</label>
                            <p>{{ $iglesia->ciudad ? $iglesia->ciudad->nombre : 'No especificado' }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Municipio:</label>
                            <p>{{ $iglesia->municipio ? $iglesia->municipio->nombre : 'No especificado' }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Parroquia:</label>
                            <p>{{ $iglesia->parroquia ? $iglesia->parroquia->nombre : 'No especificado' }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Zona:</label>
                            <p>{{ $iglesia->zona ?: 'No especificado' }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Distrito:</label>
                            <p>{{ $iglesia->distrito ?: 'No especificado' }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Latitud:</label>
                            <p>{{ $iglesia->latitud ?: 'No especificado' }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Longitud:</label>
                            <p>{{ $iglesia->longitud ?: 'No especificado' }}</p>
                        </div>

                        <!-- Información adicional -->
                        <div class="col-12 mb-4 mt-4">
                            <h6 class="text-muted mb-3">Información Adicional</h6>
                            <hr>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Descripción:</label>
                            <p>{{ $iglesia->descripcion ?: 'No especificado' }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Estado:</label>
                            <p>
                                @if($iglesia->activa)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Inactiva</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Registrado por:</label>
                            <p>{{ $iglesia->usuarioRegistro ? $iglesia->usuarioRegistro->name : 'No especificado' }}</p>
                        </div>
                        </div>
                    </div>

                        <!-- Tab: Inventario -->
                        <div class="tab-pane fade" id="inventario" role="tabpanel">
                            @can('view iglesias')
                                @livewire('admin.iglesias.inventario.index', ['iglesiaId' => $iglesia->id], key('inventario-iglesia-'.$iglesia->id))
                            @endcan

                            @can('view inventario iglesias')
                                @livewire('admin.iglesias.inventario.index', ['iglesiaId' => $iglesia->id], key('inventario-iglesia-'.$iglesia->id.'-alt'))
                            @endcan

                            @cannot('view iglesias')
                                <div class="alert alert-warning">No tienes permisos para ver el inventario.</div>
                            @endcannot
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
