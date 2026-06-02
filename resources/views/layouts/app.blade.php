<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'E-Rapor KM') }} — {{ $title ?? 'Profil' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full bg-surface-base">
    <div class="min-h-screen flex flex-col">
        <!-- Topbar -->
        <nav class="bg-cream shadow-card sticky top-0 z-30 border-b border-teal-primary/10">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center gap-3">
                        <a href="{{ auth()->user()->jabatan === 2 ? route('tu.dashboard') : route('guru.dashboard') }}" class="flex items-center gap-2 text-teal-primary font-extrabold text-xl">
                            <x-heroicon-o-book-open class="w-6 h-6" />
                            <span class="hidden sm:inline">E-Rapor KM</span>
                        </a>
                        <span class="px-3 py-1 text-xs font-bold rounded-pill bg-teal-primary text-white">Profil</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="hidden sm:inline text-sm font-medium text-teal-primary-dark">{{ auth()->user()->nama }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-bold text-coral hover:bg-coral/5 rounded-pill transition-colors">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main class="flex-1 p-4 md:p-8">
            @if (isset($header))
                <div class="max-w-2xl mx-auto mb-6">
                    {{ $header }}
                </div>
            @endif
            <div class="max-w-2xl mx-auto space-y-6">
                {{ $slot }}
            </div>
        </main>

        <footer class="py-4 text-center text-xs text-teal-primary/50">
            &copy; {{ date('Y') }} E-Rapor KM — SMK Abdi Negara Tuban
        </footer>
    </div>
</body>
</html>