@extends('layouts.guru')
@section('content')
@php
    $user = auth()->user();
    $sekolah = \App\Models\Sekolah::first();
    $taId = $sekolah?->tahun_aktif;
    $semesterId = $sekolah?->semester_aktif;
    $kelasWali = $user->kelasWali()
        ->when($taId, fn ($q) => $q->where('kelas_wali.tahun_pelajaran_id', $taId))
        ->when($semesterId, fn ($q) => $q->where('kelas_wali.semester_id', $semesterId))
        ->with('tingkat', 'kompetensiKeahlian')
        ->get();
    $proyeks = \App\Models\ProyekKelas::whereIn('kelas_id', $kelasWali->pluck('id'))
        ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
        ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
        ->with(['proyekTema', 'kelas'])
        ->latest()
        ->get();
@endphp
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2"><x-heroicon-o-sparkles class="w-7 h-7" /> P5BK</h1><p class="mt-1 text-sm text-gray-500">Projek Penguatan Profil Pelajar Pancasila.</p></div>
@if($proyeks->isEmpty())
<div class="flex flex-col items-center justify-center min-h-[40vh] text-center"><x-heroicon-o-sparkles class="w-16 h-16 text-gray-300 mb-4" /><p class="text-gray-400 max-w-md">Belum ada project P5. Buat project melalui menu <a href="{{ route('guru.project-kelas.index') }}" class="text-coral font-bold hover:underline">Project Kelas</a>.</p></div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($proyeks as $p)
    <div class="bg-white rounded-card shadow-card p-5 border-l-[6px] border-l-coral">
        <h3 class="font-extrabold text-coral-dark">{{ $p->judul }}</h3>
        <p class="text-xs text-gray-500 mt-1">{{ $p->kelas?->nama_kelas }} · {{ $p->proyekTema?->nama }}</p>
        <p class="text-sm text-gray-600 mt-2">{{ Str::limit($p->deskripsi, 120) }}</p>
        <a href="{{ route('guru.project-kelas.penilaian', $p) }}" class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-coral hover:underline">Penilaian <x-heroicon-o-arrow-right class="w-4 h-4" /></a>
    </div>
    @endforeach
</div>
@endif
</div>
@endsection
