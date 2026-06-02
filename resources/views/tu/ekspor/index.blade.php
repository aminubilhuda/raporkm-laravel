@extends('layouts.tu')
@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-arrow-down-tray class="w-7 h-7" />
            Ekspor Data (Excel)
        </h1>
        <p class="mt-1 text-sm text-gray-500">Pilih jenis data dan filter untuk mengekspor ke file XLSX.</p>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-white rounded-card shadow-card p-5 border-l-[6px] border-l-teal-primary">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-2">Nilai</h2>
            <p class="text-sm text-gray-500 mb-3">Export nilai siswa per kelas per mapel.</p>
            <form method="GET" action="{{ route('tu.ekspor.nilai') }}" class="space-y-2">
                <select name="kelas_id" required class="w-full border-gray-300 rounded-pill px-3 py-2 text-sm">
                    <option value="">— Kelas —</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
                <select name="mapel_id" required class="w-full border-gray-300 rounded-pill px-3 py-2 text-sm">
                    <option value="">— Mapel —</option>
                    @foreach($mapelList as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
                <select name="tahun" required class="w-full border-gray-300 rounded-pill px-3 py-2 text-sm">
                    @foreach(\App\Models\TahunPelajaran::all() as $tp)
                        <option value="{{ $tp->id }}">{{ $tp->tahun }}</option>
                    @endforeach
                </select>
                <select name="semester" required class="w-full border-gray-300 rounded-pill px-3 py-2 text-sm">
                    @foreach(\App\Models\Semester::all() as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                    @endforeach
                </select>
                <button class="w-full bg-teal-primary hover:bg-teal-primary-dark text-white font-bold py-2 rounded-pill flex items-center justify-center gap-2">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Export Nilai
                </button>
            </form>
        </div>

        <div class="bg-white rounded-card shadow-card p-5 border-l-[6px] border-l-coral">
            <h2 class="text-lg font-extrabold text-coral-dark mb-2">Presensi</h2>
            <p class="text-sm text-gray-500 mb-3">Export rekap presensi siswa per kelas.</p>
            <form method="GET" action="{{ route('tu.ekspor.presensi') }}" class="space-y-2">
                <select name="kelas_id" required class="w-full border-gray-300 rounded-pill px-3 py-2 text-sm">
                    <option value="">— Kelas —</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
                <select name="tahun" required class="w-full border-gray-300 rounded-pill px-3 py-2 text-sm">
                    @foreach(\App\Models\TahunPelajaran::all() as $tp)
                        <option value="{{ $tp->id }}">{{ $tp->tahun }}</option>
                    @endforeach
                </select>
                <select name="semester" required class="w-full border-gray-300 rounded-pill px-3 py-2 text-sm">
                    @foreach(\App\Models\Semester::all() as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                    @endforeach
                </select>
                <button class="w-full bg-coral hover:bg-coral-dark text-white font-bold py-2 rounded-pill flex items-center justify-center gap-2">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Export Presensi
                </button>
            </form>
        </div>

        <div class="bg-white rounded-card shadow-card p-5 border-l-[6px] border-l-sky">
            <h2 class="text-lg font-extrabold text-sky-700 mb-2">Data Siswa</h2>
            <p class="text-sm text-gray-500 mb-3">Export biodata siswa (semua atau per kelas).</p>
            <form method="GET" action="{{ route('tu.ekspor.siswa') }}" class="space-y-2">
                <select name="kelas_id" class="w-full border-gray-300 rounded-pill px-3 py-2 text-sm">
                    <option value="">— Semua Siswa —</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
                <button class="w-full bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 rounded-pill flex items-center justify-center gap-2">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Export Siswa
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
