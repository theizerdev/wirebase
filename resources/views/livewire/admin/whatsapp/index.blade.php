<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Estado</h6>
                            <h5 class="mb-0">
                                @if($status === 'connected')
                                    <span class="badge bg-success">Conectado</span>
                                @else
                                    <span class="badge bg-danger">Desconectado</span>
                                @endif
                            </h5>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="ri ri-whatsapp-line text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Enviados</h6>
                            <h2 class="mb-0">{{ $stats['sent'] }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="ri ri-send-plane-line text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Recibidos</h6>
                            <h2 class="mb-0">{{ $stats['received'] }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="ri ri-inbox-line text-info" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Fallidos</h6>
                            <h2 class="mb-0">{{ $stats['failed'] }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="ri ri-error-warning-line text-danger" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-pills mb-4" id="whatsappTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="dashboard-tab" data-bs-toggle="pill" data-bs-target="#dashboard" type="button" role="tab">
                <i class="ri ri-dashboard-line me-1"></i> Dashboard
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="conexion-tab" data-bs-toggle="pill" data-bs-target="#conexion" type="button" role="tab">
                <i class="ri ri-plug-line me-1"></i> Conexión
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="mensajes-tab" data-bs-toggle="pill" data-bs-target="#mensajes" type="button" role="tab">
                <i class="ri ri-send-plane-line me-1"></i> Enviar Mensajes
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="whatsappTabContent">
        <!-- Dashboard Tab -->
        <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
            <div class="row">
                @if($user)
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h5 class="card-title mb-0">Información de la Cuenta</h5>
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
                    </div>
                @endif

                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1">Mensajes Recientes</h5>
                                <p class="mb-0">Últimos mensajes enviados y recibidos</p>
                            </div>
                            <button wire:click="refresh" class="btn btn-label-primary">
                                <i class="ri ri-refresh-line"></i> Actualizar
                            </button>
                        </div>
                        <div class="card-datatable table-responsive">
                            @if(count($messages) > 0)
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>De/Para</th>
                                            <th>Mensaje</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($messages as $message)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($message['createdAt'])->format('d/m H:i') }}</td>
                                                <td>
                                                    @if($message['status'] === 'sent')
                                                        <i class="ri ri-arrow-right-line text-success"></i> {{ $message['to'] }}
                                                    @else
                                                        <i class="ri ri-arrow-left-line text-info"></i> {{ $message['from'] }}
                                                    @endif
                                                </td>
                                                <td>{{ Str::limit($message['message'], 50) }}</td>
                                                <td>
                                                    @if($message['status'] === 'sent')
                                                        <span class="badge bg-success">Enviado</span>
                                                    @elseif($message['status'] === 'received')
                                                        <span class="badge bg-info">Recibido</span>
                                                    @elseif($message['status'] === 'failed')
                                                        <span class="badge bg-danger">Fallido</span>
                                                    @else
                                                        <span class="badge bg-warning">{{ $message['status'] }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center p-4">
                                    <i class="ri ri-chat-3-line ri-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay mensajes recientes</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conexión Tab -->
        <div class="tab-pane fade" id="conexion" role="tabpanel">
            <livewire:admin.whatsapp.conexion />
        </div>

        <!-- Mensajes Tab -->
        <div class="tab-pane fade" id="mensajes" role="tabpanel">
            <livewire:admin.whatsapp.envio-mensajes />
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-refresh cada 30 segundos
    setInterval(() => {
        @this.refresh();
    }, 30000);
</script>
@endpush