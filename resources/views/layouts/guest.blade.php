<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'E-Rapor KM') }}</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2BA8A2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="auth-shell">
        <div class="auth-card">
            {{ $slot }}
        </div>

        <p class="mt-8 text-[11px] font-medium text-teal-primary/50 tracking-wide">&copy; {{ date('Y') }} E-Rapor KM</p>
    </div>

    <script src="{{ asset('js/pwa.js') }}"></script>
    <script>
        // Auto-login check for PWA
        document.addEventListener('DOMContentLoaded', async function() {
            const loggedIn = await window.pwaAutoLogin();
            if (loggedIn) {
                const user = window.getPwaUser();
                if (user) {
                    const redirectTo = user.jabatan == 2 ? '/tu/dashboard' : '/guru/dashboard';
                    window.location.href = redirectTo;
                }
            }
        });
    </script>
</body>
</html>