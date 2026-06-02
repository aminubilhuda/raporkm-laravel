@php $panel ??= 'TU'; @endphp
@extends($panel === 'TU' ? 'layouts.tu' : 'layouts.guru')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <x-heroicon-o-wrench-screwdriver class="w-20 h-20 mb-4 {{ $panel === 'TU' ? 'text-teal-primary' : 'text-coral' }}" />
    <h1 class="text-3xl font-extrabold text-teal-primary-dark mb-2">Fitur Sedang Dikembangkan</h1>
    <p class="text-gray-500 max-w-md">Halaman <strong class="text-teal-primary">{{ $title ?? 'ini' }}</strong> akan tersedia di fase pengembangan selanjutnya.</p>

    <a href="{{ route($panel === 'TU' ? 'tu.dashboard' : 'guru.dashboard') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 {{ $panel === 'TU' ? 'bg-teal-primary shadow-teal-glow hover:bg-teal-dark' : 'bg-coral shadow-coral-glow hover:bg-coral-dark' }} text-white font-bold rounded-pill transition-colors">
        <x-heroicon-o-arrow-left class="w-5 h-5" />
        <span>Kembali ke Dashboard</span>
    </a>
</div>
@endsection