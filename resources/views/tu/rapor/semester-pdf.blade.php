<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor Semester - {{ $siswa->nama_siswa ?? 'Siswa' }}</title>
    <style>
        @font-face { font-family: poppins; font-weight: normal; font-style: normal; src: url('{{ storage_path('fonts/Poppins-Regular.ttf') }}') format('truetype'); }
        @font-face { font-family: poppins; font-weight: bold; font-style: normal; src: url('{{ storage_path('fonts/Poppins-Bold.ttf') }}') format('truetype'); }
        @page { margin: 18mm 16mm; }
        body { font-family: poppins, sans-serif; font-size: 9pt; color: #1f2937; }
        h2, h3 { font-family: poppins, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #9ca3af; padding: 4px 6px; font-size: 8.5pt; }
        table.data th { background: #f3f4f6; font-weight: 700; text-align: left; }
        .center { text-align: center; }
        .right { text-align: right; }
        .section-title { font-size: 10pt; font-weight: 700; margin: 12px 0 4px; text-transform: uppercase; color: #0f766e; border-bottom: 1px solid #0f766e; padding-bottom: 2px; }
        .signature-row { margin-top: 22px; }
        .signature-row td { text-align: center; font-size: 9pt; }
        .signature-row .name { font-weight: 700; text-decoration: underline; margin-top: 50px; }
    </style>
</head>
<body>

<x-rapor.header :sekolah="$sekolah" :siswa="$siswa" :kelas="$kelas" :tahun="$tahun" :semester="$semester" :jenis="'semester'" />

<div class="section-title">A. Nilai Akademik</div>
<table class="data">
    <thead>
        <tr>
            <th style="width:5%;" class="center">No</th>
            <th style="width:30%;">Mata Pelajaran</th>
            <th style="width:10%;" class="center">KKM</th>
            <th style="width:10%;" class="center">Nilai</th>
            <th style="width:10%;" class="center">Predikat</th>
            <th>Deskripsi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($nilai_mapel as $mapelId => $nilaiGroup)
            @php
                $first = $nilaiGroup->first();
                $mapel = $first->mapel;
            @endphp
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>{{ $mapel?->nama_mapel ?? '-' }}</td>
                <td class="center">{{ $mapel?->kkm ?? 75 }}</td>
                <td class="center"><strong>{{ $first->nilai }}</strong></td>
                <td class="center">{{ $first->predikat ?? '-' }}</td>
                <td>{{ $first->deskripsi ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="center" style="padding:14px;color:#6b7280;">Belum ada nilai untuk semester ini.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-title">B. Catatan Wali Kelas</div>
<table class="data">
    <tbody>
        @forelse($catatan_wali as $c)
            <tr>
                <td style="padding:6px;">{{ $c->catatan }}</td>
            </tr>
        @empty
            <tr><td class="center" style="padding:10px;color:#6b7280;">Tidak ada catatan.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-title">C. Ekstrakurikuler</div>
<table class="data">
    <thead>
        <tr>
            <th style="width:5%;" class="center">No</th>
            <th>Kegiatan Ekstrakurikuler</th>
            <th style="width:15%;" class="center">Predikat</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ekskul as $e)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>{{ $e->eskul?->nama_eskul ?? '-' }}</td>
                <td class="center">{{ $e->predikat ?? '-' }}</td>
                <td>{{ $e->keterangan ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="center" style="padding:10px;color:#6b7280;">Tidak mengikuti eskul.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-title">D. Ketidakhadiran</div>
<table style="width:50%;font-size:9pt;margin-bottom:14px;">
    <tr><td style="padding:2px 0;">Sakit</td><td style="width:10px;">:</td><td><strong>{{ $presensi['sakit'] ?? 0 }}</strong> hari</td></tr>
    <tr><td>Izin</td><td>:</td><td><strong>{{ $presensi['izin'] ?? 0 }}</strong> hari</td></tr>
    <tr><td>Tanpa Keterangan (Alpha)</td><td>:</td><td><strong>{{ $presensi['alpha'] ?? 0 }}</strong> hari</td></tr>
</table>

<table class="signature-row" style="width:100%;margin-top:30px;">
    <tr>
        <td style="width:50%;">Mengetahui,<br>Orang Tua/Wali</td>
        <td style="width:50%;">{{ $sekolah?->nama_sekolah ?? 'Sekolah' }}, {{ now()->translatedFormat('d F Y') }}<br>Wali Kelas</td>
    </tr>
    <tr>
        <td class="name">_________________</td>
        <td class="name">{{ auth()->user()?->nama ?? 'Wali Kelas' }}</td>
    </tr>
</table>

</body>
</html>
