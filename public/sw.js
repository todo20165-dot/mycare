const CACHE_NAME = 'mycare-static-v1';
const STATIC_ASSETS = [
  '/manifest.json',
  '/offline.html',
];
const NO_CACHE_PATHS = ['/login', '/register', '/logout'];
const STATIC_ASSET_DESTINATIONS = ['style', 'script', 'image', 'font', 'manifest'];

function isAuthOrDynamicRoute(url) {
  return NO_CACHE_PATHS.some(path => url.pathname === path || url.pathname.startsWith(path + '/'));
}

function isNavigationRequest(request) {
  return request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html');
}

function isStaticAsset(request) {
  const url = new URL(request.url);
  return STATIC_ASSET_DESTINATIONS.includes(request.destination) || /\.(?:js|css|png|jpg|jpeg|svg|webp|gif|ico|json|woff2|woff|ttf|eot)$/.test(url.pathname);
}

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(STATIC_ASSETS))
      .catch(err => console.error('Service Worker install failed:', err))
  );
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => Promise.all(
      keys.map(key => {
        if (key !== CACHE_NAME) {
          return caches.delete(key);
        }
      })
    ))
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') {
    return;
  }

  const requestUrl = new URL(event.request.url);
  if (requestUrl.origin !== self.location.origin) {
    return;
  }

  if (isAuthOrDynamicRoute(requestUrl)) {
    event.respondWith(networkOnly(event.request));
    return;
  }

  if (isNavigationRequest(event.request)) {
    event.respondWith(networkFirst(event.request));
    return;
  }

  if (isStaticAsset(event.request)) {
    event.respondWith(cacheFirst(event.request));
    return;
  }
});

function networkOnly(request) {
  return fetch(request.clone()).catch(() => {
    if (request.destination === 'document') {
      return caches.match('/offline.html');
    }
    return new Response('Offline', { status: 503, statusText: 'Service Unavailable' });
  });
}

function networkFirst(request) {
  return fetch(request.clone())
    .then(response => {
      if (response) {
        return response;
      }
      throw new Error('No response from network');
    })
    .catch(() => caches.match('/offline.html'));
}

function cacheFirst(request) {
  return caches.match(request).then(cachedResponse => {
    if (cachedResponse) {
      return cachedResponse;
    }

    return fetch(request.clone())
      .then(response => {
        if (!response || !response.ok) {
          return response;
        }

        const responseToCache = response.clone();
        caches.open(CACHE_NAME).then(cache => cache.put(request, responseToCache));
        return response;
      })
      .catch(() => {
        return caches.match('/offline.html');
      });
  });
}

self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});
