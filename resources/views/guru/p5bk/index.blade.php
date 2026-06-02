@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-sparkles class="w-7 h-7" />
            P5BK
        </h1>
        <p class="mt-1 text-sm text-gray-500">Projek Penguatan Profil Pelajar Pancasila.</p>
    </div>

    <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
        <x-heroicon-o-sparkles class="w-16 h-16 text-gray-300 mb-4" />
        <p class="text-gray-400 max-w-md">Kelola project P5 dan penilaian sub-elemen melalui menu <a href="{{ route('guru.project-kelas.index') }}" class="text-coral font-bold hover:underline">Project Kelas</a>.</p>
    </div>
</div>
@endsection
