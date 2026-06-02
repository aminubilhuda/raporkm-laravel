@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-star class="w-7 h-7" />
            Penilaian Profil Pancasila
        </h1>
        <p class="mt-1 text-sm text-gray-500">Penilaian project P5 per sub-elemen. Kelola melalui menu Project Kelas.</p>
    </div>

    <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
        <x-heroicon-o-star class="w-16 h-16 text-gray-300 mb-4" />
        <p class="text-gray-400 max-w-md">Penilaian Profil Pancasila dilakukan melalui project P5. Silakan buka <a href="{{ route('guru.project-kelas.index') }}" class="text-coral font-bold hover:underline">Project Kelas</a> untuk memulai.</p>
    </div>
</div>
@endsection
