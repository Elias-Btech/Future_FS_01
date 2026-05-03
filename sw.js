/**
 * Service Worker - Enhanced PWA Support
 * Phase 3: PWA Enhancement & Optimization
 * 
 * Caching Strategies:
 * - HTML: Network-first (always try network, fallback to cache)
 * - CSS/JS: Cache-first (use cache, fallback to network)
 * - Images: Cache-first with expiration
 * - API: Network-first with cache fallback
 * - Translations: Cache-first (JSON language files)
 */

const CACHE_VERSION = 'v3';
const CACHE_NAMES = {
  html: `elias-html-${CACHE_VERSION}`,
  css: `elias-css-${CACHE_VERSION}`,
  js: `elias-js-${CACHE_VERSION}`,
  images: `elias-images-${CACHE_VERSION}`,
  api: `elias-api-${CACHE_VERSION}`,
  translations: `elias-translations-${CACHE_VERSION}`
};

const CRITICAL_ASSETS = [
  '/',
  '/index.php',
  '/index.html',
  '/css/auth.css',
  '/assets/elias-nobg.png',
  '/assets/elias.jpg',
  '/manifest.json'
];

const TRANSLATION_FILES = [
  '/config/lang/en.json',
  '/config/lang/fr.json',
  '/config/lang/am.json',
  '/config/lang/ti.json'
];

// ===== INSTALL EVENT =====
self.addEventListener('install', event => {
  console.log('[SW] Installing service worker...');
  
  event.waitUntil(
    Promise.all([
      // Cache critical HTML assets
      caches.open(CACHE_NAMES.html).then(cache => {
        return cache.addAll(CRITICAL_ASSETS).catch(err => {
          console.warn('[SW] Critical assets cache error:', err);
        });
      }),
      
      // Cache translation files
      caches.open(CACHE_NAMES.translations).then(cache => {
        return cache.addAll(TRANSLATION_FILES).catch(err => {
          console.warn('[SW] Translation files cache error:', err);
        });
      })
    ]).then(() => {
      console.log('[SW] Installation complete');
      self.skipWaiting();
    })
  );
});

// ===== ACTIVATE EVENT =====
self.addEventListener('activate', event => {
  console.log('[SW] Activating service worker...');
  
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys
          .filter(key => !Object.values(CACHE_NAMES).includes(key))
          .map(key => {
            console.log('[SW] Deleting old cache:', key);
            return caches.delete(key);
          })
      );
    }).then(() => {
      console.log('[SW] Activation complete');
      self.clients.claim();
    })
  );
});

// ===== FETCH EVENT =====
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);
  
  // Skip non-GET requests
  if (request.method !== 'GET') return;
  
  // Skip chrome extensions and external requests
  if (url.protocol === 'chrome-extension:') return;
  
  // Route to appropriate caching strategy
  if (isTranslationFile(url)) {
    event.respondWith(cacheFirstStrategy(request, CACHE_NAMES.translations));
  } else if (isImageFile(url)) {
    event.respondWith(cacheFirstStrategy(request, CACHE_NAMES.images));
  } else if (isCSSFile(url)) {
    event.respondWith(cacheFirstStrategy(request, CACHE_NAMES.css));
  } else if (isJSFile(url)) {
    event.respondWith(cacheFirstStrategy(request, CACHE_NAMES.js));
  } else if (isAPICall(url)) {
    event.respondWith(networkFirstStrategy(request, CACHE_NAMES.api));
  } else {
    // HTML and other files: network-first
    event.respondWith(networkFirstStrategy(request, CACHE_NAMES.html));
  }
});

// ===== CACHING STRATEGIES =====

/**
 * Network-first strategy
 * Try network first, fallback to cache
 */
function networkFirstStrategy(request, cacheName) {
  return fetch(request)
    .then(response => {
      // Cache successful responses
      if (response && response.status === 200) {
        const responseClone = response.clone();
        caches.open(cacheName).then(cache => {
          cache.put(request, responseClone);
        });
      }
      return response;
    })
    .catch(() => {
      // Fallback to cache
      return caches.match(request).then(cached => {
        if (cached) {
          return cached;
        }
        
        // Return offline page if available
        if (request.destination === 'document') {
          return caches.match('/offline.html').catch(() => {
            return new Response(
              '<h1>Offline</h1><p>You are offline. Please check your connection.</p>',
              {
                status: 503,
                statusText: 'Service Unavailable',
                headers: new Headers({ 'Content-Type': 'text/html' })
              }
            );
          });
        }
        
        return new Response('Resource not available offline', {
          status: 503,
          statusText: 'Service Unavailable',
          headers: new Headers({ 'Content-Type': 'text/plain' })
        });
      });
    });
}

/**
 * Cache-first strategy
 * Use cache first, fallback to network
 */
function cacheFirstStrategy(request, cacheName) {
  return caches.match(request).then(cached => {
    if (cached) {
      return cached;
    }
    
    return fetch(request).then(response => {
      // Cache successful responses
      if (response && response.status === 200) {
        const responseClone = response.clone();
        caches.open(cacheName).then(cache => {
          cache.put(request, responseClone);
        });
      }
      return response;
    }).catch(() => {
      // Return cached version if available
      return caches.match(request);
    });
  });
}

// ===== HELPER FUNCTIONS =====

function isTranslationFile(url) {
  return url.pathname.includes('/config/lang/') && url.pathname.endsWith('.json');
}

function isImageFile(url) {
  return /\.(png|jpg|jpeg|gif|svg|webp)$/i.test(url.pathname);
}

function isCSSFile(url) {
  return url.pathname.endsWith('.css');
}

function isJSFile(url) {
  return url.pathname.endsWith('.js');
}

function isAPICall(url) {
  return url.pathname.includes('/php/') || url.pathname.includes('/api/');
}

// ===== MESSAGE HANDLING =====
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'CLEAR_CACHE') {
    caches.keys().then(keys => {
      Promise.all(keys.map(key => caches.delete(key)));
    });
  }
});
