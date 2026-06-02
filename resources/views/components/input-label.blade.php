@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-sm text-teal-primary-dark mb-1']) }}>
    {{ $value ?? $slot }}
</label>