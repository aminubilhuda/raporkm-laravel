@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
                <x-heroicon-o-star class="w-7 h-7" />
                Penilaian Profil Pancasila
            </h1>
            <p class="mt-1 text-sm text-gray-500">Input nilai sub-elemen per siswa berdasarkan project P5.</p>
        </div>
    </div>

    @if($kelasWali->isNotEmpty())
    <div class="bg-white rounded-card shadow-card p-4">
        <label class="block text-sm font-bold text-gray-500 mb-2">Pilih Kelas Wali</label>
        <div class="flex flex-wrap gap-2">
            @foreach($kelasWali as $k)
                <a href="{{ route('guru.penilaian-profil-pancasila.index', $k) }}"
                    class="px-4 py-2 text-sm font-bold rounded-pill transition-all {{ $authorized && $kelas->id === $k->id ? 'bg-coral text-white shadow-coral-glow' : 'bg-surface-base text-gray-600 hover:bg-coral/10 hover:text-coral' }}">
                    {{ $k->nama_kelas }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($authorized)
        <div class="space-y-4">
            @forelse($proyeks as $proyek)
            <div class="bg-white rounded-card shadow-card overflow-hidden">
                <div class="px-4 py-3 bg-surface-base border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-gray-700">{{ $proyek->judul }}</h3>
                        <p class="text-xs text-gray-400">{{ $proyek->proyekTema?->nama_tema }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('guru.penilaian-profil-pancasila.penilaian', $proyek) }}" class="btn-secondary text-xs px-3 py-1.5 inline-flex items-center gap-1">
                            <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                            Nilai
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">Belum ada project P5 untuk kelas ini.</div>
            @endforelse
        </div>
    @elseif($kelasWali->isNotEmpty())
        <div class="text-center py-12 text-gray-400">Pilih kelas terlebih dahulu.</div>
    @else
        <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
            <x-heroicon-o-star class="w-16 h-16 text-gray-300 mb-4" />
            <h2 class="text-xl font-extrabold text-gray-400 mb-2">Bukan Wali Kelas</h2>
            <p class="text-gray-400">Anda belum terdaftar sebagai wali kelas.</p>
        </div>
    @endif
</div>
@endsection
