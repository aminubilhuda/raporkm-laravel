@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
                <x-heroicon-o-star class="w-7 h-7" />
                Penilaian Profil Pancasila
            </h1>
            <p class="mt-1 text-sm text-gray-500">{{ $proyekKelas->judul }} · {{ $proyekKelas->proyekTema?->nama_tema }}</p>
        </div>
        <a href="{{ route('guru.penilaian-profil-pancasila.index', $proyekKelas->kelas_id) }}" class="btn-secondary text-sm px-3 py-1.5">
            <x-heroicon-o-arrow-left class="w-4 h-4 inline-block mr-1" />
            Kembali
        </a>
    </div>

    @if($dimensiList->isNotEmpty())
        @foreach($dimensiList as $dimensi)
        <div class="bg-white rounded-card shadow-card overflow-hidden">
            <div class="px-4 py-3 bg-surface-base border-b border-gray-100">
                <h3 class="font-extrabold text-gray-700">{{ $dimensi->nama }}</h3>
            </div>
            @foreach($dimensi->elemens as $elemen)
            <div class="border-t border-gray-100">
                <div class="px-4 py-2 bg-coral/5 text-sm font-bold text-coral">{{ $elemen->nama }}</div>
                <form method="POST" action="{{ route('guru.penilaian-profil-pancasila.store', $proyekKelas) }}">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-surface-base text-left">
                                <tr>
                                    <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider min-w-[140px]">Siswa</th>
                                    @foreach($elemen->subElemens as $se)
                                    <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider min-w-[100px]">{{ $se->nama }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($siswa as $sk)
                                <tr class="hover:bg-coral/5 transition-colors">
                                    <td class="px-3 py-2 font-bold text-gray-700 text-xs">{{ $sk->siswa->nama_siswa ?? '-' }}</td>
                                    <input type="hidden" name="siswa_id[]" value="{{ $sk->siswa_id }}">
                                    @foreach($elemen->subElemens as $se)
                                    @php $key = "{$sk->siswa_id}_{$se->id}"; $ns = $nilaiSubelemen->get($key); @endphp
                                    <td class="px-2 py-2 text-center">
                                        <input type="number" name="nilai[{{ $sk->siswa_id }}][{{ $se->id }}]" value="{{ $ns->nilai ?? '' }}"
                                            min="0" max="100"
                                            class="w-16 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-coral/30 focus:border-coral outline-none">
                                    </td>
                                    @endforeach
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
        </div>
        @endforeach
    @else
        <div class="text-center py-12 text-gray-400">Belum ada data dimensi P5. Hubungi TU untuk menambahkan dimensi.</div>
    @endif
</div>
@endsection
