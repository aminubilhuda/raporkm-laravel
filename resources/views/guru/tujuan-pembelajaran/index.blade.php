@extends('layouts.guru')

@section('content')
<div class="space-y-6" x-data="{ deleteModal: false, deleteId: null, deleteAction: null, hasNilai: false, nilaiCount: 0, tpKode: '' }">
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
                            <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider text-center w-24">Nilai</th>
                            <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($tujuanPembelajaran as $tp)
                        <tr class="hover:bg-coral/5 transition-colors">
                            <td class="px-4 py-3 font-bold text-coral">{{ $tp->kode_tp }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $tp->nama_tp }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($tp->nilai_formatif_count > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                        {{ $tp->nilai_formatif_count }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button type="button"
                                    @click="deleteId = {{ $tp->id }}; deleteAction = '{{ route('guru.tujuan-pembelajaran.destroy', $tp) }}'; hasNilai = {{ $tp->nilai_formatif_count > 0 ? 'true' : 'false' }}; nilaiCount = {{ $tp->nilai_formatif_count }}; tpKode = '{{ $tp->kode_tp }}'; deleteModal = true"
                                    class="p-1.5 text-coral hover:bg-coral/5 rounded-lg transition-colors" title="Hapus">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-gray-400">Belum ada tujuan pembelajaran.</td>
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

    {{-- Delete Confirmation Modal --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50" @click="deleteModal = false" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        {{-- Modal Content --}}
        <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6 z-10" x-show="deleteModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center" :class="hasNilai ? 'bg-red-100' : 'bg-gray-100'">
                    <span :class="hasNilai ? 'text-red-600' : 'text-gray-500'">
                        <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                    </span>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-extrabold text-gray-800" x-text="hasNilai ? 'Hapus TP dengan Nilai?' : 'Hapus TP ini?'"></h3>
                    <p class="mt-2 text-sm text-gray-600">
                        <template x-if="hasNilai">
                            <span>TP <strong class="text-coral" x-text="tpKode"></strong> memiliki <strong class="text-red-600" x-text="nilaiCount + ' nilai'"></strong> yang akan <strong class="text-red-600">ikut terhapus permanen</strong> dan tidak dapat dikembalikan.</span>
                        </template>
                        <template x-if="!hasNilai">
                            <span>TP <strong class="text-coral" x-text="tpKode"></strong> akan dihapus. Tindakan ini tidak dapat dibatalkan.</span>
                        </template>
                    </p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="deleteModal = false" class="px-4 py-2 text-sm font-bold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <form :action="deleteAction" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white rounded-lg transition-colors" :class="hasNilai ? 'bg-red-600 hover:bg-red-700' : 'bg-coral hover:bg-coral-dark'">
                        <span x-text="hasNilai ? 'Ya, Hapus Semua' : 'Ya, Hapus'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
