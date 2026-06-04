// PWA Auto-Login Logic
(function() {
    'use strict';

    const PWA_TOKEN_KEY = 'pwa_token';
    const PWA_USER_KEY = 'pwa_user';

    // Register Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then((reg) => console.log('SW registered:', reg.scope))
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
                // Token invalid, clear storage
                localStorage.removeItem(PWA_TOKEN_KEY);
                localStorage.removeItem(PWA_USER_KEY);
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
})();
