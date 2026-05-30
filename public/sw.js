const CACHE = 'ostoma-v1';

const STATIC = [
  '/',
  '/css/bootstrap.css',
  '/css/style.css',
  '/css/responsive.css',
  '/css/custom.css',
  '/css/font-awesome.min.css',
  '/js/jquery-3.4.1.min.js',
  '/js/bootstrap.js',
  '/js/custom.js',
  '/images/favicon.png',
  '/images/icon-192.png',
  '/images/icon-512.png',
];

self.addEventListener('install', e => {
  e.waitUntil(
    caches.open(CACHE).then(c => c.addAll(STATIC)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', e => {
  e.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', e => {
  const url = new URL(e.request.url);

  // Само same-origin заявки
  if (url.origin !== location.origin) return;

  // HTML — network-first (винаги свежо съдържание)
  if (e.request.headers.get('accept')?.includes('text/html')) {
    e.respondWith(
      fetch(e.request)
        .then(r => { const c = r.clone(); caches.open(CACHE).then(cache => cache.put(e.request, c)); return r; })
        .catch(() => caches.match(e.request))
    );
    return;
  }

  // Статични файлове — cache-first
  e.respondWith(
    caches.match(e.request).then(cached => cached || fetch(e.request).then(r => {
      const c = r.clone();
      caches.open(CACHE).then(cache => cache.put(e.request, c));
      return r;
    }))
  );
});
