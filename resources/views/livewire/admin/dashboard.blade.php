<div>
    @push('styles')
    <style>
        .dashboard-hero {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #fff;
            border-radius: 0.75rem;
            padding: 1.4rem 1.6rem;
        }

        .dashboard-hero h2,
        .dashboard-hero p {
            margin: 0;
        }

        .stat-card {
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 0.75rem;
            padding: 1rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            background: #fff;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 0.95rem;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .stat-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.2rem;
        }

        .stat-value {
            font-size: 1.55rem;
            font-weight: 700;
            line-height: 1.1;
        }

        .dashboard-card {
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
        }

        .dashboard-card .card-header {
            background: #f8fafc;
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            padding: 1rem 1.25rem;
        }

        .dashboard-card .card-header h5 {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
        }

        .dashboard-card .card-body {
            padding: 1.25rem;
        }

        .alert-item,
        .beneficiario-item,
        .responsable-item {
            padding: 0.95rem 1rem;
            border-radius: 0.75rem;
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.06);
            margin-bottom: 0.75rem;
            transition: background-color 0.15s ease, transform 0.15s ease;
        }

        .alert-item:hover,
        .beneficiario-item:hover,
        .responsable-item:hover {
            background-color: #f8fafc;
            transform: translateY(-1px);
        }

        .alert-item:last-child,
        .beneficiario-item:last-child,
        .responsable-item:last-child {
            margin-bottom: 0;
        }

        .item-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #64748b;
            margin-bottom: 0.2rem;
        }

        .item-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .item-meta {
            font-size: 0.85rem;
            color: #475569;
        }
    </style>
    @endpush

    <div class="dashboard-hero mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h2 class="fw-semibold"><i class="ri ri-home-heart-line me-2"></i>Dashboard de Casas de Alimentación</h2>
            <p class="mt-1">Resumen en tiempo real del rendimiento y beneficiarios por casa de alimentación.</p>
        </div>
        <button class="btn btn-light btn-sm" wire:click="loadDashboardData">
            <i class="ri ri-refresh-line me-1"></i>Actualizar datos
        </button>
    </div>

    {{-- Filtros por Estado y Casa de Alimentación --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">
                        <i class="ri ri-map-pin-line me-1 text-primary"></i>Filtrar por Estado
                    </label>
                    <select class="form-select form-select-sm"
                            wire:model.change="filtro_estado_id">
                        <option value="">-- Seleccionar Estado --</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">
                        <i class="ri ri-home-heart-line me-1 text-success"></i>Filtrar por Casa de Alimentación
                    </label>
                    <select class="form-select form-select-sm"
                            wire:model.change="filtro_casa_alimentacion_id"
                            {{ !$filtro_estado_id ? 'disabled' : '' }}>
                        <option value="">-- Seleccionar Casa de Alimentación --</option>
                        @if($filtro_estado_id)
                            @foreach($casasAlimentacion as $casa)
                                <option value="{{ $casa->id }}">{{ $casa->codigo.' '.$casa->parroquia }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary btn-sm w-100"
                            wire:click="limpiarFiltros"
                            title="Limpiar filtros">
                        <i class="ri ri-close-line me-1"></i>Limpiar
                    </button>
                </div>
            </div>
            @if($filtro_estado_id || $filtro_casa_alimentacion_id)
                <div class="alert alert-info mt-3 mb-0 py-2 px-3 small">
                    <i class="ri ri-information-line me-1"></i>
                    Mostrando datos filtrados:
                    @if($filtro_casa_alimentacion_id)
                        <strong>Casa de Alimentación:</strong> {{ $casasAlimentacion->where('id', $filtro_casa_alimentacion_id)->first()->codigo ?? 'N/A' }}
                    @elseif($filtro_estado_id)
                        <strong>Estado:</strong> {{ $estados->where('id', $filtro_estado_id)->first()->codigo ?? 'N/A' }}
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-4">
         <div class="col-sm-12 col-xl-12">
            <div class="stat-card">
                <div class="stat-icon" style="background: #dbeafe; color: #2563eb;"><i class="ri ri-home-heart-line"></i></div>
                <div>
                    <div class="stat-label">Casas de Alimentación</div>
                    <div class="stat-value">{{ $stats['total_casas_alimentacion'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e0e7ff; color: #4338ca;"><i class="ri ri-group-line"></i></div>
                <div>
                    <div class="stat-label">Beneficiarios</div>
                    <div class="stat-value">{{ $stats['total_beneficiarios'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: #dcfce7; color: #16a34a;"><i class="ri ri-user-shared-line"></i></div>
                <div>
                    <div class="stat-label">Con responsable</div>
                    <div class="stat-value">{{ $stats['con_responsable'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: #fee2e2; color: #b91c1c;"><i class="ri ri-wheelchair-line"></i></div>
                <div>
                    <div class="stat-label">Con discapacidad</div>
                    <div class="stat-value">{{ $stats['con_discapacidad'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: #fef9c3; color: #ca8a04;"><i class="ri ri-nurse-line"></i></div>
                <div>
                    <div class="stat-label">Embarazadas</div>
                    <div class="stat-value">{{ $stats['embarazadas'] }}</div>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="dashboard-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="ri ri-bar-chart-box-line me-2 text-primary"></i>Distribución por edad</h5>
                    <small class="text-muted">Últimos datos registrados</small>
                </div>

                <div class="card-body" wire:ignore>
                    <div id="dashboardChart" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="ri ri-notification-badge-line me-2 text-warning"></i>Alertas clave</h5>
                    <small class="text-muted">Acciones prioritarias</small>
                </div>
                <div class="card-body">
                    @if(count($alerts) > 0)
                        @foreach($alerts as $alert)
                            <div class="alert-item" style="border-left-color: {{ $alert['color'] }};">
                                <div class="d-flex align-items-start gap-3">
                                    <div style="width: 42px; height: 42px; border-radius: 0.75rem; background: {{ $alert['color'] }}15; display: flex; align-items: center; justify-content: center;">
                                        <i class="{{ $alert['icon'] }}" style="font-size: 1.25rem; color: {{ $alert['color'] }};"></i>
                                    </div>
                                    <div>
                                        <div class="item-title">{{ $alert['title'] }}</div>
                                        <div class="item-meta">{{ $alert['message'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="ri ri-checkbox-circle-line" style="font-size: 3rem; color: #10b981; opacity: 0.45;"></i>
                            <p class="text-muted mt-3 mb-0">No hay alertas pendientes.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="dashboard-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="ri ri-file-line me-2 text-primary"></i>Beneficiarios recientes</h5>
                    <a href="{{ route('admin.beneficiarios.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
                </div>
                <div class="card-body">
                    @if(count($recentBeneficiarios) > 0)
                        @foreach($recentBeneficiarios as $beneficiario)
                            <div class="beneficiario-item">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="item-title">{{ $beneficiario['nombre'] }}</div>
                                        <div class="item-meta">
                                            <i class="ri ri-home-heart-line me-1 text-success"></i>{{ $beneficiario['casa_alimentacion'] }}
                                        </div>
                                        <div class="item-meta small text-muted">Responsable: {{ $beneficiario['responsable'] }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="item-label">Edad</div>
                                        <div class="fw-semibold">{{ $beneficiario['edad'] ?? '—' }}</div>
                                        <div class="text-muted" style="font-size: .82rem;">{{ $beneficiario['created_at'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">No hay beneficiarios recientes registrados.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="ri ri-user-follow-line me-2 text-success"></i>Top responsables</h5>
                    <a href="{{ route('admin.responsables.index') }}" class="btn btn-sm btn-outline-success">Ver responsables</a>
                </div>
                <div class="card-body">
                    @if(count($topResponsables) > 0)
                        @foreach($topResponsables as $responsable)
                            <div class="responsable-item d-flex align-items-center justify-content-between gap-3">
                                <div>
                                    <div class="item-title">{{ $responsable['nombre'] }}</div>
                                    <div class="item-meta">{{ $responsable['telefono'] ?? 'Teléfono no disponible' }} {{ $responsable['estado']['nombre'] ?? 'Estado no disponible' }}</div>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $responsable['candidatos'] }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">No hay responsables con beneficiarios asignados.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Todos los Responsables con Beneficiarios (como el export de Excel) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="ri ri-team-line me-2 text-info"></i>Todos los Responsables con Beneficiarios</h5>
                    <span class="badge bg-info">{{ count($responsablesConBeneficiarios) }} responsables</span>
                </div>
                <div class="card-body">
                    @if(count($responsablesConBeneficiarios) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>Responsable</th>
                                        <th style="width: 150px;" class="text-center">Beneficiarios</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach($responsablesConBeneficiarios as $nombre => $cantidad)
                                        <tr>
                                            <td>{{ $index++ }}</td>
                                            <td>
                                                <strong>{{ $nombre }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill fs-6">{{ $cantidad }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="ri ri-user-unfollow-line ri-lg mb-2"></i>
                            <p>No hay responsables con beneficiarios asignados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function initDashboardChart(labels, data) {
            console.log('initDashboardChart called with:', { labels, data });

            labels = Array.isArray(labels) ? labels : [];
            var series = Array.isArray(data) ? data.map(function (value) { return Number(value) || 0; }) : [];
            var el = document.querySelector('#dashboardChart');

            if (!el) {
                console.warn('Chart element #dashboardChart not found');
                return;
            }

            var config = {
                chart: {
                    height: 340,
                    type: 'bar',
                    toolbar: { show: false }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        columnWidth: '45%'
                    }
                },
                dataLabels: { enabled: false },
                colors: ['#4338ca'],
                series: [{ name: 'Beneficiarios', data: series }],
                xaxis: { categories: labels },
                yaxis: { min: 0, tickAmount: 4 },
                tooltip: {
                    theme: 'light',
                    y: { formatter: function (val) { return val + ' personas'; } }
                }
            };

            try {
                if (window.dashboardChart && typeof window.dashboardChart.updateOptions === 'function') {
                    console.log('Updating existing chart');
                    console.log('Current series:', window.dashboardChart.series);
                    console.log('New series data:', series);

                    // Actualizar categorías del eje X
                    window.dashboardChart.updateOptions({
                        xaxis: { categories: labels }
                    }, false, true);

                    // Actualizar serie de datos - usar el método correcto
                    window.dashboardChart.updateSeries([{
                        name: 'Beneficiarios',
                        data: series
                    }], true);

                    console.log('Chart updated successfully');
                } else {
                    console.log('Creating new chart');
                    if (window.dashboardChart) {
                        window.dashboardChart.destroy();
                    }
                    window.dashboardChart = new ApexCharts(el, config);
                    window.dashboardChart.render();
                    console.log('New chart created and rendered');
                }
            } catch (error) {
                console.error('Error updating/creating chart:', error);
                console.error('Error details:', error.message, error.stack);
                // Si hay error, destruir y recrear
                if (window.dashboardChart) {
                    try {
                        window.dashboardChart.destroy();
                    } catch (e) {
                        console.warn('Could not destroy old chart:', e);
                    }
                }
                window.dashboardChart = new ApexCharts(el, config);
                window.dashboardChart.render();
                console.log('Chart recreated after error');
            }
        }

        initDashboardChart(@json($chartData['labels']), @json($chartData['data']));

        document.addEventListener('livewire:init', function () {
            console.log('Livewire initialized, setting up chart listener');

            Livewire.on('chartDataUpdated', function (payload) {
                console.log('=== chartDataUpdated event received ===');
                console.log('Full payload:', JSON.stringify(payload));

                // En Livewire 3, el payload puede venir como array [0] o como objeto
                var eventData = Array.isArray(payload) ? payload[0] : payload;
                console.log('Extracted event data:', eventData);

                if (eventData && eventData.chartData) {
                    console.log('Chart data labels:', eventData.chartData.labels);
                    console.log('Chart data values:', eventData.chartData.data);

                    initDashboardChart(eventData.chartData.labels, eventData.chartData.data || []);
                    console.log('Chart updated successfully');
                } else {
                    console.warn('No chartData in payload:', eventData);
                }
            });

            console.log('Chart listener registered');
        });

        setInterval(function () {
            @this.loadDashboardData();
        }, 300000);
    </script>
    @endpush
</div>
