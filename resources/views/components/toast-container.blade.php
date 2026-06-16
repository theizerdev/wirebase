{{-- Contenedor de Toasts Global --}}
<div id="global-toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999 !important; pointer-events: none;">
  {{-- Los toasts se añadirán dinámicamente aquí --}}
</div>

<style>
/* Estilos adicionales para asegurar visibilidad de los toasts */
.toast-container {
  position: fixed !important;
  top: 20px !important;
  right: 20px !important;
  max-width: 350px !important;
}

.toast {
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
  border: none !important;
  margin-bottom: 1rem !important;
}

/* Asegurar que los toasts estén por encima de todo */
.toast-container * {
  z-index: 10000 !important;
}
</style>

<script>
// Verificar mensajes flash de sesión al cargar
document.addEventListener('DOMContentLoaded', function() {
    @if(session()->has('success'))
        window.showToast('success', "{{ session('success') }}");
    @endif

    @if(session()->has('error'))
        window.showToast('error', "{{ session('error') }}");
    @endif

    @if(session()->has('warning'))
        window.showToast('warning', "{{ session('warning') }}");
    @endif

    @if(session()->has('info'))
        window.showToast('info', "{{ session('info') }}");
    @endif
});

// Función para crear y mostrar un toast
window.showToast = function(type, message, duration = 5000) {
  if (!type || typeof type !== 'string') {
    console.warn('showToast called with invalid type:', type);
    return;
  }

  function initToast() {
    const toastContainer = document.getElementById('global-toast-container');
    if (!toastContainer) {
      console.warn('Toast container no encontrado');
      return;
    }

    if (typeof bootstrap === 'undefined' || !bootstrap.Toast) {
      console.warn('Bootstrap Toast no está disponible');
      const alertDiv = document.createElement('div');
      alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
      alertDiv.style.zIndex = '9999';
      alertDiv.innerHTML = `
        <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      document.body.appendChild(alertDiv);

      setTimeout(() => {
        if (alertDiv.parentNode) {
          alertDiv.parentNode.removeChild(alertDiv);
        }
      }, duration);
      return;
    }

    const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

    let toastClass, iconClass;
    switch(type) {
      case 'success':
        toastClass = 'bg-success text-white';
        iconClass = 'ri-check-line';
        break;
      case 'error':
        toastClass = 'bg-danger text-white';
        iconClass = 'ri-close-line';
        break;
      case 'warning':
        toastClass = 'bg-warning text-dark';
        iconClass = 'ri-alert-line';
        break;
      case 'info':
        toastClass = 'bg-info text-white';
        iconClass = 'ri-information-line';
        break;
      default:
        toastClass = 'bg-light text-dark';
        iconClass = 'ri-notification-line';
    }

    const toastHTML = `
      <div id="${toastId}" class="toast ${toastClass}" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${duration}" style="pointer-events: auto;">
        <div class="toast-header ${toastClass}">
          <i class="${iconClass} me-2"></i>
          <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          ${message}
        </div>
      </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHTML);

    try {
      const toastElement = document.getElementById(toastId);
      const toast = new bootstrap.Toast(toastElement);
      toast.show();

      toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
      });
    } catch (error) {
      console.error('Error al mostrar toast:', error);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initToast);
  } else {
    initToast();
  }
};

function handleToastPayload(payload) {
  const toastData = payload && payload.type && payload.message ? payload : (Array.isArray(payload) && payload[0] ? payload[0] : payload);
  if (toastData && toastData.type && toastData.message) {
    window.showToast(toastData.type, toastData.message, toastData.duration || 5000);
  }
}

// Livewire v2/v3 compatible listeners
(function initLivewireToastListeners() {
  // Evitar duplicados: algunos setups pueden disparar el evento 2 veces
  const seen = new Set();
  const TTL = 1500; // ms

  function handleOnce(payload) {
    try {
      const t = payload && payload.type ? payload.type : (Array.isArray(payload) && payload[0] ? payload[0].type : undefined);
      const m = payload && payload.message ? payload.message : (Array.isArray(payload) && payload[0] ? payload[0].message : undefined);
      const d = payload && payload.duration ? payload.duration : (Array.isArray(payload) && payload[0] ? payload[0].duration : undefined);
      const key = `${t}|${m}|${d || ''}`;

      if (seen.has(key)) return;
      seen.add(key);
      setTimeout(() => seen.delete(key), TTL);

      handleToastPayload(payload);
    } catch (e) {
      // fallback: no duplicar lógica
      handleToastPayload(payload);
    }
  }

  // 1) Listener clásico (v2)
  if (typeof Livewire !== 'undefined' && Livewire.on) {
    Livewire.on('showToast', handleOnce);
    Livewire.on('notify', handleOnce);
  }

  // 2) Listener DOM/customEvent (v3 suele dispatchar como CustomEvent en algunos setups)
  window.addEventListener('notify', function(e) {
    handleOnce(e.detail);
  });
  window.addEventListener('showToast', function(e) {
    handleOnce(e.detail);
  });
})();

// Listener para eventos de Alpine.js (si se usa)
if (typeof Alpine !== 'undefined') {
  window.addEventListener('show-toast', function(event) {
    const { type, message, duration } = event.detail;
    window.showToast(type, message, duration || 5000);
  });
}

window.addEventListener('load', function() {
  const container = document.getElementById('global-toast-container');
  if (container) {
    container.style.display = 'block';
    container.style.visibility = 'visible';
    container.style.opacity = '1';
  }
});

window.showToastSafe = function(type, message, duration = 5000) {
  try {
    if (typeof window.showToast === 'function') {
      window.showToast(type, message, duration);
    } else {
      const notification = document.createElement('div');
      notification.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed`;
      notification.style.cssText = 'top: 20px; right: 20px; z-index: 99999; min-width: 300px; max-width: 400px;';
      notification.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
          <div><strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}</div>
          <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
      `;
      document.body.appendChild(notification);

      setTimeout(() => {
        if (notification.parentNode) {
          notification.remove();
        }
      }, duration);
    }
  } catch (error) {
    console.error('Error al mostrar notificación:', error);
  }
};
</script>
