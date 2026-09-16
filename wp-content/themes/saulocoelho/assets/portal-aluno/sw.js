/* Portal do Aluno SW — cache leve de assets do shell. Scope: / */
const CACHE = 'sc-portal-aluno-v4';
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

/**
 * Web Push (Fase B · issue #16).
 * Payload JSON: { title, body, url, tag }
 */
self.addEventListener('push', (event) => {
  let data = {
    title: 'Portal do Aluno',
    body: '',
    url: '/minha-conta/',
    tag: 'sc-portal-notice',
  };
  try {
    if (event.data) {
      const parsed = event.data.json();
      if (parsed && typeof parsed === 'object') {
        data = Object.assign(data, parsed);
      }
    }
  } catch (e) {
    try {
      const text = event.data && event.data.text();
      if (text) data.body = text;
    } catch (e2) {
      /* ignore */
    }
  }

  const options = {
    body: data.body || '',
    tag: data.tag || 'sc-portal-notice',
    data: { url: data.url || '/minha-conta/' },
    renotify: true,
    icon: '/wp-content/themes/saulocoelho/assets/portal-aluno/icon-192.png',
    badge: '/wp-content/themes/saulocoelho/assets/portal-aluno/icon-192.png',
  };

  event.waitUntil(self.registration.showNotification(data.title || 'Portal do Aluno', options));
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const target = (event.notification && event.notification.data && event.notification.data.url) || '/minha-conta/';
  const abs = new URL(target, self.location.origin).href;

  event.waitUntil(
    self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      for (let i = 0; i < clientList.length; i++) {
        const client = clientList[i];
        if (client.url && 'focus' in client) {
          client.focus();
          if ('navigate' in client) {
            return client.navigate(abs);
          }
          return client;
        }
      }
      if (self.clients.openWindow) {
        return self.clients.openWindow(abs);
      }
      return undefined;
    })
  );
});
