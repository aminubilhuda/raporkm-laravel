@props([
    'icon' => 'heroicon-o-plus-circle',
    'label' => 'Aksi',
    'href' => '#',
    'accent' => 'teal',
])

@php
    $bgClass = match($accent) {
        'gold' => 'quick-action-gold',
        'coral' => 'quick-action-coral',
        'sky' => 'quick-action-sky',
        default => 'quick-action-teal',
    };
    $circleClass = match($accent) {
        'gold' => 'icon-circle-gold',
        'coral' => 'icon-circle-coral',
        'sky' => 'icon-circle-sky',
        default => 'icon-circle-teal',
    };
@endphp

<a href="{{ $href }}" class="quick-action {{ $bgClass }}">
    <div class="icon-circle {{ $circleClass }} w-10 h-10">
        <x-dynamic-component :component="$icon" class="w-5 h-5" />
    </div>
    <span class="text-xs font-bold text-gray-600 leading-tight">{{ $label }}</span>
</a>