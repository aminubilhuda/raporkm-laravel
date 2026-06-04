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
        {{-- Export Nilai --}}
        <div class="bg-white rounded-card shadow-card p-5 border-l-[6px] border-l-teal-primary">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-2">Nilai</h2>
            <p class="text-sm text-gray-500 mb-3">Export nilai siswa per kelas per mapel.</p>
            <form method="GET" action="{{ route('tu.ekspor.nilai') }}" class="space-y-2" onsubmit="showLoading(this)">
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
                    @foreach($tahunList as $tp)
                        <option value="{{ $tp->id }}" {{ $tp->status ? 'selected' : '' }}>{{ $tp->tahun }}</option>
                    @endforeach
                </select>
                <select name="semester" required class="w-full border-gray-300 rounded-pill px-3 py-2 text-sm">
                    @foreach($semesterList as $s)
                        <option value="{{ $s->id }}" {{ $s->status ? 'selected' : '' }}>{{ $s->nama }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-export-teal w-full bg-teal-primary hover:bg-teal-primary-dark text-white font-bold py-2 rounded-pill flex items-center justify-center gap-2">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> <span class="btn-text">Export Nilai</span>
                </button>
            </form>
        </div>

        {{-- Export Presensi --}}
        <div class="bg-white rounded-card shadow-card p-5 border-l-[6px] border-l-coral">
            <h2 class="text-lg font-extrabold text-coral-dark mb-2">Presensi</h2>
            <p class="text-sm text-gray-500 mb-3">Export rekap presensi siswa per kelas.</p>
            <form method="GET" action="{{ route('tu.ekspor.presensi') }}" class="space-y-2" onsubmit="showLoading(this)">
                <select name="kelas_id" required class="w-full border-gray-300 rounded-pill px-3 py-2 text-sm">
                    <option value="">— Kelas —</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
                <select name="tahun" required class="w-full border-gray-300 rounded-pill px-3 py-2 text-sm">
                    @foreach($tahunList as $tp)
                        <option value="{{ $tp->id }}" {{ $tp->status ? 'selected' : '' }}>{{ $tp->tahun }}</option>
                    @endforeach
                </select>
                <select name="semester" required class="w-full border-gray-300 rounded-pill px-3 py-2 text-sm">
                    @foreach($semesterList as $s)
                        <option value="{{ $s->id }}" {{ $s->status ? 'selected' : '' }}>{{ $s->nama }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-export-coral w-full bg-coral hover:bg-coral-dark text-white font-bold py-2 rounded-pill flex items-center justify-center gap-2">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> <span class="btn-text">Export Presensi</span>
                </button>
            </form>
        </div>

        {{-- Export Siswa --}}
        <div class="bg-white rounded-card shadow-card p-5 border-l-[6px] border-l-sky">
            <h2 class="text-lg font-extrabold text-sky-700 mb-2">Data Siswa</h2>
            <p class="text-sm text-gray-500 mb-3">Export biodata siswa (semua atau per kelas).</p>
            <form method="GET" action="{{ route('tu.ekspor.siswa') }}" class="space-y-2" onsubmit="showLoading(this)">
                <select name="kelas_id" class="w-full border-gray-300 rounded-pill px-3 py-2 text-sm">
                    <option value="">— Semua Siswa —</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-export-sky w-full bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 rounded-pill flex items-center justify-center gap-2">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> <span class="btn-text">Export Siswa</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function showLoading(form) {
        const btn = form.querySelector('button[type="submit"]');
        const text = btn.querySelector('.btn-text');
        const icon = btn.querySelector('svg');

        btn.disabled = true;
        text.innerText = 'Mengunduh...';
        if (icon) icon.classList.add('animate-spin');

        setTimeout(() => {
            btn.disabled = false;
            text.innerText = text.dataset.original || text.innerText.replace('Mengunduh...', '');
            if (icon) icon.classList.remove('animate-spin');
        }, 5000);
    }

    document.querySelectorAll('.btn-text').forEach(el => {
        el.dataset.original = el.innerText;
    });
</script>
@endsection
