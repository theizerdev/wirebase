<x-layouts.admin>
    <x-slot name="title">Prueba Toast Completa</x-slot>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Prueba de Notificaciones Toast - Modo Completo</h4>
                        <p class="text-muted mb-0">Esta prueba verifica que los toasts funcionen en pantalla completa y responsive</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Toast desde JavaScript Directo:</h5>
                                <div class="d-flex flex-column gap-2">
                                    <button onclick="window.showToast('success', '¡Éxito! La operación se completó correctamente.')" class="btn btn-success">
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
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Toast con Función Segura (Fallback):</h5>
                                <div class="d-flex flex-column gap-2">
                                    <button onclick="window.showToastSafe('success', 'Éxito con función segura: Fallback activado si es necesario.')" class="btn btn-outline-success">
                                        <i class="ri-shield-check-line"></i> Éxito Seguro
                                    </button>
                                    
                                    <button onclick="window.showToastSafe('error', 'Error con función segura: Fallback garantizado.')" class="btn btn-outline-danger">
                                        <i class="ri-shield-flash-line"></i> Error Seguro
                                    </button>
                                    
                                    <button onclick="window.showToastSafe('warning', 'Advertencia: Esta función siempre funciona.')" class="btn btn-outline-warning">
                                        <i class="ri-shield-warning-line"></i> Advertencia Segura
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <h5>Toast desde Livewire:</h5>
                                <div class="d-flex flex-column gap-2">
                                    <button onclick="Livewire.dispatch('showToast', {type: 'success', message: 'Toast desde Livewire: ¡Operación exitosa!'})" class="btn btn-success">
                                        <i class="ri-flashlight-line"></i> Livewire Éxito
                                    </button>
                                    
                                    <button onclick="Livewire.dispatch('showToast', {type: 'error', message: 'Toast desde Livewire: Error en el proceso.'})" class="btn btn-danger">
                                        <i class="ri-flashlight-line"></i> Livewire Error
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Toast con Mensaje Largo:</h5>
                                <div class="d-flex flex-column gap-2">
                                    <button onclick="window.showToast('success', 'Este es un mensaje muy largo para probar cómo se manejan los textos extensos en las notificaciones Toast. El sistema debería mostrar todo el contenido de manera legible y responsiva.')" class="btn btn-primary">
                                        <i class="ri-article-line"></i> Mensaje Largo
                                    </button>
                                    
                                    <button onclick="window.showToast('error', 'Error crítico: El sistema no pudo procesar la solicitud debido a un problema de conexión con el servidor externo. Por favor, verifica tu conexión a internet y vuelve a intentarlo en unos minutos.')" class="btn btn-dark">
                                        <i class="ri-error-warning-line"></i> Error Detallado
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="alert alert-info">
                            <h6><i class="ri-information-line"></i> Instrucciones de Prueba:</h6>
                            <ol class="mb-0">
                                <li>Abre esta página en <strong>pantalla completa</strong> (sin F12)</li>
                                <li>Haz clic en los botones de arriba</li>
                                <li>Abre las <strong>herramientas de desarrollo</strong> (F12)</li>
                                <li>Haz clic en los botones nuevamente</li>
                                <li>Los toasts deberían funcionar en ambos modos</li>
                            </ol>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Estado del Sistema:</h5>
                                <div id="toast-status" class="p-3 bg-light rounded">
                                    <small class="text-muted">Verificando sistema...</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar el estado del sistema
        function checkToastSystem() {
            const statusDiv = document.getElementById('toast-status');
            let status = '';
            
            // Verificar Bootstrap
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                status += '<span class="text-success"><i class="ri-check-line"></i> Bootstrap Toast disponible</span><br>';
            } else {
                status += '<span class="text-danger"><i class="ri-close-line"></i> Bootstrap Toast NO disponible</span><br>';
            }
            
            // Verificar contenedor
            const container = document.getElementById('global-toast-container');
            if (container) {
                status += '<span class="text-success"><i class="ri-check-line"></i> Contenedor Toast encontrado</span><br>';
            } else {
                status += '<span class="text-danger"><i class="ri-close-line"></i> Contenedor Toast NO encontrado</span><br>';
            }
            
            // Verificar función showToast
            if (typeof window.showToast === 'function') {
                status += '<span class="text-success"><i class="ri-check-line"></i> Función showToast disponible</span><br>';
            } else {
                status += '<span class="text-danger"><i class="ri-close-line"></i> Función showToast NO disponible</span><br>';
            }
            
            // Verificar función showToastSafe
            if (typeof window.showToastSafe === 'function') {
                status += '<span class="text-success"><i class="ri-check-line"></i> Función showToastSafe disponible</span><br>';
            } else {
                status += '<span class="text-danger"><i class="ri-close-line"></i> Función showToastSafe NO disponible</span><br>';
            }
            
            // Verificar Livewire
            if (typeof Livewire !== 'undefined') {
                status += '<span class="text-success"><i class="ri-check-line"></i> Livewire disponible</span><br>';
            } else {
                status += '<span class="text-danger"><i class="ri-close-line"></i> Livewire NO disponible</span><br>';
            }
            
            statusDiv.innerHTML = status;
        }
        
        // Verificar después de un pequeño retraso para asegurar que todo esté cargado
        setTimeout(checkToastSystem, 1000);
        
        // Verificar nuevamente después de 3 segundos
        setTimeout(checkToastSystem, 3000);
    });
    </script>
    @endpush
</x-layouts.admin>