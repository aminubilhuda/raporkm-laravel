@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-pencil-square class="w-7 h-7" />
            Penilaian Kokurikuler
        </h1>
        <p class="mt-1 text-sm text-gray-500">Input nilai kegiatan kokurikuler per siswa.</p>
    </div>

    @if($kelasWali->isNotEmpty())
    <div class="bg-white rounded-card shadow-card p-4">
        <label class="block text-sm font-bold text-gray-500 mb-2">Pilih Kelas Wali</label>
        <div class="flex flex-wrap gap-2">
            @foreach($kelasWali as $k)
                <a href="{{ route('guru.penilaian-kokurikuler.index', $k) }}"
                    class="px-4 py-2 text-sm font-bold rounded-pill transition-all {{ $authorized && $kelas->id === $k->id ? 'bg-coral text-white shadow-coral-glow' : 'bg-surface-base text-gray-600 hover:bg-coral/10 hover:text-coral' }}">
                    {{ $k->nama_kelas }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($authorized && $dimensiList->isNotEmpty())
        @foreach($dimensiList as $dimensi)
        <div class="bg-white rounded-card shadow-card overflow-hidden">
            <div class="px-4 py-3 bg-surface-base border-b border-gray-100">
                <h3 class="font-extrabold text-gray-700">{{ $dimensi->nama }}</h3>
            </div>
            <form method="POST" action="{{ route('guru.penilaian-kokurikuler.store') }}">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                <input type="hidden" name="dimensi_id" value="{{ $dimensi->id }}">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-surface-base text-left">
                            <tr>
                                <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider min-w-[160px]">Siswa</th>
                                <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($siswa as $sk)
                            @php $key = "{$sk->siswa_id}_{$dimensi->id}"; $nk = $nilai->get($key); @endphp
                            <tr class="hover:bg-coral/5 transition-colors">
                                <td class="px-4 py-3 font-bold text-gray-700 text-sm">{{ $sk->siswa->nama_siswa ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <input type="hidden" name="siswa_id[]" value="{{ $sk->siswa_id }}">
                                    <input type="number" name="nilai[{{ $sk->siswa_id }}]" value="{{ $nk->nilai ?? '' }}"
                                        min="0" max="100"
                                        class="w-16 px-2 py-1 text-center border border-gray-200 rounded text-sm focus:ring-1 focus:ring-coral/30 focus:border-coral outline-none">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-100 text-right">
                    <button type="submit" class="bg-coral text-white px-5 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
        @endforeach
    @elseif($authorized && $dimensiList->isEmpty())
        <div class="text-center py-12 text-gray-400">Belum ada dimensi kokurikuler (dikelola TU).</div>
    @elseif($kelasWali->isNotEmpty())
        <div class="text-center py-12 text-gray-400">Pilih kelas terlebih dahulu.</div>
    @else
        <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
            <x-heroicon-o-pencil-square class="w-16 h-16 text-gray-300 mb-4" />
            <h2 class="text-xl font-extrabold text-gray-400 mb-2">Bukan Wali Kelas</h2>
            <p class="text-gray-400">Hanya wali kelas yang dapat menilai kokurikuler.</p>
        </div>
    @endif
</div>
@endsection
