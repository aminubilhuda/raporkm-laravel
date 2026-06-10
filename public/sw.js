const CACHE_NAME = 'erapor-v2';
const urlsToCache = [
    '/',
    '/css/app.css',
    '/js/app.js',
];

// Install
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(urlsToCache))
            .catch(() => {})
    );
    self.skipWaiting();
});

// Fetch
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }
    if (event.request.url.includes('/api/')) {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                if (response) {
                    return response;
                }
                return fetch(event.request).then((response) => {
                    if (!response || response.status !== 200) {
                        return response;
                    }
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                    return response;
                });
            })
            .catch(() => {
                return caches.match('/');
            })
    );
});

// Activate
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.filter((cacheName) => cacheName !== CACHE_NAME)
                    .map((cacheName) => caches.delete(cacheName))
            );
        })
    );
    self.clients.claim();
});

// Push Notification
self.addEventListener('push', (event) => {
    if (!event.data) {
        return;
    }

    let payload;
    try {
        payload = event.data.json();
    } catch (e) {
        payload = {
            title: 'E-Rapor',
            body: event.data.text(),
        };
    }

    const title = payload.title || 'E-Rapor KM';
    const options = {
        body: payload.body || '',
        icon: payload.icon || '/icons/icon-192.png',
        badge: payload.badge || '/icons/icon-192.png',
        vibrate: [200, 100, 200],
        tag: 'erapor-notification',
        renotify: true,
    };

    if (payload.url) {
        options.data = { url: payload.url };
    }

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Notification click
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const url = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(url);
                    return client.focus();
                }
            }
            return clients.openWindow(url);
        })
    );
});

// Background Sync for nilai (offline queue)
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-nilai') {
        event.waitUntil(syncNilaiFromIndexedDB());
    }
});

async function syncNilaiFromIndexedDB() {
    const db = await openDB();
    const tx = db.transaction('pwa_sync_queue', 'readwrite');
    const store = tx.objectStore('pwa_sync_queue');
    const request = store.getAll();

    return new Promise((resolve, reject) => {
        request.onsuccess = async () => {
            const items = request.result;
            for (const item of items) {
                try {
                    const response = await fetch(item.url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': item.csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(item.payload),
                    });

                    if (response.ok) {
                        store.delete(item.id);
                    }
                } catch (err) {
                    // Will retry next sync
                }
            }
            resolve();
        };
        request.onerror = () => reject(request.error);
    });
}

function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('erapor-pwa', 1);
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}
