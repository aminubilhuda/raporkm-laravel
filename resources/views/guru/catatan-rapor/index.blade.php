@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-clipboard-document-list class="w-7 h-7" />
            Catatan Rapor
        </h1>
        <p class="mt-1 text-sm text-gray-500">Catatan wali kelas untuk rapor siswa.</p>
    </div>

    @if($kelasWali->isNotEmpty())
    <div class="bg-white rounded-card shadow-card p-4">
        <label class="block text-sm font-bold text-gray-500 mb-2">Pilih Kelas Wali</label>
        <div class="flex flex-wrap gap-2">
            @foreach($kelasWali as $k)
                <a href="{{ route('guru.catatan-rapor.index', $k) }}"
                    class="px-4 py-2 text-sm font-bold rounded-pill transition-all {{ $authorized && $kelas->id === $k->id ? 'bg-coral text-white shadow-coral-glow' : 'bg-surface-base text-gray-600 hover:bg-coral/10 hover:text-coral' }}">
                    {{ $k->nama_kelas }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($authorized)
    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-surface-base">
            <h3 class="font-extrabold text-gray-700">{{ $kelas->nama_kelas }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider min-w-[160px]">Siswa</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Catatan</th>
                        <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($siswa as $sk)
                    <tr class="hover:bg-coral/5 transition-colors">
                        <form method="POST" action="{{ route('guru.catatan-rapor.store') }}">
                            @csrf
                            <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                            <input type="hidden" name="siswa_id" value="{{ $sk->siswa_id }}">
                            <td class="px-4 py-3 font-bold text-gray-700">{{ $sk->siswa->nama_siswa ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <textarea name="catatan" rows="2"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-card text-sm focus:ring-2 focus:ring-coral/30 focus:border-coral outline-none resize-none"
                                    placeholder="Catatan wali kelas...">{{ $catatan->get($sk->siswa_id)?->catatan }}</textarea>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button type="submit" class="text-coral font-bold text-xs hover:text-coral-dark transition-colors">
                                    Simpan
                                </button>
                            </td>
                        </form>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-12 text-center text-gray-400">Belum ada siswa di kelas ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @elseif($kelasWali->isNotEmpty())
        <div class="text-center py-12 text-gray-400">Pilih kelas terlebih dahulu.</div>
    @else
        <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
            <x-heroicon-o-clipboard-document-list class="w-16 h-16 text-gray-300 mb-4" />
            <h2 class="text-xl font-extrabold text-gray-400 mb-2">Bukan Wali Kelas</h2>
            <p class="text-gray-400">Anda belum terdaftar sebagai wali kelas manapun.</p>
        </div>
    @endif
</div>
@endsection
