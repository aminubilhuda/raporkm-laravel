@extends('layouts.tu')

@section('content')
<div class="space-y-6 md:space-y-8">
    {{-- Welcome Banner --}}
    <x-welcome-banner
        panel="TU"
        tahun="2025/2026"
        semester="Genap"
        accent="teal"
    />

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4">
        <x-quick-action icon="heroicon-o-user-plus" label="Tambah Siswa" href="#" accent="teal" />
        <x-quick-action icon="heroicon-o-arrow-up-tray" label="Import Data" href="#" accent="gold" />
        <x-quick-action icon="heroicon-o-printer" label="Cetak Rapor" href="#" accent="sky" />
        <x-quick-action icon="heroicon-o-document-chart-bar" label="Lihat Laporan" href="#" accent="coral" />
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
        <x-stat-card
            title="Total Siswa" value="{{ $totalSiswa }}" icon="heroicon-o-academic-cap"
            accent="teal" stagger="stagger-2"
        />
        <x-stat-card
            title="Total Kelas" value="{{ $totalKelas }}" icon="heroicon-o-building-office-2"
            accent="teal" stagger="stagger-3"
        />
        <x-stat-card
            title="Total Mapel" value="{{ $totalMapel }}" icon="heroicon-o-book-open"
            accent="gold" stagger="stagger-4"
        />
        <x-stat-card
            title="Total Guru" value="{{ $totalGuru }}" icon="heroicon-o-users"
            accent="sky" stagger="stagger-5"
        />
    </div>

    {{-- Bottom Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5">
        <x-progress-card
            title="Progress Semester" icon="heroicon-o-clock"
            accent="teal" :progress="65" footer="78 hari tersisa"
        />

        <div class="stat-card stat-card-gold animate-fade-up stagger-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="icon-circle icon-circle-gold w-9 h-9">
                    <x-heroicon-o-calendar-days class="w-5 h-5" />
                </div>
                <h3 class="text-sm font-extrabold text-gray-700">Pembagian Rapor</h3>
            </div>
            <div class="space-y-2.5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Rapor Mid Semester</span>
                    <span class="font-bold text-gold-dark">15 Jun 2026</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Rapor Semester</span>
                    <span class="font-bold text-gold-dark">20 Des 2026</span>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-sky animate-fade-up stagger-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="icon-circle icon-circle-sky w-9 h-9">
                    <x-heroicon-o-users class="w-5 h-5" />
                </div>
                <h3 class="text-sm font-extrabold text-gray-700">Sebaran Siswa</h3>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Laki-laki</span>
                    <span class="flex items-center gap-2">
                        <span class="font-bold text-sky">120</span>
                        <span class="text-xs text-gray-400">48%</span>
                    </span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill progress-fill-teal" style="width: 48%"></div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Perempuan</span>
                    <span class="flex items-center gap-2">
                        <span class="font-bold text-coral">130</span>
                        <span class="text-xs text-gray-400">52%</span>
                    </span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill progress-fill-coral" style="width: 52%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection