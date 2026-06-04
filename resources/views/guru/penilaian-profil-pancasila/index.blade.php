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
<div><h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2"><x-heroicon-o-star class="w-7 h-7" /> Penilaian Profil Pancasila</h1><p class="mt-1 text-sm text-gray-500">Penilaian dimensi dan elemen Profil Pelajar Pancasila melalui project P5.</p></div>
@if($proyeks->isEmpty())
<div class="flex flex-col items-center justify-center min-h-[40vh] text-center"><x-heroicon-o-star class="w-16 h-16 text-gray-300 mb-4" /><p class="text-gray-400 max-w-md">Penilaian Profil Pancasila dilakukan melalui project P5. Buka <a href="{{ route('guru.project-kelas.index') }}" class="text-coral font-bold hover:underline">Project Kelas</a> untuk memulai.</p></div>
@else
<div class="bg-white rounded-card shadow-card overflow-hidden">
<table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Project</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden md:table-cell">Tema</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden lg:table-cell">Kelas</th><th class="px-4 py-3 font-extrabold text-xs uppercase text-right">Aksi</th></tr></thead>
<tbody class="divide-y">@foreach($proyeks as $p)<tr class="hover:bg-coral-bg/30"><td class="px-4 py-3 font-bold">{{ $p->judul }}</td><td class="px-4 py-3 hidden md:table-cell text-gray-500">{{ $p->proyekTema?->nama }}</td><td class="px-4 py-3 hidden lg:table-cell text-gray-500">{{ $p->kelas?->nama_kelas }}</td><td class="px-4 py-3 text-right"><a href="{{ route('guru.project-kelas.penilaian', $p) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-coral shadow-coral-glow hover:bg-coral-dark text-white font-bold text-xs rounded-pill transition-colors"><x-heroicon-o-pencil-square class="w-4 h-4" /> Nilai</a></td></tr>@endforeach</tbody></table></div>
@endif
</div>
@endsection
