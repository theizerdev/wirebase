<div>
    <!-- Estadísticas principales -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">Estado</h6>
                            <h5 class="mb-0">
                                @if($status === 'connected')
                                    <i class="fas fa-check-circle"></i> Conectado
                                @else
                                    <i class="fas fa-times-circle"></i> Desconectado
                                @endif
                            </h5>
                        </div>
                        <div class="align-self-center">
                            <i class="fab fa-whatsapp fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">Enviados</h6>
                            <h4 class="mb-0">{{ number_format($stats['sent']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-paper-plane fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">Entregados</h6>
                            <h4 class="mb-0">{{ number_format($stats['delivered']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-double fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">Pendientes</h6>
                            <h4 class="mb-0">{{ number_format($stats['pending']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">Fallidos</h6>
                            <h4 class="mb-0">{{ number_format($stats['failed']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">Total</h6>
                            <h4 class="mb-0">{{ number_format($stats['total']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-bar fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Últimos 7 días</h6>
                </div>
                <div class="card-body">
                    <canvas id="dailyChart" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Últimas 4 semanas</h6>
                </div>
                <div class="card-body">
                    <canvas id="weeklyChart" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Últimos 6 meses</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top destinatarios y actividad reciente -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Top Destinatarios</h6>
                </div>
                <div class="card-body">
                    @if(count($topRecipients) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Destinatario</th>
                                        <th>Mensajes</th>
                                        <th>Último</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topRecipients as $recipient)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $recipient['name'] }}</strong><br>
                                                    <small class="text-muted">{{ $recipient['phone'] }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $recipient['total_messages'] }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $recipient['last_message'] }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <p>No hay destinatarios registrados</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Actividad Reciente (24h)</h6>
                </div>
                <div class="card-body">
                    @if(count($recentActivity) > 0)
                        <div class="timeline">
                            @foreach($recentActivity as $activity)
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            @if($activity['status'] === 'sent')
                                                <div class="bg-success rounded-circle p-2">
                                                    <i class="fas fa-check text-white"></i>
                                                </div>
                                            @elseif($activity['status'] === 'failed')
                                                <div class="bg-danger rounded-circle p-2">
                                                    <i class="fas fa-times text-white"></i>
                                                </div>
                                            @else
                                                <div class="bg-warning rounded-circle p-2">
                                                    <i class="fas fa-clock text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="mb-1">{{ $activity['action'] }}</p>
                                            <small class="text-muted">{{ $activity['time'] }} • {{ $activity['user'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-history fa-2x mb-2"></i>
                            <p>No hay actividad reciente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($user)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Información de la Cuenta WhatsApp</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Número:</strong> {{ $user['id'] ?? 'N/A' }}</p>
                        <p><strong>Nombre:</strong> {{ $user['name'] ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Última conexión:</strong> 
                            {{ $lastSeen ? \Carbon\Carbon::parse($lastSeen)->diffForHumans() : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Mensajes recientes -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">Mensajes Recientes</h6>
            <button wire:click="refresh" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-sync"></i> Actualizar
            </button>
        </div>
        <div class="card-body">
            @if(count($messages) > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Destinatario</th>
                                <th>Mensaje</th>
                                <th>Estado</th>
                                <th>Creado por</th>
                                <th>Reintentos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                                <tr>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($message['created_at'])->format('d/m H:i') }}</small>
                                        @if($message['sent_at'])
                                            <br><small class="text-muted">Env: {{ \Carbon\Carbon::parse($message['sent_at'])->format('H:i') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $message['recipient_name'] ?: 'Sin nombre' }}</strong><br>
                                            <small class="text-muted">{{ $message['recipient_phone'] }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span title="{{ $message['message_content'] }}">
                                            {{ Str::limit($message['message_content'], 40) }}
                                        </span>
                                        @if($message['error_message'])
                                            <br><small class="text-danger">{{ Str::limit($message['error_message'], 30) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($message['status'] === 'sent')
                                            <span class="badge bg-success">Enviado</span>
                                        @elseif($message['status'] === 'delivered')
                                            <span class="badge bg-info">Entregado</span>
                                        @elseif($message['status'] === 'failed')
                                            <span class="badge bg-danger">Fallido</span>
                                        @elseif($message['status'] === 'pending')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($message['status']) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $message['creator'] }}</small>
                                    </td>
                                    <td>
                                        @if($message['retry_count'] > 0)
                                            <span class="badge bg-warning">{{ $message['retry_count'] }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-comments fa-3x mb-3"></i>
                    <p>No hay mensajes registrados</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Auto-refresh cada 30 segundos
    setInterval(() => {
        @this.refresh();
    }, 30000);

    // Inicializar gráficos cuando se carga la página
    document.addEventListener('DOMContentLoaded', function() {
        initCharts();
    });

    // Reinicializar gráficos después de refresh
    document.addEventListener('livewire:updated', function() {
        initCharts();
    });

    function initCharts() {
        // Gráfico diario
        const dailyCtx = document.getElementById('dailyChart');
        if (dailyCtx) {
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: @json(collect($dailyStats)->pluck('date')),
                    datasets: [{
                        label: 'Enviados',
                        data: @json(collect($dailyStats)->pluck('sent')),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Fallidos',
                        data: @json(collect($dailyStats)->pluck('failed')),
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Gráfico semanal
        const weeklyCtx = document.getElementById('weeklyChart');
        if (weeklyCtx) {
            new Chart(weeklyCtx, {
                type: 'bar',
                data: {
                    labels: @json(collect($weeklyStats)->pluck('week')),
                    datasets: [{
                        label: 'Enviados',
                        data: @json(collect($weeklyStats)->pluck('sent')),
                        backgroundColor: '#28a745'
                    }, {
                        label: 'Fallidos',
                        data: @json(collect($weeklyStats)->pluck('failed')),
                        backgroundColor: '#dc3545'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Gráfico mensual
        const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'doughnut',
                data: {
                    labels: @json(collect($monthlyStats)->pluck('month')),
                    datasets: [{
                        data: @json(collect($monthlyStats)->pluck('sent')),
                        backgroundColor: [
                            '#007bff', '#28a745', '#ffc107', 
                            '#dc3545', '#6f42c1', '#fd7e14'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }
</script>

<style>
.timeline-item {
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 16px;
    top: 40px;
    width: 2px;
    height: calc(100% - 20px);
    background-color: #e9ecef;
}

.opacity-75 {
    opacity: 0.75;
}
</style>