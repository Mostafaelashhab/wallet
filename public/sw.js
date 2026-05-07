// Splitty service worker
const CACHE = 'splitty-v1';
const PRECACHE = ['/', '/manifest.webmanifest'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE).then((c) => c.addAll(PRECACHE)).catch(() => {}));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') return;
    const url = new URL(req.url);
    if (url.origin !== location.origin) return;

    // Network first for HTML pages, cache fallback
    if (req.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(req).then((res) => {
                const copy = res.clone();
                caches.open(CACHE).then((c) => c.put(req, copy)).catch(() => {});
                return res;
            }).catch(() => caches.match(req).then((r) => r || caches.match('/')))
        );
        return;
    }

    // Cache first for assets
    event.respondWith(
        caches.match(req).then((cached) => cached || fetch(req).then((res) => {
            if (res.status === 200 && res.type === 'basic') {
                const copy = res.clone();
                caches.open(CACHE).then((c) => c.put(req, copy));
            }
            return res;
        }))
    );
});

// Push notifications
self.addEventListener('push', (event) => {
    let data = { title: 'Splitty', body: '', url: '/' };
    if (event.data) { try { data = event.data.json(); } catch { data.body = event.data.text(); } }
    event.waitUntil(self.registration.showNotification(data.title, {
        body: data.body,
        icon: '/icons/icon-192.png',
        badge: '/icons/badge.png',
        data: { url: data.url || '/' },
    }));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || '/';
    event.waitUntil(self.clients.openWindow(url));
});
