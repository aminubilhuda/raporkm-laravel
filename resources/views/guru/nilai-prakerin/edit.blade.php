@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-pencil-square class="w-7 h-7" />
            Input Nilai Prakerin
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Input nilai untuk <strong>{{ $siswaPrakerin->siswa->nama_siswa ?? '-' }}</strong>
            di <strong>{{ $siswaPrakerin->prakerin->nama_perusahaan ?? '-' }}</strong>
        </p>
    </div>

    <div class="bg-white rounded-card shadow-card p-5 border-l-[6px] border-l-coral">
        <div class="grid md:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Siswa</span>
                <p class="font-bold text-gray-700">{{ $siswaPrakerin->siswa->nama_siswa ?? '-' }}</p>
            </div>
            <div>
                <span class="text-gray-500">Kelas</span>
                <p class="font-bold text-gray-700">{{ $siswaPrakerin->kelas->nama_kelas ?? '-' }}</p>
            </div>
            <div>
                <span class="text-gray-500">Perusahaan</span>
                <p class="font-bold text-gray-700">{{ $siswaPrakerin->prakerin->nama_perusahaan ?? '-' }}</p>
            </div>
        </div>
    </div>

    @if($siswaPrakerin->status !== 'aktif')
    <div class="bg-red-50 text-red-700 text-sm p-4 rounded-card flex gap-2 items-start">
        <x-heroicon-o-exclamation-triangle class="w-5 h-5 flex-shrink-0 mt-0.5" />
        <div>Status siswa ini <strong>{{ $siswaPrakerin->status }}</strong>. Nilai tidak dapat diinput.</div>
    </div>
    @endif

    @if($siswaPrakerin->status === 'aktif')
    <form method="POST" action="{{ route('guru.nilai-prakerin.store', $siswaPrakerin->id) }}" class="bg-white rounded-card shadow-card overflow-hidden">
        @csrf
        <div class="px-4 py-3 bg-surface-base border-b border-gray-100">
            <h3 class="font-extrabold text-gray-700">Daftar Mata Pelajaran</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider min-w-[180px]">Mata Pelajaran</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-24">Nilai</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($mapelList as $mapel)
                    @php $existing = $existingNilai->get($mapel->id); @endphp
                    <tr class="hover:bg-coral/5 transition-colors">
                        <td class="px-4 py-3">
                            <input type="hidden" name="mapel_id[]" value="{{ $mapel->id }}">
                            <span class="font-bold text-gray-700">{{ $mapel->nama_mapel }}</span>
                            <span class="text-xs text-gray-400 ml-1">({{ $mapel->kode }})</span>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="nilai[]" value="{{ $existing->nilai ?? 0 }}"
                                min="0" max="100"
                                class="w-20 px-2 py-1 text-center border border-gray-200 rounded text-sm focus:ring-1 focus:ring-coral/30 focus:border-coral outline-none">
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" name="deskripsi[]" value="{{ $existing->deskripsi ?? '' }}"
                                placeholder="Catatan (opsional)"
                                class="w-full px-2 py-1 border border-gray-200 rounded text-sm focus:ring-1 focus:ring-coral/30 focus:border-coral outline-none">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
            <a href="{{ route('guru.nilai-prakerin.index') }}" class="text-gray-500 hover:text-gray-700 font-bold text-sm inline-flex items-center gap-1">
                <x-heroicon-o-arrow-left class="w-4 h-4" /> Kembali
            </a>
            <button type="submit" class="bg-coral text-white px-5 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                Simpan Nilai
            </button>
        </div>
    </form>
    @else
    <div class="text-center py-12 text-gray-400">
        <x-heroicon-o-x-circle class="w-16 h-16 mx-auto mb-4 text-gray-300" />
        <p>Siswa tidak aktif. Nilai tidak dapat diinput.</p>
        <a href="{{ route('guru.nilai-prakerin.index') }}" class="text-coral font-bold hover:underline mt-4 inline-block">Kembali</a>
    </div>
    @endif
</div>
@endsection