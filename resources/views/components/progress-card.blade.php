@props([
    'title',
    'icon' => 'heroicon-o-chart-bar',
    'progress' => 0,
    'accent' => 'teal',
    'footer' => null,
    'animate' => true,
])

@php
    $gradientClass = match($accent) {
        'coral' => 'stat-card-coral',
        'gold' => 'stat-card-gold',
        'sky' => 'stat-card-sky',
        default => 'stat-card-teal',
    };
    $fillClass = match($accent) {
        'coral' => 'progress-fill-coral',
        'gold' => 'progress-fill-gold',
        default => 'progress-fill-teal',
    };
    $circleClass = match($accent) {
        'coral' => 'icon-circle-coral',
        'gold' => 'icon-circle-gold',
        'sky' => 'icon-circle-sky',
        default => 'icon-circle-teal',
    };
    $textClass = match($accent) {
        'coral' => 'text-coral',
        'gold' => 'text-gold-dark',
        'sky' => 'text-sky',
        default => 'text-teal-primary',
    };
    $pct = max(0, min(100, (int) $progress));
@endphp

<div class="stat-card {{ $gradientClass }} animate-fade-up">
    <div class="flex items-center gap-3 mb-3">
        <div class="icon-circle {{ $circleClass }} w-9 h-9">
            <x-dynamic-component :component="$icon" class="w-5 h-5" />
        </div>
        <h3 class="text-sm font-extrabold text-gray-700">{{ $title }}</h3>
    </div>

    <div class="progress-track">
        <div class="progress-fill {{ $fillClass }}" style="width: {{ $pct }}%"></div>
    </div>

    <div class="flex items-center justify-between mt-2">
        <span class="text-xs font-bold {{ $textClass }}">{{ $pct }}%</span>
        @if ($footer)
            <span class="text-xs text-gray-400">{{ $footer }}</span>
        @endif
    </div>
</div>