/* Portal do Aluno SW — cache leve de assets do shell. Scope: / */
const CACHE = 'sc-portal-aluno-v1';
const PRECACHE = [];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE).then((cache) => cache.addAll(PRECACHE)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const req = event.request;
  if (req.method !== 'GET') {
    return;
  }
  const url = new URL(req.url);
  // Só cachear assets estáticos do tema portal (não HTML de conta / LMS).
  if (!url.pathname.includes('/assets/portal-aluno/')) {
    return;
  }
  event.respondWith(
    caches.open(CACHE).then(async (cache) => {
      const cached = await cache.match(req);
      if (cached) {
        return cached;
      }
      try {
        const res = await fetch(req);
        if (res && res.ok) {
          cache.put(req, res.clone());
        }
        return res;
      } catch (e) {
        return cached || Response.error();
      }
    })
  );
});
