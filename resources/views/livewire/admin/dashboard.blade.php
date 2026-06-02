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
            <h2 class="fw-semibold"><i class="ri ri-dashboard-line me-2"></i>Dashboard administrativo</h2>
            <p class="mt-1">Resumen en tiempo real del rendimiento del programa de beneficiarios.</p>
        </div>
        <button class="btn btn-light btn-sm" wire:click="loadDashboardData">
            <i class="ri ri-refresh-line me-1"></i>Actualizar datos
        </button>
    </div>

    <div class="row g-3 mb-4">
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
                                        <div class="item-meta">Responsable: {{ $beneficiario['responsable'] }}</div>
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

    @push('scripts')
    <script>
        function initDashboardChart(labels, data) {
            labels = Array.isArray(labels) ? labels : [];
            var series = Array.isArray(data) ? data.map(function (value) { return Number(value) || 0; }) : [];
            var el = document.querySelector('#dashboardChart');
            if (!el) {
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

            if (window.dashboardChart && typeof window.dashboardChart.updateOptions === 'function') {
                window.dashboardChart.updateOptions({ xaxis: { categories: labels } });
                window.dashboardChart.updateSeries([{ data: series }]);
            } else {
                window.dashboardChart = new ApexCharts(el, config);
                window.dashboardChart.render();
            }
        }

        initDashboardChart(@json($chartData['labels']), @json($chartData['data']));

        document.addEventListener('livewire:init', function () {
            Livewire.on('chartDataUpdated', function (payload) {
                if (payload && payload.chartData) {
                    initDashboardChart(payload.chartData.labels, payload.chartData.data || []);
                }
            });
        });

        setInterval(function () {
            @this.loadDashboardData();
        }, 300000);
    </script>
    @endpush
</div>
