<div>
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Conexión WhatsApp</h4>
                        <p class="text-muted mb-0">Gestiona tu conexión con WhatsApp Business API</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button wire:click="checkConnection" class="btn btn-outline-primary" {{ $isConnecting ? 'disabled' : '' }}>
                            <i class="ri ri-refresh-line me-2"></i>
                            Actualizar Estado
                        </button>
                        <a href="{{ route('admin.whatsapp.dashboard') }}" class="btn btn-secondary">
                            <i class="ri ri-arrow-left-line me-2"></i>
                            Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-lg me-3">
                                        <span class="avatar-initial rounded bg-{{ $this->statusColor }}">
                                            <i class="{{ $this->statusIcon }} mdi-24px"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ $this->statusMessage }}</h5>
                                        @if($status === 'connected' && $user)
                                            <p class="text-muted mb-0">
                                                <i class="ri ri-user-line me-1"></i>
                                                Conectado como: <strong>{{ $user['name'] ?? 'Usuario' }}</strong>
                                                @if($lastSeen)
                                                    <br><i class="ri ri-time-line me-1"></i>
                                                    Última vez visto: {{ \Carbon\Carbon::parse($lastSeen)->diffForHumans() }}
                                                @endif
                                            </p>
                                        @elseif($status === 'error')
                                            <p class="text-danger mb-0">
                                                <i class="ri ri-error-warning-line me-1"></i>
                                                {{ $connectionError }}
                                            </p>
                                        @elseif($status === 'service_unavailable')
                                            <p class="text-warning mb-0">
                                                <i class="ri ri-wifi-off-line me-1"></i>
                                                El servicio de WhatsApp no está disponible. Verifica que el servicio esté ejecutándose.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="d-flex gap-2 justify-content-md-end">
                                    @if($status === 'disconnected' || $status === 'error' || $status === 'service_unavailable')
                                        <button wire:click="startConnection" class="btn btn-success" {{ $isConnecting ? 'disabled' : '' }}>
                                            @if($isConnecting)
                                                <span class="spinner-border spinner-border-sm me-2"></span>
                                                Conectando...
                                            @else
                                                <i class="ri ri-link me-2"></i>
                                                Conectar WhatsApp
                                            @endif
                                        </button>
                                    @elseif($status === 'connected')
                                        <button wire:click="disconnect" class="btn btn-danger" onclick="confirm('¿Estás seguro de desconectar WhatsApp?') || event.stopImmediatePropagation()">
                                            <i class="ri ri-links-line me-2"></i>
                                            Desconectar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code Section -->
        @if($status === 'qr_ready' && $qrCode)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="card-title mb-0">
                                <i class="ri ri-qr-code-line me-2"></i>
                                Código QR de WhatsApp
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <div class="qr-code-container d-inline-block p-3 bg-white rounded shadow-sm">
                                    <img src="{{ $qrCode }}" alt="Código QR WhatsApp" class="img-fluid" style="max-width: 300px;">
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <h6><i class="ri ri-information-line me-2"></i>Instrucciones:</h6>
                                <ol class="mb-0 text-start">
                                    <li>Abre WhatsApp en tu teléfono</li>
                                    <li>Toca el menú (tres puntos) → WhatsApp Web</li>
                                    <li>Escanea este código QR</li>
                                    <li>La conexión se establecerá automáticamente</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Connection Info -->
        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="ri ri-information-line me-2"></i>
                            Información de Conexión
                        </h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Estado:</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-{{ $this->statusColor }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </dd>
                            
                            @if($user)
                                <dt class="col-sm-4">Usuario:</dt>
                                <dd class="col-sm-8">{{ $user['name'] ?? 'N/A' }}</dd>
                                
                                <dt class="col-sm-4">Número:</dt>
                                <dd class="col-sm-8">{{ $user['id'] ?? 'N/A' }}</dd>
                            @endif
                            
                            @if($lastSeen)
                                <dt class="col-sm-4">Última vez:</dt>
                                <dd class="col-sm-8">{{ \Carbon\Carbon::parse($lastSeen)->format('d/m/Y H:i') }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="ri ri-lightbulb-line me-2"></i>
                            Ayuda
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($status === 'disconnected')
                            <p class="text-muted mb-3">
                                Haz clic en "Conectar WhatsApp" para iniciar el proceso de conexión.
                            </p>
                            <ul class="text-muted small mb-0">
                                <li>Asegúrate de tener WhatsApp instalado en tu teléfono</li>
                                <li>El teléfono debe tener conexión a internet</li>
                                <li>Escanea el código QR cuando aparezca</li>
                            </ul>
                        @elseif($status === 'qr_ready')
                            <p class="text-muted mb-3">
                                Escanea el código QR con WhatsApp para completar la conexión.
                            </p>
                            <div class="alert alert-warning small mb-0">
                                <i class="ri ri-time-line me-1"></i>
                                El código QR expira en pocos minutos.
                            </div>
                        @elseif($status === 'connecting')
                            <p class="text-muted mb-3">
                                Estableciendo conexión con WhatsApp...
                            </p>
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        @elseif($status === 'connected')
                            <p class="text-muted mb-3">
                                ¡Conexión establecida exitosamente! Ahora puedes enviar y recibir mensajes.
                            </p>
                            <div class="alert alert-success small mb-0">
                                <i class="ri ri-check-line me-1"></i>
                                La conexión está activa y funcionando.
                            </div>
                        @elseif($status === 'error')
                            <p class="text-danger mb-3">
                                Ha ocurrido un error en la conexión.
                            </p>
                            <button wire:click="checkConnection" class="btn btn-sm btn-outline-primary">
                                <i class="ri ri-refresh-line me-1"></i>
                                Reintentar
                            </button>
                        @elseif($status === 'service_unavailable')
                            <p class="text-warning mb-3">
                                El servicio de WhatsApp API no está disponible.
                            </p>
                            <div class="alert alert-warning small mb-0">
                                <i class="ri ri-information-line me-1"></i>
                                Verifica que el servicio esté ejecutándose en el servidor.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Messages -->
    @if (session()->has('success'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri ri-check-line me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ri ri-error-warning-line me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Auto-refresh Script -->
    @if($status === 'connecting' || $status === 'qr_ready')
        <script>
            document.addEventListener('livewire:init', function() {
                setInterval(function() {
                    Livewire.find('{{ $this->getId() }}').call('checkConnection');
                }, 5000); // Actualizar cada 5 segundos durante conexión
            });
        </script>
    @endif

    <style>
        .qr-code-container {
            position: relative;
            display: inline-block;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .qr-code-container::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            z-index: 1;
        }
        
        .qr-code-container img {
            position: relative;
            z-index: 2;
        }
    </style>
</div>