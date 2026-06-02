@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
                <x-heroicon-o-check-badge class="w-7 h-7" />
                Tujuan Pembelajaran
            </h1>
            <p class="mt-1 text-sm text-gray-500">Kelola TP per mata pelajaran dan kelas.</p>
        </div>
    </div>

    {{-- Picker --}}
    @if($mapelKelasList->isNotEmpty())
    <div class="bg-white rounded-card shadow-card p-4">
        <label class="block text-sm font-bold text-gray-500 mb-2">Pilih Kelas & Mapel</label>
        <div class="flex flex-wrap gap-2">
            @foreach($mapelKelasList as $mk)
                <a href="{{ route('guru.tujuan-pembelajaran.index', ['kelas' => $mk->kelas_id, 'mapel' => $mk->mapel_id]) }}"
                    class="px-4 py-2 text-sm font-bold rounded-pill transition-all {{ $selected && $selected->id === $mk->id ? 'bg-coral text-white shadow-coral-glow' : 'bg-surface-base text-gray-600 hover:bg-coral/10 hover:text-coral' }}">
                    {{ $mk->kelas->nama_kelas }} · {{ $mk->mapel->nama_mapel }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($selected)
        {{-- Add TP Form --}}
        <div class="bg-white rounded-card shadow-card p-4 md:p-5">
            <h2 class="font-extrabold text-gray-700 mb-3">Tambah TP Baru</h2>
            <form method="POST" action="{{ route('guru.tujuan-pembelajaran.store') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">
                <div>
                    <input type="text" name="kode_tp" placeholder="Kode TP (contoh: 3.1)" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-card text-sm focus:ring-2 focus:ring-coral/30 focus:border-coral outline-none">
                </div>
                <div class="sm:col-span-2">
                    <input type="text" name="nama_tp" placeholder="Deskripsi Tujuan Pembelajaran" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-card text-sm focus:ring-2 focus:ring-coral/30 focus:border-coral outline-none">
                </div>
                <div class="sm:col-span-3">
                    <button type="submit" class="bg-coral text-white px-4 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>

        {{-- TP List --}}
        <div class="bg-white rounded-card shadow-card overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-surface-base">
                <h3 class="font-extrabold text-gray-700">Daftar TP · {{ $kelas->nama_kelas }} · {{ $mapel->nama_mapel }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface-base text-left">
                        <tr>
                            <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-32">Kode</th>
                            <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Deskripsi</th>
                            <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($tujuanPembelajaran as $tp)
                        <tr class="hover:bg-coral/5 transition-colors">
                            <td class="px-4 py-3 font-bold text-coral">{{ $tp->kode_tp }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $tp->nama_tp }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('guru.tujuan-pembelajaran.destroy', $tp) }}" class="inline" onsubmit="return confirm('Hapus TP ini?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg transition-colors" title="Hapus">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-12 text-center text-gray-400">Belum ada tujuan pembelajaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($mapelKelasList->isNotEmpty())
        <div class="flex flex-col items-center justify-center min-h-[30vh] text-center">
            <x-heroicon-o-cursor-arrow-rays class="w-12 h-12 text-gray-300 mb-3" />
            <p class="text-gray-400">Pilih kelas & mapel terlebih dahulu.</p>
        </div>
    @else
        <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
            <x-heroicon-o-check-badge class="w-16 h-16 text-gray-300 mb-4" />
            <h2 class="text-xl font-extrabold text-gray-400 mb-2">Belum Ada Mapel</h2>
            <p class="text-gray-400">Anda belum terdaftar sebagai pengajar mapel apapun.</p>
        </div>
    @endif
</div>
@endsection
