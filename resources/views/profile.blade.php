@php
    $isTU = auth()->user()->jabatan === 2;
    $layout = $isTU ? 'layouts.tu' : 'layouts.guru';
    $h1Class = $isTU ? 'text-teal-primary-dark' : 'text-coral-dark';
    $cardBorder = $isTU ? 'border-l-teal-primary' : 'border-l-coral';
@endphp
@extends($layout)

@section('content')
<div>
    <h1 class="text-2xl md:text-3xl font-extrabold {{ $h1Class }} flex items-center gap-2">
        <x-heroicon-o-user class="w-7 h-7" />
        Profil Pengguna
    </h1>
    <p class="mt-1 text-sm md:text-base text-gray-500">Kelola informasi akun, password, dan keamanan.</p>

    <div class="mt-6 md:mt-8 max-w-2xl space-y-6">
        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] {{ $cardBorder }}">
            <div class="max-w-xl">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-gold">
            <div class="max-w-xl">
                <livewire:profile.update-password-form />
            </div>
        </div>

        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-coral">
            <div class="max-w-xl">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</div>
@endsection