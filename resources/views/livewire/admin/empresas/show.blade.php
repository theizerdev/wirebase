<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-1">Detalles de Empresa</h5>
                    <p class="mb-0">Información detallada de la empresa</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Razón Social:</label>
                            <p>{{ $empresa->razon_social }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Documento:</label>
                            <p>{{ $empresa->documento }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Representante Legal:</label>
                            <p>{{ $empresa->representante_legal ?? 'No especificado' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estado:</label>
                            <p>
                                @if($empresa->status)
                                    <span class="badge bg-label-success">Activa</span>
                                @else
                                    <span class="badge bg-label-danger">Inactiva</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Dirección:</label>
                            <p>{{ $empresa->direccion ?? 'No especificada' }}</p>
                        </div>

                        @if($empresa->latitud && $empresa->longitud)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Latitud:</label>
                            <p>{{ $empresa->latitud }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Longitud:</label>
                            <p>{{ $empresa->longitud }}</p>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Ubicación en el Mapa:</label>
                            <div id="map" style="height: 400px;"></div>
                        </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.empresas.index') }}" class="btn btn-label-secondary">Volver</a>
                        <a href="{{ route('admin.empresas.edit', $empresa->id) }}" class="btn btn-primary">Editar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($empresa->latitud && $empresa->longitud)
    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('livewire:init', function () {
            // Inicializar el mapa con la ubicación de la empresa
            var map = L.map('map').setView([{{ $empresa->latitud }}, {{ $empresa->longitud }}], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Marcador en la ubicación de la empresa
            L.marker([{{ $empresa->latitud }}, {{ $empresa->longitud }}])
                .addTo(map)
                .bindPopup('{{ $empresa->razon_social }}')
                .openPopup();
        });
    </script>
    @endpush
    @endif
</div>