<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-coral border border-transparent rounded-pill font-bold text-sm text-white shadow-coral-glow tracking-wide hover:bg-coral-dark focus:outline-none focus:ring-2 focus:ring-coral/30 focus:ring-offset-2 transition-all duration-200 active:scale-95 disabled:opacity-50',
    ]) }}>
    {{ $slot }}
</button>