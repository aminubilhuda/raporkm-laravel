<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lager Nilai - {{ $kelas->nama_kelas }} - {{ $mapel->nama_mapel }}</title>
    <style>
        @font-face { font-family: poppins; font-weight: normal; font-style: normal; src: url('{{ storage_path('fonts/Poppins-Regular.ttf') }}') format('truetype'); }
        @font-face { font-family: poppins; font-weight: bold; font-style: normal; src: url('{{ storage_path('fonts/Poppins-Bold.ttf') }}') format('truetype'); }
        @page { margin: 16mm 14mm; }
        body { font-family: poppins, sans-serif; font-size: 8.5pt; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #9ca3af; padding: 3px 5px; font-size: 8pt; }
        table.data th { background: #f3f4f6; font-weight: 700; text-align: left; }
        .center { text-align: center; }
        .kop { margin-bottom: 12px; }
        .kop h1 { font-size: 12pt; margin: 0 0 4px 0; text-align: center; }
        .kop .meta { font-size: 8pt; color: #6b7280; text-align: center; }
    </style>
</head>
<body>

<div class="kop">
    <h1>LAGER NILAI AKHIR</h1>
    <div class="meta">
        {{ $sekolah?->nama_sekolah ?? '-' }} —
        Kelas {{ $kelas->nama_kelas }} —
        Mata Pelajaran {{ $mapel->nama_mapel }} —
        {{ $tahun?->tahun ?? '-' }} ({{ $semester?->nama ?? '-' }})
    </div>
</div>

<table class="data">
    <thead>
        <tr>
            <th style="width:4%;" class="center">No</th>
            <th style="width:12%;">NISN</th>
            <th style="width:10%;">NIS</th>
            <th>Nama Siswa</th>
            <th style="width:8%;" class="center">Formatif<br><small>(40%)</small></th>
            <th style="width:8%;" class="center">Sumatif PH<br><small>(30%)</small></th>
            <th style="width:8%;" class="center">Sumatif AS<br><small>(30%)</small></th>
            <th style="width:8%;" class="center">Nilai<br>Akhir</th>
            <th style="width:8%;" class="center">Predikat</th>
        </tr>
    </thead>
    <tbody>
        @forelse($siswa as $s)
            @php
                $nilai = $grid[$s->id][$mapel->id] ?? null;
            @endphp
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>{{ $s->nisn }}</td>
                <td>{{ $s->nis }}</td>
                <td>{{ $s->nama_siswa }}</td>
                <td class="center">{{ $nilai?->rata_formatif ?? '-' }}</td>
                <td class="center">{{ $nilai?->rata_sumatif_ph ?? '-' }}</td>
                <td class="center">{{ $nilai?->sumatif_as ?? '-' }}</td>
                <td class="center"><strong>{{ $nilai?->nilai_akhir ?? '-' }}</strong></td>
                <td class="center">{{ $nilai?->predikat ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="9" class="center" style="padding:14px;color:#6b7280;">Belum ada data nilai untuk kelas ini.</td></tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top:30px;font-size:8pt;">
    <div style="float:right;text-align:center;width:200px;">
        {{ $sekolah?->nama_sekolah ?? 'Sekolah' }}, {{ now()->translatedFormat('d F Y') }}<br>
        Guru Mata Pelajaran<br><br><br>
        <strong>{{ auth()->user()?->nama ?? 'Guru' }}</strong>
    </div>
    <div style="clear:both;"></div>
</div>

</body>
</html>
