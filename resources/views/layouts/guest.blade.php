<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'E-Rapor KM') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 bg-gradient-to-br from-teal-bg to-teal-primary/10">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-card bg-white shadow-teal-glow mb-4">
                <x-heroicon-o-book-open class="w-8 h-8 text-teal-primary" />
            </div>
            <h1 class="text-3xl font-extrabold text-teal-primary-dark tracking-wide">E-Rapor KM</h1>
            <p class="text-sm font-medium text-teal-primary/70 mt-1 tracking-widest uppercase">Kurikulum Merdeka</p>
            <p class="text-xs text-gray-500 mt-0.5">SMK Abdi Negara Tuban</p>
        </div>

        <div class="w-full max-w-md bg-white rounded-card shadow-card overflow-hidden">
            {{ $slot }}
        </div>

        <p class="mt-8 text-xs text-teal-primary/50">&copy; {{ date('Y') }} E-Rapor KM</p>
    </div>
</body>
</html>