@props([
    'name' => auth()->user()->nama,
    'panel' => 'TU',
    'tahun' => '2025/2026',
    'semester' => 'Genap',
    'subtitle' => null,
    'accent' => 'teal',
])

@php
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $today = $days[now()->dayOfWeek] . ', ' . now()->day . ' ' . [
    'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
][now()->month - 1] . ' ' . now()->year;
    $gradient = $accent === 'coral' ? 'from-coral/5 to-coral/10' : 'from-teal-bg to-teal-primary/5';
    $iconClass = $accent === 'coral' ? 'bg-coral/10 text-coral' : 'bg-teal-primary/10 text-teal-primary';
    $nameClass = $accent === 'coral' ? 'text-coral-dark' : 'text-teal-primary-dark';
    $badgeBg = $accent === 'coral' ? 'bg-coral' : 'bg-teal-primary';
    $userIcon = auth()->user()->jabatan === 2 ? 'heroicon-o-user' : (auth()->user()->jabatan === 4 ? 'heroicon-o-user' : 'heroicon-o-academic-cap');
@endphp

<div class="animate-fade-up stagger-1">
    <div class="relative overflow-hidden rounded-card bg-gradient-to-br {{ $gradient }} p-5 md:p-6 shadow-card border border-teal-primary/5">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="icon-circle {{ $iconClass }} w-14 h-14 flex-shrink-0">
                <x-dynamic-component :component="$userIcon" class="w-7 h-7" />
            </div>

            <div class="flex-1 min-w-0">
                <h1 class="text-xl md:text-2xl font-extrabold {{ $nameClass }} leading-tight">
                    Selamat datang, {{ explode(' ', trim($name))[0] }}
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    Panel {{ $panel === 'TU' ? 'Tata Usaha' : ($panel === 'Guru' ? 'Guru' : $panel) }}
                    @if($subtitle)
                        <span class="mx-1.5 text-gray-300">·</span> {{ $subtitle }}
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="text-right">
                    <p class="text-xs text-gray-400">{{ $today }}</p>
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-pill text-xs font-bold {{ $badgeBg }} text-white">
                            <svg class="w-2.5 h-2.5" viewBox="0 0 10 10"><circle cx="5" cy="5" r="4" fill="currentColor"/></svg>
                            {{ $semester }} {{ $tahun }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>