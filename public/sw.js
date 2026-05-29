// Service Worker para PWA de PUPILAINC
const CACHE_NAME = 'pupilainc-v1';
const STATIC_CACHE = 'pupilainc-static-v1';
const DYNAMIC_CACHE = 'pupilainc-dynamic-v1';

// Recursos estáticos a cachear
const STATIC_ASSETS = [
  '/',
  '/logo/1719430882.png',
  '/manifest.json'
];

// Instalar Service Worker
self.addEventListener('install', (event) => {
  console.log('[Service Worker] Instalando...');
  event.waitUntil(
    caches.open(STATIC_CACHE).then((cache) => {
      console.log('[Service Worker] Cacheando recursos estáticos');
      return cache.addAll(STATIC_ASSETS);
    }).then(() => {
      return self.skipWaiting(); // Activar inmediatamente
    })
  );
});

// Activar Service Worker
self.addEventListener('activate', (event) => {
  console.log('[Service Worker] Activado');
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((cacheName) => {
            return cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE;
          })
          .map((cacheName) => {
            console.log('[Service Worker] Eliminando cache antiguo:', cacheName);
            return caches.delete(cacheName);
          })
      );
    })
  );
});

// Interceptar solicitudes
self.addEventListener('fetch', (event) => {
  // Solo interceptar solicitudes GET
  if (event.request.method !== 'GET') {
    return;
  }

  event.respondWith(
    caches.match(event.request).then((cacheResponse) => {
      if (cacheResponse) {
        return cacheResponse;
      }

      // Clonar la solicitud
      const fetchRequest = event.request.clone();

      return fetch(fetchRequest).then((response) => {
        // Verificar si la respuesta es válida
        if (!response || response.status !== 200 || response.type !== 'basic') {
          return response;
        }

        // Clonar la respuesta
        const responseToCache = response.clone();

        caches.open(DYNAMIC_CACHE).then((cache) => {
          // No cachear páginas de administrador para evitar datos obsoletos
          if (!event.request.url.includes('/admin/')) {
            cache.put(event.request, responseToCache);
          }
        });

        return response;
      }).catch(() => {
        // Si falla la red, intentar servir desde cache
        return caches.match('/');
      });
    })
  );
});

// Sincronización en segundo plano
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-data') {
    console.log('[Service Worker] Sincronización en segundo plano');
  }
});

// Notificaciones Push (futuro)
self.addEventListener('push', (event) => {
  console.log('[Service Worker] Push recibido');
  // Aquí puedes agregar manejo de notificaciones push
});
