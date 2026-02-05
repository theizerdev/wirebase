<x-layouts.admin>
    <x-slot name="title">Prueba de Toast - Admin</x-slot>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Prueba de Notificaciones Toast - Layout Admin</h4>
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
                        
                        <button onclick="window.showToast('success', 'Este es un mensaje largo para probar cómo se manejan los textos extensos en las notificaciones Toast. El sistema debería mostrar todo el contenido de manera legible.')" class="btn btn-primary">
                            <i class="ri-notification-line"></i> Mensaje Largo
                        </button>
                    </div>

                    <hr>

                    <h5>Prueba con Livewire:</h5>
                    <p>Estos botones usan Livewire para disparar eventos Toast:</p>
                    
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

@push('scripts')
<script>
    // También podemos usar Alpine.js si está disponible
    document.addEventListener('alpine:init', () => {
        Alpine.data('toastDemo', () => ({
            showSuccess() {
                this.$dispatch('show-toast', {
                    type: 'success',
                    message: 'Toast desde Alpine.js: ¡Operación completada!'
                });
            },
            showError() {
                this.$dispatch('show-toast', {
                    type: 'error', 
                    message: 'Toast desde Alpine.js: Se produjo un error.'
                });
            }
        }));
    });
</script>
@endpush
</x-layouts.admin>