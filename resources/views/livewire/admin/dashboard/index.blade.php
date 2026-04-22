<div wire:poll.60s="refresh">
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri ri-check-line me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Panel de Control</h4>
                <small class="text-muted">Resumen ejecutivo y alertas</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-label-primary" wire:click="refresh">
                    <i class="ri ri-refresh-line me-1"></i> Actualizar
                </button>
                <button class="btn btn-label-success" wire:click="exportExcel">
                    <i class="mdi mdi-file-excel me-1"></i> Exportar Alertas
                </button>
                <button class="btn btn-label-danger" wire:click="exportPdf">
                    <i class="ri ri-file-pdf-2-line me-1"></i> Exportar PDF
                </button>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h6 class="mb-0">Filtros</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" wire:model.lazy="dateFrom">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" wire:model.lazy="dateTo">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Monto mínimo</label>
                    <input type="number" step="0.01" class="form-control" wire:model.lazy="minAmount" placeholder="0.00">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Monto máximo</label>
                    <input type="number" step="0.01" class="form-control" wire:model.lazy="maxAmount" placeholder="0.00">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $metrics['clientes']['total'] ?? 0 }}</h5>
                            <small class="text-muted">Clientes Totales</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-team-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <span class="badge bg-label-success">Activos {{ $metrics['clientes']['activos'] ?? 0 }}</span>
                        <span class="badge bg-label-secondary">Inactivos {{ $metrics['clientes']['inactivos'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $metrics['contratos']['vigentes'] ?? 0 }}</h5>
                            <small class="text-muted">Contratos Vigentes</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-file-paper-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <span class="badge bg-label-danger">Vencidos {{ $metrics['contratos']['vencidos'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $metrics['pagos']['recibidos'] ?? 0 }}</h5>
                            <small class="text-muted">Pagos Recibidos</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-money-dollar-circle-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <span class="badge bg-label-warning">Pendientes {{ $metrics['pagos']['pendientes'] ?? 0 }}</span>
                        <span class="badge bg-label-danger">Morosos {{ $metrics['pagos']['morosos'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">${{ number_format($metrics['ingresos']['mensual'] ?? 0, 2) }}</h5>
                            <small class="text-muted">Ingresos Mensuales</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-bar-chart-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">Anual: ${{ number_format($metrics['ingresos']['anual'] ?? 0, 2) }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Tendencia de Ingresos</h6>
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0">Anticipación (días)</label>
                        <input type="number" class="form-control form-control-sm" style="width: 90px" wire:model.live="anticipationDays" min="1" max="60">
                    </div>
                </div>
                <div class="card-body">
                    <div id="dashboardIncomeTrend" style="min-height: 260px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Cuotas próximas a vencerse</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>#</th>
                                    <th>Vence</th>
                                    <th class="text-end">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cuotas as $c)
                                    <tr>
                                        <td>{{ optional(optional($c->contrato)->cliente)->nombre }} {{ optional(optional($c->contrato)->cliente)->apellido }}</td>
                                        <td>{{ $c->numero_cuota }}</td>
                                        <td><span class="badge bg-label-{{ $c->estado === 'vencido' ? 'danger' : 'warning' }}">{{ optional($c->fecha_vencimiento)->format('d/m/Y') }}</span></td>
                                        <td class="text-end">${{ number_format($c->saldo_pendiente, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted text-center">Sin cuotas próximas</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $cuotas->links('livewire.pagination') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Contratos por vencer</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Contrato</th>
                                    <th>Cliente</th>
                                    <th>Vence</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contratos as $ct)
                                    <tr>
                                        <td>{{ $ct->numero_contrato }}</td>
                                        <td>{{ $ct->cliente->nombre }} {{ $ct->cliente->apellido }}</td>
                                        <td><span class="badge bg-label-danger">{{ optional($ct->fecha_fin_estimada)->format('d/m/Y') }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted text-center">Sin vencimientos próximos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $contratos->links('livewire.pagination') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Clientes con pendientes</h6>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control form-control-sm" placeholder="Buscar cliente..." wire:model.live.debounce.300ms="searchClient" style="max-width: 220px;">
                        <button class="btn btn-sm btn-primary" wire:click="notifyWhatsApp"><i class="ri ri-whatsapp-line me-1"></i> Notificar</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Documento</th>
                                    <th class="text-center">Seleccionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clientes as $cl)
                                    <tr>
                                        <td>{{ $cl->nombre }} {{ $cl->apellido }}</td>
                                        <td>{{ $cl->documento }}</td>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input" wire:model="selectedClients" value="{{ $cl->id }}">
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted text-center">Sin clientes pendientes</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        {{ $clientes->links('livewire.pagination') }}
                        <button class="btn btn-sm btn-label-secondary" wire:click="scheduleReminders"><i class="ri ri-time-line me-1"></i> Programar envíos</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('materialize/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script>
    let _trendChart = null;
    function renderIncomeTrend(labels, values) {
        const el = document.querySelector('#dashboardIncomeTrend');
        if (!el) return;
        if (_trendChart) {
            _trendChart.updateOptions({
                xaxis: { categories: labels },
                series: [{ name: 'Ingresos', data: values }]
            });
            return;
        }
        _trendChart = new ApexCharts(el, {
            chart: { type: 'area', height: 260, toolbar: { show: false } },
            series: [{ name: 'Ingresos', data: values }],
            xaxis: { categories: labels },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            yaxis: { labels: { formatter: val => '$' + Number(val).toFixed(0) } },
            colors: ['#7367F0'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 0.3, opacityFrom: 0.6, opacityTo: 0.1 } },
            tooltip: { y: { formatter: val => '$' + Number(val).toFixed(2) } }
        });
        _trendChart.render();
    }
    document.addEventListener('DOMContentLoaded', () => {
        renderIncomeTrend(
            @json($monthlyTrend['labels'] ?? []),
            @json($monthlyTrend['values'] ?? [])
        );
    });
    document.addEventListener('livewire:init', () => {
        Livewire.on('trendUpdated', ([data]) => {
            renderIncomeTrend(data.labels || [], data.values || []);
        });
    });
    </script>
</div>
