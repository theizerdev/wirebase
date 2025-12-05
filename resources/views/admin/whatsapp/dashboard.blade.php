@extends('layouts.admin')

@section('title', 'WhatsApp Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">WhatsApp API Dashboard</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCompanyModal">
                        <i class="fas fa-plus"></i> Nueva Empresa
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if(isset($error))
                        <div class="alert alert-warning">{{ $error }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>API Key</th>
                                    <th>Estado</th>
                                    <th>Creada</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($companies as $company)
                                <tr>
                                    <td>{{ $company['id'] }}</td>
                                    <td>{{ $company['name'] }}</td>
                                    <td>
                                        <code class="api-key" data-key="{{ $company['apiKey'] }}">
                                            {{ substr($company['apiKey'], 0, 20) }}...
                                        </code>
                                        <button class="btn btn-sm btn-outline-secondary ms-1" onclick="copyApiKey('{{ $company['apiKey'] }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $company['isActive'] ? 'success' : 'danger' }}">
                                            {{ $company['isActive'] ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($company['createdAt'])->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="openWhatsAppControl({{ $company['id'] }}, '{{ $company['apiKey'] }}')">
                                            <i class="fab fa-whatsapp"></i> Control
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay empresas registradas</td>
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

<!-- Modal Crear Empresa -->
<div class="modal fade" id="createCompanyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.whatsapp.create-company') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Empresa WhatsApp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Empresa</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Webhook URL (Opcional)</label>
                        <input type="url" class="form-control" name="webhook_url">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rate Limit (req/min)</label>
                        <input type="number" class="form-control" name="rate_limit" value="60" min="1" max="1000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Empresa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Control WhatsApp -->
<div class="modal fade" id="whatsappControlModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Control WhatsApp - <span id="companyName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Estado de Conexión</h6>
                        <div id="connectionStatus" class="mb-3">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            Verificando estado...
                        </div>
                        
                        <div id="qrSection" style="display: none;">
                            <h6>Código QR</h6>
                            <div id="qrCode" class="text-center mb-3"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Enviar Mensaje de Prueba</h6>
                        <form id="sendMessageForm">
                            <div class="mb-3">
                                <label class="form-label">Número (sin +)</label>
                                <input type="text" class="form-control" id="phoneNumber" placeholder="584121234567">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mensaje</label>
                                <textarea class="form-control" id="messageText" rows="3" placeholder="Mensaje de prueba"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fab fa-whatsapp"></i> Enviar
                            </button>
                        </form>
                        <div id="messageResult" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentCompanyId = null;
let currentApiKey = null;

function copyApiKey(apiKey) {
    navigator.clipboard.writeText(apiKey).then(() => {
        alert('API Key copiada al portapapeles');
    });
}

function openWhatsAppControl(companyId, apiKey) {
    currentCompanyId = companyId;
    currentApiKey = apiKey;
    
    $('#whatsappControlModal').modal('show');
    checkWhatsAppStatus();
}

function checkWhatsAppStatus() {
    $('#connectionStatus').html('<div class="spinner-border spinner-border-sm" role="status"></div> Verificando estado...');
    
    fetch(`/admin/whatsapp/status/${currentCompanyId}/${currentApiKey}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const status = data.isConnected ? 'Conectado' : 'Desconectado';
                const badgeClass = data.isConnected ? 'success' : 'danger';
                
                $('#connectionStatus').html(`
                    <span class="badge bg-${badgeClass}">${status}</span>
                    <div class="mt-2">
                        <small>Estado: ${data.connectionState}</small>
                    </div>
                `);
                
                if (!data.isConnected && data.connectionState === 'qr_ready') {
                    loadQRCode();
                }
            } else {
                $('#connectionStatus').html(`<span class="badge bg-danger">Error: ${data.error}</span>`);
            }
        })
        .catch(error => {
            $('#connectionStatus').html(`<span class="badge bg-danger">Error de conexión</span>`);
        });
}

function loadQRCode() {
    fetch(`/admin/whatsapp/qr/${currentCompanyId}/${currentApiKey}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.qr) {
                $('#qrCode').html(`<img src="${data.qr}" class="img-fluid" alt="QR Code">`);
                $('#qrSection').show();
            }
        });
}

$('#sendMessageForm').on('submit', function(e) {
    e.preventDefault();
    
    const phone = $('#phoneNumber').val();
    const message = $('#messageText').val();
    
    if (!phone || !message) {
        alert('Por favor completa todos los campos');
        return;
    }
    
    $('#messageResult').html('<div class="spinner-border spinner-border-sm" role="status"></div> Enviando...');
    
    fetch(`/admin/whatsapp/send/${currentCompanyId}/${currentApiKey}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify({
            to: phone,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#messageResult').html('<div class="alert alert-success">Mensaje enviado exitosamente</div>');
            $('#sendMessageForm')[0].reset();
        } else {
            $('#messageResult').html(`<div class="alert alert-danger">Error: ${data.error}</div>`);
        }
    })
    .catch(error => {
        $('#messageResult').html('<div class="alert alert-danger">Error de conexión</div>');
    });
});
</script>
@endpush