@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
                <x-heroicon-o-sparkles class="w-7 h-7" />
                Project Kelas (P5)
            </h1>
            <p class="mt-1 text-sm text-gray-500">Kelola project P5 dan penilaian sub-elemen.</p>
        </div>
    </div>

    @if($kelasWali->isNotEmpty())
    <div class="bg-white rounded-card shadow-card p-4">
        <label class="block text-sm font-bold text-gray-500 mb-2">Pilih Kelas Wali</label>
        <div class="flex flex-wrap gap-2">
            @foreach($kelasWali as $k)
                <a href="{{ route('guru.project-kelas.index', $k) }}"
                    class="px-4 py-2 text-sm font-bold rounded-pill transition-all {{ $authorized && $kelas->id === $k->id ? 'bg-coral text-white shadow-coral-glow' : 'bg-surface-base text-gray-600 hover:bg-coral/10 hover:text-coral' }}">
                    {{ $k->nama_kelas }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($authorized)
        <div class="bg-white rounded-card shadow-card p-4 md:p-5">
            <h2 class="font-extrabold text-gray-700 mb-3">Tambah Project Baru</h2>
            <form method="POST" action="{{ route('guru.project-kelas.store') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                <div>
                    <select name="proyek_tema_id" required class="w-full px-3 py-2 border border-gray-200 rounded-card text-sm focus:ring-2 focus:ring-coral/30 focus:border-coral outline-none">
                        <option value="">Pilih Tema</option>
                        @foreach($temaList as $tema)
                            <option value="{{ $tema->id }}">{{ $tema->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <input type="text" name="judul" placeholder="Judul Project" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-card text-sm focus:ring-2 focus:ring-coral/30 focus:border-coral outline-none">
                </div>
                <div>
                    <button type="submit" class="w-full bg-coral text-white px-4 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                        Simpan
                    </button>
                </div>
                <div class="sm:col-span-3">
                    <textarea name="deskripsi" rows="2" placeholder="Deskripsi project (opsional)"
                        class="w-full px-3 py-2 border border-gray-200 rounded-card text-sm focus:ring-2 focus:ring-coral/30 focus:border-coral outline-none resize-none"></textarea>
                </div>
            </form>
        </div>

        <div class="space-y-4">
            @forelse($proyeks as $proyek)
            <div class="bg-white rounded-card shadow-card overflow-hidden">
                <div class="px-4 py-3 bg-surface-base border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-gray-700">{{ $proyek->judul }}</h3>
                        <p class="text-xs text-gray-400">{{ $proyek->proyekTema?->nama }} · {{ $proyek->subelemens->count() }} sub-elemen</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('guru.project-kelas.penilaian', $proyek) }}" class="btn-secondary text-xs px-3 py-1.5 inline-flex items-center gap-1">
                            <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                            Nilai
                        </a>
                        <form method="POST" action="{{ route('guru.project-kelas.destroy', $proyek) }}" class="inline" onsubmit="return confirm('Hapus project ini?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg transition-colors">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </form>
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
            <x-heroicon-o-sparkles class="w-16 h-16 text-gray-300 mb-4" />
            <h2 class="text-xl font-extrabold text-gray-400 mb-2">Bukan Wali Kelas</h2>
            <p class="text-gray-400">Anda belum terdaftar sebagai wali kelas.</p>
        </div>
    @endif
</div>
@endsection
