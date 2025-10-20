<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-1">Detalles de Sucursal</h5>
                    <p class="mb-0">Información detallada de la sucursal</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Empresa:</label>
                            <p>{{ $sucursal->empresa->razon_social }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombre:</label>
                            <p>{{ $sucursal->nombre }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Teléfono:</label>
                            <p>{{ $sucursal->telefono ?? 'No especificado' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estado:</label>
                            <p>
                                @if($sucursal->status)
                                    <span class="badge bg-label-success">Activa</span>
                                @else
                                    <span class="badge bg-label-danger">Inactiva</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Dirección:</label>
                            <p>{{ $sucursal->direccion ?? 'No especificada' }}</p>
                        </div>

                        @if($sucursal->latitud && $sucursal->longitud)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Latitud:</label>
                            <p>{{ $sucursal->latitud }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Longitud:</label>
                            <p>{{ $sucursal->longitud }}</p>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Ubicación en el Mapa:</label>
                            <div id="map" style="height: 400px;"></div>
                        </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.sucursales.index') }}" class="btn btn-label-secondary">Volver</a>
                        <a href="{{ route('admin.sucursales.edit', $sucursal->id) }}" class="btn btn-primary">Editar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($sucursal->latitud && $sucursal->longitud)
    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('livewire:init', function () {
            // Inicializar el mapa con la ubicación de la sucursal
            var map = L.map('map').setView([{{ $sucursal->latitud }}, {{ $sucursal->longitud }}], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Marcador en la ubicación de la sucursal
            L.marker([{{ $sucursal->latitud }}, {{ $sucursal->longitud }}])
                .addTo(map)
                .bindPopup('{{ $sucursal->nombre }}')
                .openPopup();
        });
    </script>
    @endpush
    @endif
</div>