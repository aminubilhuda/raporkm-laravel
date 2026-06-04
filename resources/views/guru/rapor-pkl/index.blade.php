@extends('layouts.guru')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2"><x-heroicon-o-document-text class="w-7 h-7" /> Rapor PKL</h1><p class="mt-1 text-sm text-gray-500">Cetak laporan praktik kerja lapangan.</p></div>
@if($daftarPkl->isEmpty())
<div class="flex flex-col items-center justify-center min-h-[30vh] text-center"><x-heroicon-o-document-text class="w-16 h-16 text-gray-300 mb-4" /><p class="text-gray-400">Belum ada siswa PKL yang Anda bimbing.</p></div>
@else
<div class="bg-white rounded-card shadow-card overflow-hidden">
<table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Siswa</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden md:table-cell">Kelas</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden lg:table-cell">Perusahaan</th><th class="px-4 py-3 font-extrabold text-xs uppercase text-right">Aksi</th></tr></thead>
<tbody class="divide-y">@foreach($daftarPkl as $sp)<tr class="hover:bg-coral-bg/30"><td class="px-4 py-3 font-bold">{{ $sp->siswa->nama_siswa ?? '-' }}</td><td class="px-4 py-3 hidden md:table-cell text-gray-500">{{ $sp->kelas?->nama_kelas ?? '-' }}</td><td class="px-4 py-3 hidden lg:table-cell text-gray-500">{{ $sp->prakerin?->nama_perusahaan ?? '-' }}</td><td class="px-4 py-3 text-right"><a href="{{ route('guru.rapor-pkl.pdf', $sp) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-coral shadow-coral-glow hover:bg-coral-dark text-white font-bold text-xs rounded-pill transition-colors"><x-heroicon-o-document-arrow-down class="w-4 h-4" /> Cetak PDF</a></td></tr>@endforeach</tbody></table></div>
@endif
</div>
@endsection
