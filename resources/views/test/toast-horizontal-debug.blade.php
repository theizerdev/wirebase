<x-layouts.horizontal>
    <x-slot name="title">Debug Toast Horizontal</x-slot>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Debug Toast - Layout Horizontal</h4>
                        <p class="text-muted mb-0">Prueba de diagnóstico para verificar toasts en layout horizontal</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Toast Directo:</h5>
                                <div class="d-flex flex-column gap-2">
                                    <button onclick="window.showToast('success', 'Éxito directo desde horizontal')" class="btn btn-success">
                                        <i class="ri-check-line"></i> Éxito Directo
                                    </button>
                                    
                                    <button onclick="window.showToast('error', 'Error directo desde horizontal')" class="btn btn-danger">
                                        <i class="ri-close-line"></i> Error Directo
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Toast con Fallback:</h5>
                                <div class="d-flex flex-column gap-2">
                                    <button onclick="window.showToastSafe('success', 'Éxito seguro desde horizontal')" class="btn btn-outline-success">
                                        <i class="ri-shield-check-line"></i> Éxito Seguro
                                    </button>
                                    
                                    <button onclick="window.showToastSafe('error', 'Error seguro desde horizontal')" class="btn btn-outline-danger">
                                        <i class="ri-shield-flash-line"></i> Error Seguro
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <h5>Diagnóstico del Sistema:</h5>
                                <div id="debug-info" class="p-3 bg-light rounded">
                                    <small class="text-muted">Verificando sistema...</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Contenedor Toast:</h5>
                                <div id="container-info" class="p-3 bg-light rounded">
                                    <small class="text-muted">Buscando contenedor...</small>
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
        // Función para actualizar información de debug
        function updateDebugInfo() {
            const debugDiv = document.getElementById('debug-info');
            const containerDiv = document.getElementById('container-info');
            let debugInfo = '';
            let containerInfo = '';
            
            // Verificar Bootstrap
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                debugInfo += '<span class="text-success"><i class="ri-check-line"></i> Bootstrap Toast disponible</span><br>';
            } else {
                debugInfo += '<span class="text-danger"><i class="ri-close-line"></i> Bootstrap Toast NO disponible</span><br>';
            }
            
            // Verificar funciones
            if (typeof window.showToast === 'function') {
                debugInfo += '<span class="text-success"><i class="ri-check-line"></i> showToast disponible</span><br>';
            } else {
                debugInfo += '<span class="text-danger"><i class="ri-close-line"></i> showToast NO disponible</span><br>';
            }
            
            if (typeof window.showToastSafe === 'function') {
                debugInfo += '<span class="text-success"><i class="ri-check-line"></i> showToastSafe disponible</span><br>';
            } else {
                debugInfo += '<span class="text-danger"><i class="ri-close-line"></i> showToastSafe NO disponible</span><br>';
            }
            
            // Verificar contenedor
            const container = document.getElementById('global-toast-container');
            if (container) {
                containerInfo += '<span class="text-success"><i class="ri-check-line"></i> Contenedor encontrado</span><br>';
                containerInfo += '<strong>ID:</strong> ' + container.id + '<br>';
                containerInfo += '<strong>Clases:</strong> ' + container.className + '<br>';
                containerInfo += '<strong>Estilo:</strong> ' + container.getAttribute('style') + '<br>';
                containerInfo += '<strong>Visible:</strong> ' + (container.offsetParent !== null ? 'Sí' : 'No') + '<br>';
                containerInfo += '<strong>Hijos:</strong> ' + container.children.length + '<br>';
                
                // Verificar z-index computado
                const computedStyle = window.getComputedStyle(container);
                containerInfo += '<strong>z-index:</strong> ' + computedStyle.zIndex + '<br>';
                containerInfo += '<strong>position:</strong> ' + computedStyle.position + '<br>';
                containerInfo += '<strong>top:</strong> ' + computedStyle.top + '<br>';
                containerInfo += '<strong>right:</strong> ' + computedStyle.right + '<br>';
            } else {
                containerInfo += '<span class="text-danger"><i class="ri-close-line"></i> Contenedor NO encontrado</span><br>';
                
                // Buscar otros contenedores
                const allContainers = document.querySelectorAll('.toast-container, [id*="toast"]');
                if (allContainers.length > 0) {
                    containerInfo += '<strong>Contenedores encontrados:</strong><br>';
                    allContainers.forEach((cont, index) => {
                        containerInfo += '- ' + (cont.id || 'sin-id') + ' (' + cont.className + ')<br>';
                    });
                }
            }
            
            debugDiv.innerHTML = debugInfo;
            containerDiv.innerHTML = containerInfo;
        }
        
        // Actualizar inmediatamente y después de 2 segundos
        updateDebugInfo();
        setTimeout(updateDebugInfo, 2000);
        
        // Forzar un toast de prueba al cargar
        setTimeout(() => {
            console.log('Forzando toast de prueba...');
            if (typeof window.showToast === 'function') {
                window.showToast('info', 'Toast de diagnóstico - Layout Horizontal');
            } else if (typeof window.showToastSafe === 'function') {
                window.showToastSafe('info', 'Toast de diagnóstico - Layout Horizontal');
            } else {
                alert('No hay función de toast disponible');
            }
        }, 3000);
    });
    </script>
    @endpush
</x-layouts.horizontal>