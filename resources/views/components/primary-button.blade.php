<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-teal-primary border border-transparent rounded-pill font-bold text-sm text-white shadow-teal-glow tracking-wide hover:bg-teal-dark focus:outline-none focus:ring-2 focus:ring-teal-primary/30 focus:ring-offset-2 transition-all duration-200 active:scale-95 disabled:opacity-50',
    ]) }}>
    {{ $slot }}
</button>