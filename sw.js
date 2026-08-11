const CACHE_NAME = 'canteen-cache-v3';
const ASSETS = [
  'menu.php',
  'css/style.css',
  'js/cart.js',
  'about.php',
  'cart.php',
  'checkout.php',
  'track_order.php',
  'order_history.php',
  'manifest.json'
];

// Cache all assets on install
self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS).catch(err => console.log("SW Install cache note:", err));
    })
  );
  self.skipWaiting();
});

// Purge old versions on activation
self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Network-First for dynamic PHP pages, Cache-First for static assets
self.addEventListener('fetch', (e) => {
  const url = new URL(e.request.url);

  // Exclude third-party CDN assets from complex caching
  if (url.origin !== self.location.origin) {
    e.respondWith(fetch(e.request));
    return;
  }

  // Bypass cache for non-GET requests (e.g. POST requests for logins, orders, updates)
  if (e.request.method !== 'GET') {
    e.respondWith(fetch(e.request));
    return;
  }

  const isDynamicPage = url.pathname.endsWith('.php') || url.pathname.endsWith('/') || !url.pathname.includes('.');

  if (isDynamicPage) {
    // Network-First: Fetch from XAMPP first. Fallback to cache if offline.
    e.respondWith(
      fetch(e.request)
        .then((response) => {
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(e.request, responseClone);
          });
          return response;
        })
        .catch(() => {
          return caches.match(e.request);
        })
    );
  } else {
    // Cache-First: Search in cache. If not found, fetch from network.
    e.respondWith(
      caches.match(e.request).then((cachedResponse) => {
        return cachedResponse || fetch(e.request).then((response) => {
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(e.request, responseClone);
          });
          return response;
        });
      })
    );
  }
});
