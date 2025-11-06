<div>
    @can('view student historico')
<div>
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            @if($student->foto)
                                <img src="{{ asset('storage/' . $student->foto) }}" alt="Foto" class="rounded-circle me-3" width="80" height="80">
                            @else
                                <div class="avatar avatar-xl me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($student->nombres, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h4 class="mb-1">{{ $student->nombres }} {{ $student->apellidos }}</h4>
                                <p class="mb-0 text-muted">{{ $student->documento_identidad }} | {{ $student->grado }} - {{ $student->seccion }}</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="btn-group" role="group">
                                <button wire:click="$set('selectedPeriod', '3months')" class="btn btn-sm {{ $selectedPeriod === '3months' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    3 Meses
                                </button>
                                <button wire:click="$set('selectedPeriod', '6months')" class="btn btn-sm {{ $selectedPeriod === '6months' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    6 Meses
                                </button>
                                <button wire:click="$set('selectedPeriod', '1year')" class="btn btn-sm {{ $selectedPeriod === '1year' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    1 Año
                                </button>
                                <button wire:click="$set('selectedPeriod', 'all')" class="btn btn-sm {{ $selectedPeriod === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Todo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded-3">
                                <i class="ri ri-graduation-cap-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">{{ $this->activeEnrollments }}</h4>
                        <p class="mb-0">Matrículas Activas</p>
                        <small class="text-muted">Programas inscritos</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded-3">
                                <i class="ri ri-money-dollar-circle-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">${{ number_format($this->totalPaid, 0) }}</h4>
                        <p class="mb-0">Total Pagado</p>
                        <small class="text-muted">Histórico de pagos</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-warning rounded-3">
                                <i class="ri ri-alarm-warning-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">${{ number_format($this->pendingPayments, 0) }}</h4>
                        <p class="mb-0">Pagos Pendientes</p>
                        <small class="text-muted">Por cobrar</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-info rounded-3">
                                <i class="ri ri-login-box-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">{{ $this->monthlyAccessStats }}</h4>
                        <p class="mb-0">Accesos Este Mes</p>
                        <small class="text-muted">Registros del mes</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Morosidad Row -->
    @if($this->overduePayments > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="ri ri-alarm-warning-line ri-24px me-2"></i>
                <div>
                    <strong>¡Atención!</strong> Este estudiante tiene {{ $this->overduePayments }} cuota(s) vencida(s) que requieren atención inmediata.
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de Cuotas Vencidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-label-danger">
                    <h5 class="mb-1 text-danger">
                        <i class="ri ri-alarm-warning-line me-2"></i>Cuotas Vencidas
                    </h5>
                    <p class="mb-0 text-muted">Pagos pendientes con fecha de vencimiento pasada</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Programa</th>
                                    <th>Cuota #</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Días Vencido</th>
                                    <th>Monto</th>
                                    <th>Saldo Pendiente</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->overduePaymentList as $schedule)
                                <tr>
                                    <td>{{ $schedule->matricula->programa->nombre }}</td>
                                    <td>
                                        <span class="badge bg-label-primary">Cuota {{ $schedule->numero_cuota }}</span>
                                    </td>
                                    <td>{{ $schedule->fecha_vencimiento->format('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $diasVencido = floor($schedule->fecha_vencimiento->diffInDays(now()));
                                            $mesesVencido = floor($diasVencido / 30);
                                            $diasRestantes = $diasVencido % 30;
                                        @endphp
                                        <span class="badge bg-label-danger">
                                            @if($mesesVencido > 0)
                                                {{ $mesesVencido }} mes{{ $mesesVencido > 1 ? 'es' : '' }}, {{ $diasRestantes }} días
                                            @else
                                                {{ $diasVencido }} días
                                            @endif
                                        </span>
                                    </td>
                                    <td class="fw-medium">${{ number_format($schedule->monto, 2) }}</td>
                                    <td class="fw-bold text-danger">
                                        ${{ number_format($schedule->monto - $schedule->monto_pagado, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Access Chart -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Historial de Accesos</h5>
                </div>
                <div class="card-body">
                    <div id="accessChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Payments Chart -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Pagos por Mes</h5>
                </div>
                <div class="card-body">
                    <div id="paymentsChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4">
        <!-- Matriculas -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-1">Matrículas</h5>
                    <p class="mb-0 text-muted">Programas inscritos</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Programa</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($student->matriculas as $matricula)
                                <tr>
                                    <td>{{ $matricula->programa->nombre }}</td>
                                    <td>
                                        <span class="badge bg-label-{{ $matricula->estado === 'activo' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($matricula->estado) }}
                                        </span>
                                    </td>
                                    <td>{{ format_date($matricula->fecha_matricula) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        No hay matrículas registradas
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-1">Pagos Recientes</h5>
                    <p class="mb-0 text-muted">Últimos 10 pagos</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Concepto</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($student->matriculas->flatMap->pagos->sortByDesc('fecha')->take(10) as $pago)
                                <tr>
                                    <td>{{ format_date($pago->fecha) }}</td>
                                    <td>{{ $pago->tipo_pago }}</td>
                                    <td class="fw-medium">@money($pago->total)</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        No hay pagos registrados
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan

<script>
    window.historicoCharts = {};

    function destroyHistoricoCharts() {
        Object.keys(window.historicoCharts).forEach(chartName => {
            try {
                if (window.historicoCharts[chartName]) {
                    window.historicoCharts[chartName].destroy();
                }
            } catch (e) {
                console.log('Error destroying chart:', chartName, e);
            }
        });
        window.historicoCharts = {};
    }

    function renderHistoricoCharts() {
        destroyHistoricoCharts();

        setTimeout(() => {
            // Access Chart
            const accessChartEl = document.querySelector('#accessChart');
            if (accessChartEl) {
                accessChartEl.innerHTML = '';
                const accessData = @json($this->accessData);

                const accessOptions = {
                    series: [{
                        name: 'Accesos',
                        data: accessData.map(item => item.accesos)
                    }],
                    chart: {
                        height: 350,
                        type: 'area',
                        toolbar: { show: false }
                    },
                    colors: ['#28a745'],
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.1
                        }
                    },
                    xaxis: {
                        categories: accessData.map(item => item.date)
                    },
                    yaxis: {
                        min: 0,
                        labels: {
                            formatter: function(val) {
                                return Math.floor(val);
                            }
                        }
                    },
                    legend: { show: false }
                };
                window.historicoCharts.accessChart = new ApexCharts(accessChartEl, accessOptions);
                window.historicoCharts.accessChart.render();
            }

            // Payments Chart
            const paymentsChartEl = document.querySelector('#paymentsChart');
            if (paymentsChartEl) {
                paymentsChartEl.innerHTML = '';
                const paymentData = @json($this->paymentData);

                const paymentsOptions = {
                    series: paymentData.map(item => item.total),
                    chart: {
                        height: 350,
                        type: 'donut'
                    },
                    labels: paymentData.map(item => item.month),
                    colors: ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#00f2fe', '#43e97b'],
                    legend: {
                        position: 'bottom',
                        fontSize: '12px'
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: function(w) {
                                            const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            return '@money(' + total + ')';
                                        }
                                    }
                                }
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return '@money(' + val + ')';
                            }
                        }
                    }
                };
                window.historicoCharts.paymentsChart = new ApexCharts(paymentsChartEl, paymentsOptions);
                window.historicoCharts.paymentsChart.render();
            }
        }, 150);
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderHistoricoCharts();
    });

    document.addEventListener('livewire:init', function () {
        if (typeof Livewire !== 'undefined') {
            Livewire.on('periodChanged', () => {
                setTimeout(() => {
                    renderHistoricoCharts();
                }, 100);
            });
        }
    });

    document.addEventListener('livewire:navigated', function () {
        renderHistoricoCharts();
    });

    document.addEventListener('livewire:update', function () {
        setTimeout(() => {
            renderHistoricoCharts();
        }, 100);
    });
</script>
</div>
