<x-layouts.horizontal>
    <x-slot name="title">Prueba de Toast - Horizontal</x-slot>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Prueba de Notificaciones Toast - Layout Horizontal</h4>
                </div>
                <div class="card-body">
                    <p>Haz clic en los botones para probar las notificaciones Toast:</p>
                    
                    <div class="d-flex gap-2 flex-wrap">
                        <button onclick="window.showToast('success', '¡Operación exitosa! El mensaje fue enviado correctamente.')" class="btn btn-success">
                            <i class="ri-check-line"></i> Éxito
                        </button>
                        
                        <button onclick="window.showToast('error', 'Error: No se pudo procesar la solicitud. Por favor, intenta nuevamente.')" class="btn btn-danger">
                            <i class="ri-close-line"></i> Error
                        </button>
                        
                        <button onclick="window.showToast('warning', 'Advertencia: El servicio está experimentando lentitud.')" class="btn btn-warning">
                            <i class="ri-alert-line"></i> Advertencia
                        </button>
                        
                        <button onclick="window.showToast('info', 'Información: El proceso se está ejecutando en segundo plano.')" class="btn btn-info">
                            <i class="ri-information-line"></i> Información
                        </button>
                    </div>

                    <hr>

                    <h5>Prueba con Livewire:</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <button onclick="Livewire.dispatch('showToast', {type: 'success', message: 'Toast desde Livewire: ¡Todo salió bien!'})" class="btn btn-outline-success">
                            <i class="ri-flashlight-line"></i> Livewire Éxito
                        </button>
                        
                        <button onclick="Livewire.dispatch('showToast', {type: 'error', message: 'Toast desde Livewire: Hubo un error en el proceso.'})" class="btn btn-outline-danger">
                            <i class="ri-flashlight-line"></i> Livewire Error
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts.horizontal>