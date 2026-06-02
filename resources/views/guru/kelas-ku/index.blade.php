@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-building-office-2 class="w-7 h-7" />
            Kelas Saya
        </h1>
        <p class="mt-1 text-sm text-gray-500">Daftar kelas yang Anda wali atau ajar.</p>
    </div>

    @if($kelasWali->isNotEmpty())
    <div>
        <h2 class="text-lg font-extrabold text-gray-700 mb-3 flex items-center gap-2">
            <x-heroicon-o-star class="w-5 h-5 text-coral" />
            Kelas Wali
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($kelasWali as $kelas)
            <a href="{{ route('guru.anggota-kelas.index', $kelas) }}" class="stat-card stat-card-coral hover:shadow-coral-glow transition-all hover:scale-[1.02] active:scale-95 block animate-fade-up">
                <div class="flex items-center gap-3 mb-3">
                    <div class="icon-circle icon-circle-coral w-10 h-10">
                        <x-heroicon-o-building-office-2 class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="font-extrabold text-gray-800">{{ $kelas->nama_kelas }}</h3>
                        <p class="text-xs text-gray-400">{{ $kelas->tingkat?->nama ?? '-' }} · {{ $kelas->kompetensiKeahlian?->nama_kompetensi ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Siswa</span>
                    <span class="font-bold text-coral">{{ $kelas->siswaKelas()->count() }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($mapelKelas->isNotEmpty())
    <div>
        <h2 class="text-lg font-extrabold text-gray-700 mb-3 flex items-center gap-2">
            <x-heroicon-o-book-open class="w-5 h-5 text-sky" />
            Mapel Diajar
        </h2>
        @foreach($mapelKelas as $kelasNama => $items)
        <div class="bg-white rounded-card shadow-card overflow-hidden mb-4">
            <div class="px-4 py-3 bg-surface-base border-b border-gray-100">
                <h3 class="font-extrabold text-gray-700">{{ $kelasNama }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface-base text-left">
                        <tr>
                            <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Mapel</th>
                            <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden sm:table-cell">KKM</th>
                            <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($items as $mk)
                        <tr class="hover:bg-coral/5 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-bold text-gray-800">{{ $mk->mapel?->nama_mapel ?? '-' }}</p>
                                <p class="text-xs text-gray-400">{{ $mk->kelas?->tingkat?->nama ?? '-' }} {{ $mk->kelas?->nama_kelas ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $mk->kkm }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('guru.penilaian.index', ['kelas' => $mk->kelas_id, 'mapel' => $mk->mapel_id]) }}" class="btn-secondary text-xs px-3 py-1.5 inline-flex items-center gap-1">
                                    <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                                    Nilai
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if($kelasWali->isEmpty() && $mapelKelas->isEmpty())
    <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
        <x-heroicon-o-building-office-2 class="w-16 h-16 text-gray-300 mb-4" />
        <h2 class="text-xl font-extrabold text-gray-400 mb-2">Belum Ada Kelas</h2>
        <p class="text-gray-400">Anda belum memiliki kelas wali atau mapel yang diajar.</p>
    </div>
    @endif
</div>
@endsection
