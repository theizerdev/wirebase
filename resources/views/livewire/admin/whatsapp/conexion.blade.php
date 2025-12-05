<div class="card">
    <div class="card-header border-bottom">
        <h4 class="card-title mb-0">
            <i class="ri ri-whatsapp-line text-success me-2"></i>
            Conexión WhatsApp
        </h4>
    </div>
    <div class="card-body">
        @if($error)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $error }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Estado de Conexión:</label>
                    <div>
                        @if($status === 'connected')
                            <span class="badge bg-success fs-6">
                                <i class="ri ri-check-circle-line"></i> Conectado
                            </span>
                        @elseif($status === 'connecting')
                            <span class="badge bg-warning fs-6">
                                <i class="ri ri-loader-4-line"></i> Conectando...
                            </span>
                        @elseif($status === 'qr_ready')
                            <span class="badge bg-info fs-6">
                                <i class="ri ri-qr-code-line"></i> QR Listo
                            </span>
                        @else
                            <span class="badge bg-danger fs-6">
                                <i class="ri ri-close-circle-line"></i> Desconectado
                            </span>
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-2">
                    @if($status === 'disconnected')
                        <button wire:click="connect" 
                                wire:loading.attr="disabled" 
                                class="btn btn-success">
                            <span wire:loading.remove wire:target="connect">
                                <i class="ri ri-plug-line"></i> Conectar
                            </span>
                            <span wire:loading wire:target="connect">
                                <i class="ri ri-loader-4-line"></i> Conectando...
                            </span>
                        </button>
                    @else
                        <button wire:click="disconnect" class="btn btn-danger">
                            <i class="ri ri-unplug-line"></i> Desconectar
                        </button>
                    @endif

                    <button wire:click="checkStatus" class="btn btn-label-primary">
                        <i class="ri ri-refresh-line"></i> Actualizar
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                @if($qrCode)
                    <div class="text-center">
                        <h6>Escanea este código QR con WhatsApp:</h6>
                        <img src="{{ $qrCode }}" alt="QR Code" class="img-fluid border rounded" style="max-width: 250px;">
                        <div class="alert alert-info mt-3">
                            <small>
                                <i class="ri ri-information-line"></i>
                                Abre WhatsApp → Dispositivos vinculados → Vincular dispositivo
                            </small>
                        </div>
                    </div>
                @elseif($status === 'connecting')
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Generando QR...</span>
                        </div>
                        <p class="mt-2">Generando código QR...</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-refresh cada 5 segundos si está conectando
    @if($status === 'connecting' || $status === 'qr_ready')
        setInterval(() => {
            @this.checkStatus();
        }, 5000);
    @endif
</script>
@endpush