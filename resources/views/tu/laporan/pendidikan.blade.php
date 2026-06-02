@extends('layouts.tu')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
                <x-heroicon-o-chart-bar class="w-7 h-7" />
                Laporan Pendidikan
            </h1>
            <p class="mt-1 text-sm text-gray-500">Rekap agregat nilai, presensi, dan distribusi predikat per tahun ajaran.</p>
        </div>
        <form method="GET" class="flex gap-2 items-end">
            <div>
                <label class="block text-xs text-gray-500">Tahun</label>
                <select name="tahun" class="border-gray-300 rounded-pill px-3 py-1.5 text-sm">
                    @foreach($tahunList as $tp)
                        <option value="{{ $tp->id }}" @selected($tahunId == $tp->id)>{{ $tp->tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500">Semester</label>
                <select name="semester" class="border-gray-300 rounded-pill px-3 py-1.5 text-sm">
                    @foreach($semesterList as $s)
                        <option value="{{ $s->id }}" @selected($semesterId == $s->id)>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <button class="bg-teal-primary hover:bg-teal-primary-dark text-white font-bold px-4 py-1.5 rounded-pill text-sm">Terapkan</button>
        </form>
    </div>

    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-white rounded-card shadow-card p-4">
            <p class="text-xs uppercase text-gray-500">Kelas Aktif</p>
            <p class="text-2xl font-extrabold text-teal-primary-dark">{{ $kelas_list->count() }}</p>
        </div>
        <div class="bg-white rounded-card shadow-card p-4">
            <p class="text-xs uppercase text-gray-500">Mapel Dinilai</p>
            <p class="text-2xl font-extrabold text-coral">{{ count($nilai_per_mapel) }}</p>
        </div>
        <div class="bg-white rounded-card shadow-card p-4">
            <p class="text-xs uppercase text-gray-500">Hadir</p>
            <p class="text-2xl font-extrabold text-emerald-600">{{ $presensi_rekap['hadir'] }}</p>
        </div>
        <div class="bg-white rounded-card shadow-card p-4">
            <p class="text-xs uppercase text-gray-500">Tanpa Keterangan</p>
            <p class="text-2xl font-extrabold text-rose-600">{{ $presensi_rekap['alpha'] }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        <div class="bg-white rounded-card shadow-card p-5">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-3">Rata-rata Nilai per Mapel</h2>
            <table class="w-full text-sm">
                <thead class="bg-teal-50 text-teal-primary-dark">
                    <tr>
                        <th class="text-left px-3 py-2 rounded-l-pill">Mapel</th>
                        <th class="text-center px-3 py-2">Rata-rata</th>
                        <th class="text-center px-3 py-2">Min</th>
                        <th class="text-center px-3 py-2">Max</th>
                        <th class="text-center px-3 py-2 rounded-r-pill">N</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nilai_per_mapel as $row)
                        <tr class="border-b last:border-0">
                            <td class="px-3 py-2 font-semibold">{{ $row['mapel']->nama_mapel }}</td>
                            <td class="text-center px-3 py-2 font-bold text-teal-primary-dark">{{ $row['rata_rata'] ?? '—' }}</td>
                            <td class="text-center px-3 py-2">{{ $row['min'] ?? '—' }}</td>
                            <td class="text-center px-3 py-2">{{ $row['max'] ?? '—' }}</td>
                            <td class="text-center px-3 py-2">{{ $row['jumlah'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-gray-400 py-3">Belum ada data nilai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-card shadow-card p-5">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-3">Distribusi Predikat</h2>
            <table class="w-full text-sm">
                <thead class="bg-coral-50 text-coral-dark">
                    <tr>
                        <th class="text-left px-3 py-2 rounded-l-pill">Predikat</th>
                        <th class="text-center px-3 py-2">Jumlah</th>
                        <th class="text-center px-3 py-2 rounded-r-pill">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($distribusi_predikat as $d)
                        <tr class="border-b last:border-0">
                            <td class="px-3 py-2 font-bold">{{ $d['predikat'] }}</td>
                            <td class="text-center px-3 py-2">{{ $d['jumlah'] }}</td>
                            <td class="text-center px-3 py-2">{{ $d['persen'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        <div class="bg-white rounded-card shadow-card p-5">
            <h2 class="text-lg font-extrabold text-emerald-700 mb-3">Top 10 Siswa</h2>
            <ol class="space-y-1 text-sm">
                @forelse($top_bottom['top'] as $i => $r)
                    <li class="flex justify-between border-b pb-1 last:border-0">
                        <span><span class="font-bold text-emerald-700">{{ $i+1 }}.</span> {{ $r['siswa']?->nama_siswa ?? '—' }}</span>
                        <span class="font-bold">{{ $r['rata_rata'] }}</span>
                    </li>
                @empty
                    <li class="text-gray-400">Belum ada data.</li>
                @endforelse
            </ol>
        </div>
        <div class="bg-white rounded-card shadow-card p-5">
            <h2 class="text-lg font-extrabold text-rose-700 mb-3">Bottom 10 Siswa</h2>
            <ol class="space-y-1 text-sm">
                @forelse($top_bottom['bottom'] as $i => $r)
                    <li class="flex justify-between border-b pb-1 last:border-0">
                        <span><span class="font-bold text-rose-700">{{ $i+1 }}.</span> {{ $r['siswa']?->nama_siswa ?? '—' }}</span>
                        <span class="font-bold">{{ $r['rata_rata'] }}</span>
                    </li>
                @empty
                    <li class="text-gray-400">Belum ada data.</li>
                @endforelse
            </ol>
        </div>
    </div>
</div>
@endsection
