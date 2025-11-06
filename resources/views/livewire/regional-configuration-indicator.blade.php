<div>
    @if($currentConfig)
    <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="regionalConfigDropdown"
                data-bs-toggle="dropdown" aria-expanded="false" wire:click="toggleDetails">
            <i class="ri ri-global-line"></i>
            <span class="d-none d-md-inline">{{ $currentConfig['currency_symbol'] }} {{ $currentConfig['currency'] }}</span>
        </button>

        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="regionalConfigDropdown"
             style="width: 300px;" @if($showDetails) style="display: block;" @endif>
            <div class="px-3 py-2">
                <h6 class="dropdown-header">Configuración Regional</h6>

                <div class="row g-2">
                    <div class="col-6">
                        <small class="text-muted">Moneda:</small><br>
                        <strong>{{ $currentConfig['currency'] }}</strong><br>
                        <small>{{ $currentConfig['currency_symbol'] }}</small>
                    </div>

                    <div class="col-6">
                        <small class="text-muted">Zona Horaria:</small><br>
                        <strong>{{ $currentConfig['timezone'] }}</strong>
                    </div>

                    <div class="col-6">
                        <small class="text-muted">Formato Fecha:</small><br>
                        <strong>{{ $currentConfig['date_format'] }}</strong>
                    </div>

                    <div class="col-6">
                        <small class="text-muted">Idioma:</small><br>
                        <strong>{{ strtoupper($currentConfig['idioma']) }}</strong>
                    </div>
                </div>

                <hr class="my-2">

                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Configuración activa</small>
                    <span class="badge bg-success">Activo</span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Escuchar cambios en la configuración regional
    document.addEventListener('livewire:init', function () {
        Livewire.on('regional-configuration-updated', function (data) {
            // Mostrar notificación de cambio
            if (typeof toastr !== 'undefined') {
                toastr.success('Configuración regional actualizada', 'Éxito', {
                    timeOut: 3000,
                    positionClass: 'toast-top-right'
                });
            }
        });
    });
</script>
@endpush
