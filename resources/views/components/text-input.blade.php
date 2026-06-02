@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-teal-primary/20 bg-white rounded-card shadow-sm focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20 transition-colors']) }}>