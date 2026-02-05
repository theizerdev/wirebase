<x-layouts.horizontal>
    <x-slot name="title">Toast Minimal Horizontal</x-slot>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Toast Minimal - Layout Horizontal</h4>
                        <p class="text-muted mb-0">Prueba mínima para verificar toasts</p>
                    </div>
                    <div class="card-body">
                        <button onclick="testToast()" class="btn btn-primary">
                            <i class="ri-notification-line"></i> Probar Toast
                        </button>
                        
                        <div id="resultado" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function testToast() {
        console.log('=== INICIANDO PRUEBA DE TOAST ===');
        
        // 1. Verificar contenedor
        const container = document.getElementById('global-toast-container');
        console.log('Contenedor encontrado:', container);
        
        if (!container) {
            console.error('❌ Contenedor NO encontrado');
            document.getElementById('resultado').innerHTML = '<div class="alert alert-danger">Contenedor NO encontrado</div>';
            return;
        }
        
        console.log('✅ Contenedor encontrado');
        console.log('ID:', container.id);
        console.log('Clases:', container.className);
        console.log('Estilos computados:', window.getComputedStyle(container));
        
        // 2. Verificar Bootstrap
        if (typeof bootstrap === 'undefined' || !bootstrap.Toast) {
            console.error('❌ Bootstrap Toast NO disponible');
            document.getElementById('resultado').innerHTML = '<div class="alert alert-danger">Bootstrap Toast NO disponible</div>';
            return;
        }
        
        console.log('✅ Bootstrap Toast disponible');
        
        // 3. Crear toast manualmente
        try {
            const toastHtml = `
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <i class="ri-check-line text-success me-2"></i>
                        <strong class="me-auto">Éxito</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Toast de prueba manual
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = container.lastElementChild;
            
            console.log('Toast elemento creado:', toastElement);
            
            // 4. Inicializar toast
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000
            });
            
            console.log('Toast inicializado:', toast);
            
            // 5. Mostrar toast
            toast.show();
            
            console.log('✅ Toast mostrado');
            document.getElementById('resultado').innerHTML = '<div class="alert alert-success">Toast creado y mostrado manualmente</div>';
            
        } catch (error) {
            console.error('❌ Error creando toast manual:', error);
            document.getElementById('resultado').innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
        }
    }
    
    // Prueba con showToast
    function testShowToast() {
        console.log('=== PROBANDO showToast ===');
        
        if (typeof window.showToast === 'function') {
            try {
                window.showToast('success', 'Prueba con showToast');
                console.log('✅ showToast ejecutado');
            } catch (error) {
                console.error('❌ Error en showToast:', error);
            }
        } else {
            console.error('❌ showToast NO disponible');
        }
    }
    
    // Ejecutar pruebas al cargar
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            console.log('=== ESTADO AL CARGAR ===');
            console.log('Contenedor:', document.getElementById('global-toast-container'));
            console.log('Bootstrap:', typeof bootstrap);
            console.log('showToast:', typeof window.showToast);
        }, 1000);
    });
    </script>
    
    <button onclick="testShowToast()" class="btn btn-secondary mt-2">
        <i class="ri-function-line"></i> Probar showToast
    </button>
    @endpush
</x-layouts.horizontal>