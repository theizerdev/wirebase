<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">🌍 Prueba de Configuración Regional</h5>
        </div>
        <div class="card-body">
            @if(session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Seleccionar Empresa:</label>
                    <select class="form-select" wire:change="selectEmpresa($event.target.value)">
                        <option value="">-- Seleccione una empresa --</option>
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id }}" {{ $selectedEmpresaId == $empresa->id ? 'selected' : '' }}>
                                {{ $empresa->razon_social }} ({{ $empresa->pais->nombre ?? 'Sin país' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">📊 Configuración Actual</h6>
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td><strong>Moneda:</strong></td>
                                    <td>{{ $currentConfig['currency'] ?? 'No definida' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Símbolo:</strong></td>
                                    <td>{{ $currentConfig['currency_symbol'] ?? 'No definido' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Zona Horaria:</strong></td>
                                    <td>{{ $currentConfig['timezone'] ?? 'No definida' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Formato Fecha:</strong></td>
                                    <td>{{ $currentConfig['date_format'] ?? 'No definido' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Idioma:</strong></td>
                                    <td>{{ $currentConfig['locale'] ?? 'No definido' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">🧪 Pruebas de Formateo</h6>
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td><strong>Monto Original:</strong></td>
                                    <td>{{ $testAmount }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Monto Formateado:</strong></td>
                                    <td class="text-success fw-bold">{{ $formattedAmount }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha Original:</strong></td>
                                    <td>{{ $testDate->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha Formateada:</strong></td>
                                    <td class="text-success fw-bold">{{ $formattedDate }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">💡 Notas:</h6>
                        <ul class="mb-0">
                            <li>Selecciona una empresa para ver cómo cambia la configuración regional</li>
                            <li>El indicador de configuración regional en la barra de navegación también se actualizará</li>
                            <li>Los cambios se aplican inmediatamente a toda la aplicación</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
