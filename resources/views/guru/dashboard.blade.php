@extends('layouts.guru')

@section('content')
<div class="space-y-6 md:space-y-8">
    <x-welcome-banner
        panel="Guru"
        title="Selamat Datang, {{ auth()->user()->nama }}"
        subtitle="{{ $totalKelasWali > 0 ? $totalKelasWali.' Kelas Wali' : '' }} · {{ $totalMapelDiajar }} Mapel Diajar"
        accent="coral"
    />

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4">
        <x-quick-action icon="heroicon-o-pencil-square" label="Input Nilai" href="{{ route('guru.penilaian.index') }}" accent="coral" />
        <x-quick-action icon="heroicon-o-check-badge" label="TP Baru" href="{{ route('guru.tujuan-pembelajaran.index') }}" accent="gold" />
        <x-quick-action icon="heroicon-o-building-office-2" label="Kelas Saya" href="{{ route('guru.kelas-ku.index') }}" accent="sky" />
        <x-quick-action icon="heroicon-o-clipboard-document-check" label="Presensi" href="{{ route('guru.presensi.rekap') }}" accent="teal" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-5">
        <x-stat-card
            title="Kelas Wali" value="{{ $totalKelasWali }}" icon="heroicon-o-building-office-2"
            accent="coral" stagger="stagger-2"
        />
        <x-stat-card
            title="Mapel Diajar" value="{{ $totalMapelDiajar }}" icon="heroicon-o-book-open"
            accent="coral" stagger="stagger-3"
        />
        <x-stat-card
            title="Total Siswa" value="{{ $totalSiswa }}" icon="heroicon-o-users"
            accent="gold" stagger="stagger-4"
        />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5">
            @if($kelasWaliList->isNotEmpty())
                    @php $first = $kelasWaliList->first(); @endphp
                    <x-progress-card
                        title="Kelas Wali: {{ $first->nama_kelas ?? '-' }}"
                icon="heroicon-o-academic-cap"
                accent="coral"
                :progress="0"
                footer="Siswa: {{ $siswaWali }}"
            />
        @else
            <x-progress-card
                title="Belum Ada Kelas Wali"
                icon="heroicon-o-academic-cap"
                accent="coral"
                :progress="0"
                footer="-"
            />
        @endif

        <div class="stat-card stat-card-gold animate-fade-up stagger-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="icon-circle icon-circle-gold w-9 h-9">
                    <x-heroicon-o-calendar-days class="w-5 h-5" />
                </div>
                <h3 class="text-sm font-extrabold text-gray-700">Timeline Penting</h3>
            </div>
            <div class="space-y-2.5">
                <div class="flex items-center gap-2 text-sm">
                    <div class="w-2 h-2 rounded-full bg-coral flex-shrink-0"></div>
                    <span class="text-gray-500">Batas input nilai</span>
                    <span class="ml-auto font-bold text-coral">10 Jun</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <div class="w-2 h-2 rounded-full bg-gold flex-shrink-0"></div>
                    <span class="text-gray-500">Cetak rapor mid</span>
                    <span class="ml-auto font-bold text-gold-dark">15 Jun</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <div class="w-2 h-2 rounded-full bg-sky flex-shrink-0"></div>
                    <span class="text-gray-500">Pembagian rapor</span>
                    <span class="ml-auto font-bold text-sky">20 Jun</span>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-sky animate-fade-up stagger-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="icon-circle icon-circle-sky w-9 h-9">
                    <x-heroicon-o-chart-bar class="w-5 h-5" />
                </div>
                <h3 class="text-sm font-extrabold text-gray-700">Ringkasan</h3>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Total siswa diampu</span>
                    <span class="font-bold text-sky">{{ $totalSiswa }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Kelas wali</span>
                    <span class="font-bold text-coral">{{ $siswaWali }} siswa</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Kelas diajar</span>
                    <span class="font-bold text-teal-primary">{{ $mapelDiajar->pluck('kelas_id')->unique()->count() }} kelas</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
