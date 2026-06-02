@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-document-chart-bar class="w-7 h-7" />
            Lager Nilai Kelas
        </h1>
        <p class="mt-1 text-sm text-gray-500">Konsolidasi nilai akhir per mapel per siswa.</p>
    </div>

    @if($kelasList->isNotEmpty())
    <div class="bg-white rounded-card shadow-card p-4">
        <label class="block text-sm font-bold text-gray-500 mb-2">Pilih Kelas</label>
        <div class="flex flex-wrap gap-2">
            @foreach($kelasList as $k)
                <a href="{{ route('guru.lager-nilai-kelas.index', $k) }}"
                    class="px-4 py-2 text-sm font-bold rounded-pill transition-all {{ $authorized && $kelas->id === $k->id ? 'bg-coral text-white shadow-coral-glow' : 'bg-surface-base text-gray-600 hover:bg-coral/10 hover:text-coral' }}">
                    {{ $k->nama_kelas }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($authorized && $siswa->isNotEmpty() && $mapelGuru->isNotEmpty())
        @foreach($mapelGuru as $mg)
        <div class="bg-white rounded-card shadow-card overflow-hidden">
            <div class="px-4 py-3 bg-surface-base border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-extrabold text-gray-700">
                    <x-heroicon-o-book-open class="w-4 h-4 inline-block mr-1 text-sky" />
                    {{ $mg->mapel->nama_mapel ?? '-' }}
                </h3>
                <span class="text-xs text-gray-400">KKM: {{ $mg->kkm }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface-base text-left">
                        <tr>
                            <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider min-w-[160px]">Siswa</th>
                            <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider w-16">Formatif</th>
                            <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider w-16">PH</th>
                            <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider w-16">AS</th>
                            <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider w-16">NA</th>
                            <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">Predikat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($siswa as $sk)
                        @php
                            $key = "{$sk->siswa_id}_{$mg->mapel_id}";
                            $nm = $nilaiMapel->get($key);
                            $formatif = $rataFormatif->get($sk->siswa_id) ?? 0;
                            $ph = $rataPh->get($sk->siswa_id) ?? 0;
                            $as = $nilaiAs->get($key)?->nilai ?? 0;
                            $na = $nm?->nilai ?? round(($formatif * 0.4 + $ph * 0.3 + $as * 0.3), 0);
                        @endphp
                        <tr class="hover:bg-coral/5 transition-colors">
                            <td class="px-3 py-2 font-bold text-gray-700 text-xs">{{ $sk->siswa->nama_siswa ?? '-' }}</td>
                            <td class="px-2 py-2 text-center text-gray-600">{{ $formatif ?: '-' }}</td>
                            <td class="px-2 py-2 text-center text-gray-600">{{ $ph ?: '-' }}</td>
                            <td class="px-2 py-2 text-center text-gray-600">{{ $as ?: '-' }}</td>
                            <td class="px-2 py-2 text-center font-bold {{ $na >= 75 ? 'text-success' : 'text-coral' }}">{{ $na ?: '-' }}</td>
                            <td class="px-2 py-2 text-center">
                                @if($nm?->predikat)
                                <span class="px-2 py-0.5 text-xs font-bold rounded-pill
                                    {{ $nm->predikat == 'SB' ? 'bg-teal/10 text-teal-primary' : '' }}
                                    {{ $nm->predikat == 'B' ? 'bg-sky/10 text-sky' : '' }}
                                    {{ $nm->predikat == 'C' ? 'bg-gold/10 text-gold-dark' : '' }}
                                    {{ $nm->predikat == 'PB' ? 'bg-coral/10 text-coral' : '' }}">
                                    {{ $nm->predikat }}
                                </span>
                                @else
                                <span class="text-gray-300">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    @elseif($authorized && $siswa->isEmpty())
        <div class="text-center py-12 text-gray-400">Belum ada siswa di kelas ini.</div>
    @elseif($authorized && $mapelGuru->isEmpty())
        <div class="text-center py-12 text-gray-400">Anda tidak mengajar mapel di kelas ini.</div>
    @elseif($kelasList->isNotEmpty())
        <div class="text-center py-12 text-gray-400">Pilih kelas terlebih dahulu.</div>
    @else
        <div class="text-center py-12 text-gray-400">Anda belum memiliki kelas.</div>
    @endif
</div>
@endsection
