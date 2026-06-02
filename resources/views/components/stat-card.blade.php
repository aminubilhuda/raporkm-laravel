@props([
    'title',
    'value' => '-',
    'icon' => 'heroicon-o-chart-bar',
    'accent' => 'teal',
    'trend' => null,
    'trendLabel' => null,
    'stagger' => null,
])

@php
    $gradientClass = match($accent) {
        'coral' => 'stat-card-coral',
        'gold' => 'stat-card-gold',
        'sky' => 'stat-card-sky',
        default => 'stat-card-teal',
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
    $trendColor = $trend > 0 ? 'text-success' : 'text-coral';
    $trendIcon = $trend > 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down';
@endphp

<div class="stat-card {{ $gradientClass }} {{ $stagger ? 'animate-fade-up ' . $stagger : '' }}">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">{{ $title }}</p>
            <p class="text-3xl md:text-4xl font-extrabold {{ $textClass }} tracking-tight">{{ $value }}</p>

            @if ($trend !== null)
                <div class="flex items-center gap-1 mt-2">
                    <x-dynamic-component :component="$trendIcon" class="w-3.5 h-3.5 {{ $trendColor }}" />
                    <span class="text-xs font-bold {{ $trendColor }}">{{ abs($trend) }}%</span>
                    @if ($trendLabel)
                        <span class="text-xs text-gray-400 ml-1">{{ $trendLabel }}</span>
                    @endif
                </div>
            @endif
        </div>

        <div class="icon-circle {{ $circleClass }}">
            <x-dynamic-component :component="$icon" class="w-6 h-6" />
        </div>
    </div>
</div>