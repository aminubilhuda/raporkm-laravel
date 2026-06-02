@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-pencil-square class="w-7 h-7" />
            Penilaian
        </h1>
        <p class="mt-1 text-sm text-gray-500">Input nilai formatif, sumatif PH, sumatif TS, dan sumatif AS.</p>
    </div>

    {{-- Picker --}}
    @if($mapelKelasList->isNotEmpty())
    <div class="bg-white rounded-card shadow-card p-4">
        <label class="block text-sm font-bold text-gray-500 mb-2">Pilih Kelas & Mapel</label>
        <div class="flex flex-wrap gap-2">
            @foreach($mapelKelasList as $mk)
                <a href="{{ route('guru.penilaian.index', ['kelas' => $mk->kelas_id, 'mapel' => $mk->mapel_id]) }}"
                    class="px-4 py-2 text-sm font-bold rounded-pill transition-all {{ $authorized && $kelas->id === $mk->kelas_id && $mapel->id === $mk->mapel_id ? 'bg-coral text-white shadow-coral-glow' : 'bg-surface-base text-gray-600 hover:bg-coral/10 hover:text-coral' }}">
                    {{ $mk->kelas->nama_kelas }} · {{ $mk->mapel->nama_mapel }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($authorized && $tujuanPembelajaran->isNotEmpty() && $siswa->isNotEmpty())
        {{-- Formatif --}}
        <div class="bg-white rounded-card shadow-card overflow-hidden">
            <div class="px-4 py-3 bg-surface-base border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-extrabold text-gray-700 flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-5 h-5 text-coral" />
                    Nilai Formatif
                </h3>
                <span class="text-xs text-gray-400">Bobot 40%</span>
            </div>
            <div class="overflow-x-auto">
                <form method="POST" action="{{ route('guru.penilaian.formatif') }}">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                    <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">
                    <table class="w-full text-sm">
                        <thead class="bg-surface-base text-left">
                            <tr>
                                <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider sticky left-0 bg-surface-base min-w-[160px]">Siswa</th>
                                @foreach($tujuanPembelajaran as $tp)
                                <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider min-w-[140px]">
                                    <div>{{ $tp->kode_tp }}</div>
                                    <div class="flex gap-1 mt-1 justify-center text-[10px]">
                                        <span class="text-coral">N</span>
                                        <span class="text-sky">M</span>
                                        <span class="text-teal-primary">S</span>
                                    </div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($siswa as $sk)
                            <tr class="hover:bg-coral/5 transition-colors">
                                <td class="px-3 py-2 sticky left-0 bg-white font-bold text-gray-700 text-xs">
                                    <input type="hidden" name="siswa_id[]" value="{{ $sk->siswa_id }}">
                                    {{ $sk->siswa->nama_siswa ?? '-' }}
                                </td>
                                @foreach($tujuanPembelajaran as $tp)
                                @php $key = "{$sk->siswa_id}_{$tp->id}"; $nf = $nilaiFormatif->get($key); @endphp
                                <td class="px-1 py-2">
                                    <div class="flex gap-1 justify-center">
                                        <input type="number" name="nilai[{{ $tp->id }}][{{ $sk->siswa_id }}]" value="{{ $nf->nilai ?? '' }}" min="0" max="100"
                                            class="w-10 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-coral/30 focus:border-coral outline-none">
                                        <input type="number" name="middle[{{ $tp->id }}][{{ $sk->siswa_id }}]" value="{{ $nf->middle ?? '' }}" min="0" max="100"
                                            class="w-10 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-sky/30 focus:border-sky outline-none">
                                        <input type="number" name="nas[{{ $tp->id }}][{{ $sk->siswa_id }}]" value="{{ $nf->nas ?? '' }}" min="0" max="100"
                                            class="w-10 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-teal/30 focus:border-teal outline-none">
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-4 py-3 border-t border-gray-100 text-right">
                        <button type="submit" class="bg-coral text-white px-5 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                            Simpan Formatif
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sumatif PH --}}
        <div class="bg-white rounded-card shadow-card overflow-hidden">
            <div class="px-4 py-3 bg-surface-base border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-extrabold text-gray-700 flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-5 h-5 text-sky" />
                    Nilai Sumatif PH
                </h3>
                <span class="text-xs text-gray-400">Bobot 30%</span>
            </div>
            <div class="overflow-x-auto">
                <form method="POST" action="{{ route('guru.penilaian.sumatif-ph') }}">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                    <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">
                    <table class="w-full text-sm">
                        <thead class="bg-surface-base text-left">
                            <tr>
                                <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider sticky left-0 bg-surface-base min-w-[160px]">Siswa</th>
                                @foreach($tujuanPembelajaran as $tp)
                                <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider min-w-[120px]">
                                    <div>{{ $tp->kode_tp }}</div>
                                    <div class="text-[10px] font-normal text-gray-400">Nilai</div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($siswa as $sk)
                            <tr class="hover:bg-coral/5 transition-colors">
                                <td class="px-3 py-2 sticky left-0 bg-white font-bold text-gray-700 text-xs">
                                    <input type="hidden" name="siswa_id[]" value="{{ $sk->siswa_id }}">
                                    {{ $sk->siswa->nama_siswa ?? '-' }}
                                </td>
                                @foreach($tujuanPembelajaran as $tp)
                                @php $key = "{$sk->siswa_id}_{$tp->id}"; $nph = $nilaiSumatifPh->get($key); @endphp
                                <td class="px-1 py-2">
                                    <input type="number" name="nilai[{{ $tp->id }}][{{ $sk->siswa_id }}]" value="{{ $nph->nilai ?? '' }}" min="0" max="100"
                                        class="w-16 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-sky/30 focus:border-sky outline-none mx-auto block">
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-4 py-3 border-t border-gray-100 text-right">
                        <button type="submit" class="bg-coral text-white px-5 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                            Simpan Sumatif PH
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sumatif TS & AS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-card shadow-card overflow-hidden">
                <div class="px-4 py-3 bg-surface-base border-b border-gray-100">
                    <h3 class="font-extrabold text-gray-700 flex items-center gap-2">
                        <x-heroicon-o-document-arrow-up class="w-5 h-5 text-gold-dark" />
                        Nilai Sumatif TS
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <form method="POST" action="{{ route('guru.penilaian.sumatif-ts') }}">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                        <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">
                        <table class="w-full text-sm">
                            <thead class="bg-surface-base text-left">
                                <tr>
                                    <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Siswa</th>
                                    <th class="px-2 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($siswa as $sk)
                                @php $nts = $nilaiSumatifTs->get("{$sk->siswa_id}"); @endphp
                                <tr class="hover:bg-coral/5 transition-colors">
                                    <td class="px-3 py-2 font-bold text-gray-700 text-xs">{{ $sk->siswa->nama_siswa ?? '-' }}</td>
                                    <td class="px-2 py-2">
                                        <input type="hidden" name="siswa_id[]" value="{{ $sk->siswa_id }}">
                                        <input type="number" name="nilai[{{ $sk->siswa_id }}]" value="{{ $nts->nilai ?? '' }}" min="0" max="100"
                                            class="w-16 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-gold/30 focus:border-gold outline-none">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-4 py-3 border-t border-gray-100 text-right">
                            <button type="submit" class="bg-coral text-white px-4 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                                Simpan TS
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-card shadow-card overflow-hidden">
                <div class="px-4 py-3 bg-surface-base border-b border-gray-100">
                    <h3 class="font-extrabold text-gray-700 flex items-center gap-2">
                        <x-heroicon-o-document-arrow-down class="w-5 h-5 text-teal-primary" />
                        Nilai Sumatif AS
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <form method="POST" action="{{ route('guru.penilaian.sumatif-as') }}">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                        <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">
                        <table class="w-full text-sm">
                            <thead class="bg-surface-base text-left">
                                <tr>
                                    <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Siswa</th>
                                    <th class="px-2 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($siswa as $sk)
                                @php $nas = $nilaiSumatifAs->get("{$sk->siswa_id}"); @endphp
                                <tr class="hover:bg-coral/5 transition-colors">
                                    <td class="px-3 py-2 font-bold text-gray-700 text-xs">{{ $sk->siswa->nama_siswa ?? '-' }}</td>
                                    <td class="px-2 py-2">
                                        <input type="hidden" name="siswa_id[]" value="{{ $sk->siswa_id }}">
                                        <input type="number" name="nilai[{{ $sk->siswa_id }}]" value="{{ $nas->nilai ?? '' }}" min="0" max="100"
                                            class="w-16 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-teal/30 focus:border-teal outline-none">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-4 py-3 border-t border-gray-100 text-right">
                            <button type="submit" class="bg-coral text-white px-4 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                                Simpan AS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($authorized && $tujuanPembelajaran->isEmpty())
        <div class="flex flex-col items-center justify-center min-h-[30vh] text-center">
            <x-heroicon-o-check-badge class="w-12 h-12 text-gray-300 mb-3" />
            <p class="text-gray-400">Belum ada Tujuan Pembelajaran untuk kelas & mapel ini.</p>
            <a href="{{ route('guru.tujuan-pembelajaran.index', ['kelas' => $kelas?->id, 'mapel' => $mapel?->id]) }}" class="mt-3 text-coral font-bold text-sm hover:underline">
                Buat TP dulu →
            </a>
        </div>
    @elseif($authorized && $siswa->isEmpty())
        <div class="flex flex-col items-center justify-center min-h-[30vh] text-center">
            <x-heroicon-o-users class="w-12 h-12 text-gray-300 mb-3" />
            <p class="text-gray-400">Belum ada siswa di kelas ini.</p>
        </div>
    @elseif($mapelKelasList->isNotEmpty())
        <div class="flex flex-col items-center justify-center min-h-[30vh] text-center">
            <x-heroicon-o-cursor-arrow-rays class="w-12 h-12 text-gray-300 mb-3" />
            <p class="text-gray-400">Pilih kelas & mapel terlebih dahulu.</p>
        </div>
    @else
        <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
            <x-heroicon-o-pencil-square class="w-16 h-16 text-gray-300 mb-4" />
            <h2 class="text-xl font-extrabold text-gray-400 mb-2">Belum Ada Mapel</h2>
            <p class="text-gray-400">Anda belum terdaftar sebagai pengajar mapel apapun.</p>
        </div>
    @endif
</div>
@endsection
