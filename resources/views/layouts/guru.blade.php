<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'E-Rapor KM') }} - Guru</title>
    @if($sekolah?->favicon)
        <link rel="icon" type="image/png" href="{{ asset('storage/'.$sekolah->favicon) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2BA8A2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="E-Rapor">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full bg-surface-base">
    <!-- Sidebar overlay (mobile) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden" data-close-sidebar></div>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col shadow-lg bg-gradient-to-b from-coral-dark to-coral text-white -translate-x-full md:translate-x-0 transition-transform duration-300">
        @include('components.sidebar-guru')
    </aside>

    <!-- Main -->
    <div class="md:ml-64 min-h-screen flex flex-col">
        @include('components.topbar-guru', ['panel' => 'Guru'])

        <main class="flex-1 p-4 md:p-6 bg-dots">
            <x-flash-message />
            @yield('content')
        </main>
    </div>

    <script>
    (function() {
        // ── Sidebar ──
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function openSidebar()  {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.querySelectorAll('[data-toggle-sidebar]').forEach(el => el.addEventListener('click', openSidebar));
        document.querySelectorAll('[data-close-sidebar]').forEach(el => el.addEventListener('click', closeSidebar));

        // Auto-close sidebar when clicking a menu link
        document.querySelectorAll('.sidebar-link').forEach(el => el.addEventListener('click', closeSidebar));

        // Touch swipe left to close sidebar
        let touchStartX = 0;
        sidebar.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
        sidebar.addEventListener('touchend', e => {
            const diff = touchStartX - e.changedTouches[0].clientX;
            if (diff > 80) closeSidebar();
        }, { passive: true });

        // ── Dropdown ──
        const dropdown = document.getElementById('user-dropdown');
        if (dropdown) {
            const trigger = dropdown.querySelector('[data-dropdown-trigger]');
            const menu    = dropdown.querySelector('[data-dropdown-menu]');

            function openDD()  { menu.classList.remove('hidden'); }
            function closeDD() { menu.classList.add('hidden'); }

            trigger.addEventListener('click', (e) => { e.stopPropagation(); menu.classList.contains('hidden') ? openDD() : closeDD(); });
            menu.querySelectorAll('a, button').forEach(el => el.addEventListener('click', closeDD));

            document.addEventListener('click', (e) => { if (!dropdown.contains(e.target)) closeDD(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeDD(); closeSidebar(); } });
        }

    })();
    </script>

    <script src="{{ asset('js/pwa.js') }}"></script>

    <x-pwa-update-prompt />

    {{-- Tailwind JIT safelist --}}
    <div class="hidden hover:shadow-accent-glow hover:shadow-teal-glow hover:shadow-coral-glow hover:bg-teal-bg hover:bg-coral/5 hover:scale-[1.02] active:scale-95"></div>
</body>
</html>