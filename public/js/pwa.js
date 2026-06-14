// PWA Auto-Login + Push Notification + Background Sync Logic
(function() {
    'use strict';

    const PWA_TOKEN_KEY = 'pwa_token';
    const PWA_USER_KEY = 'pwa_user';
    const PUSH_SUBSCRIBED_KEY = 'pwa_push_subscribed';

    // Register Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then((reg) => {
                console.log('SW registered:', reg.scope);

                // Check for SW update
                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    if (newWorker) {
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                // New version available, notify all clients
                                navigator.serviceWorker.controller.postMessage({ type: 'UPDATE_AVAILABLE' });
                            }
                        });
                    }
                });
            })
            .catch((err) => console.log('SW registration failed:', err));
    }

    // PWA Auto-Login Check
    window.pwaAutoLogin = async function() {
        const token = localStorage.getItem(PWA_TOKEN_KEY);
        if (!token) return false;

        try {
            const res = await fetch('/api/pwa/check', {
                method: 'GET',
                headers: {
                    'X-PWA-Token': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (res.ok) {
                const data = await res.json();
                localStorage.setItem(PWA_USER_KEY, JSON.stringify(data.user));
                return true;
            } else {
                localStorage.removeItem(PWA_TOKEN_KEY);
                localStorage.removeItem(PWA_USER_KEY);
                localStorage.removeItem(PUSH_SUBSCRIBED_KEY);
                return false;
            }
        } catch (err) {
            console.log('PWA check error:', err);
            return false;
        }
    };

    // PWA Login
    window.pwaLogin = async function(username, password) {
        try {
            const res = await fetch('/api/pwa/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ username, password })
            });

            const data = await res.json();

            if (res.ok && data.token) {
                localStorage.setItem(PWA_TOKEN_KEY, data.token);
                localStorage.setItem(PWA_USER_KEY, JSON.stringify(data.user));
                return { success: true, user: data.user };
            } else {
                return { success: false, message: data.message || 'Login gagal.' };
            }
        } catch (err) {
            return { success: false, message: 'Koneksi error.' };
        }
    };

    // PWA Logout
    window.pwaLogout = async function() {
        const token = localStorage.getItem(PWA_TOKEN_KEY);
        if (token) {
            try {
                await fetch('/api/pwa/logout', {
                    method: 'POST',
                    headers: {
                        'X-PWA-Token': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            } catch (err) {}
        }
        localStorage.removeItem(PWA_TOKEN_KEY);
        localStorage.removeItem(PWA_USER_KEY);
        localStorage.removeItem(PUSH_SUBSCRIBED_KEY);
    };

    // Check if running as PWA
    window.isPWA = function() {
        return window.matchMedia('(display-mode: standalone)').matches ||
               window.navigator.standalone === true;
    };

    // Get stored user
    window.getPwaUser = function() {
        const user = localStorage.getItem(PWA_USER_KEY);
        return user ? JSON.parse(user) : null;
    };

    // ── Push Notification ──

    function getAuthToken() {
        return localStorage.getItem(PWA_TOKEN_KEY);
    }

    async function fetchVapidPublicKey() {
        const token = getAuthToken();
        if (!token) return null;

        const res = await fetch('/api/pwa/vapid-key', {
            headers: {
                'X-PWA-Token': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (res.ok) {
            const data = await res.json();
            return data.publicKey;
        }
        return null;
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    window.pwaSubscribePush = async function() {
        const token = getAuthToken();
        if (!token) {
            return { success: false, message: 'Tidak terautentikasi.' };
        }

        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            return { success: false, message: 'Browser tidak mendukung push notification.' };
        }

        try {
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                return { success: false, message: 'Izin notifikasi ditolak.' };
            }

            const vapidKey = await fetchVapidPublicKey();
            if (!vapidKey) {
                return { success: false, message: 'Gagal mengambil VAPID key.' };
            }

            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidKey)
            });

            const sub = subscription.toJSON();
            const res = await fetch('/api/pwa/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-PWA-Token': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    endpoint: sub.endpoint,
                    publicKey: sub.keys.p256dh,
                    authToken: sub.keys.auth
                })
            });

            if (res.ok) {
                localStorage.setItem(PUSH_SUBSCRIBED_KEY, 'true');
                return { success: true };
            } else {
                return { success: false, message: 'Gagal menyimpan subscription.' };
            }
        } catch (err) {
            console.error('Push subscribe error:', err);
            return { success: false, message: 'Gagal subscribe push notification.' };
        }
    };

    window.pwaUnsubscribePush = async function() {
        const token = getAuthToken();
        if (!token) return { success: false };

        try {
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();

            if (subscription) {
                await subscription.unsubscribe();

                await fetch('/api/pwa/unsubscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-PWA-Token': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ endpoint: subscription.endpoint })
                });
            }

            localStorage.removeItem(PUSH_SUBSCRIBED_KEY);
            return { success: true };
        } catch (err) {
            console.error('Push unsubscribe error:', err);
            return { success: false };
        }
    };

    window.pwaIsSubscribed = function() {
        return localStorage.getItem(PUSH_SUBSCRIBED_KEY) === 'true';
    };

    window.pwaCheckSubscription = async function() {
        const token = getAuthToken();
        if (!token) return false;

        try {
            const res = await fetch('/api/pwa/push-status', {
                headers: {
                    'X-PWA-Token': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (res.ok) {
                const data = await res.json();
                localStorage.setItem(PUSH_SUBSCRIBED_KEY, data.subscribed ? 'true' : 'false');
                return data.subscribed;
            }
        } catch (err) {}
        return false;
    };

    // ── Background Sync (nilai only) ──

    window.pwaQueueForSync = async function(url, payload) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        const db = await openIDB();
        const tx = db.transaction('pwa_sync_queue', 'readwrite');
        const store = tx.objectStore('pwa_sync_queue');

        store.add({
            url: url,
            payload: payload,
            csrfToken: csrfToken,
            timestamp: Date.now()
        });

        const registration = await navigator.serviceWorker.ready;
        if ('sync' in registration) {
            await registration.sync.register('sync-nilai');
        }
    };

    function openIDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open('erapor-pwa', 1);

            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                if (!db.objectStoreNames.contains('pwa_sync_queue')) {
                    const store = db.createObjectStore('pwa_sync_queue', {
                        keyPath: 'id',
                        autoIncrement: true
                    });
                    store.createIndex('timestamp', 'timestamp');
                }
            };

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    // ── Update Prompt ──

    window.pwaShowUpdatePrompt = function(callback) {
        if (typeof callback === 'function') {
            window._pwaUpdateCallback = callback;
        }
    };

    window.pwaAcceptUpdate = function() {
        if (navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({ type: 'SKIP_WAITING' });
        }
        if (window._pwaUpdateCallback) {
            window._pwaUpdateCallback();
        }
        window.location.reload();
    };

    // ── Auto-login on page load (persist login after restart) ──
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => pwaAutoLogin());
    } else {
        pwaAutoLogin();
    }
})();
