<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor Mid - {{ $siswa->nama_siswa ?? 'Siswa' }}</title>
    <style>
        @font-face { font-family: poppins; font-weight: normal; font-style: normal; src: url('{{ storage_path('fonts/Poppins-Regular.ttf') }}') format('truetype'); }
        @font-face { font-family: poppins; font-weight: bold; font-style: normal; src: url('{{ storage_path('fonts/Poppins-Bold.ttf') }}') format('truetype'); }
        @page { margin: 18mm 16mm; }
        body { font-family: poppins, sans-serif; font-size: 9pt; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #9ca3af; padding: 4px 6px; font-size: 8.5pt; }
        table.data th { background: #f3f4f6; font-weight: 700; text-align: left; }
        .center { text-align: center; }
        .section-title { font-size: 10pt; font-weight: 700; margin: 12px 0 4px; text-transform: uppercase; color: #0f766e; border-bottom: 1px solid #0f766e; padding-bottom: 2px; }
        .signature-row { margin-top: 30px; }
        .signature-row td { text-align: center; font-size: 9pt; }
        .signature-row .name { font-weight: 700; text-decoration: underline; margin-top: 50px; }
    </style>
</head>
<body>

<x-rapor.header :sekolah="$sekolah" :siswa="$siswa" :kelas="$kelas" :tahun="$tahun" :semester="$semester" :jenis="'mid'" />

<div class="section-title">Nilai Sumatif Tengah Semester (TS)</div>
<table class="data">
    <thead>
        <tr>
            <th style="width:5%;" class="center">No</th>
            <th style="width:35%;">Mata Pelajaran</th>
            <th style="width:10%;" class="center">KKM</th>
            <th style="width:10%;" class="center">Nilai</th>
            <th>Deskripsi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($nilai_sumatif_ts as $n)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>{{ $n->mapel?->nama_mapel ?? '-' }}</td>
                <td class="center">{{ $n->mapel?->kkm ?? 75 }}</td>
                <td class="center"><strong>{{ $n->nilai }}</strong></td>
                <td>{{ $n->deskripsi ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="center" style="padding:14px;color:#6b7280;">Belum ada nilai tengah semester.</td></tr>
        @endforelse
    </tbody>
</table>

<table class="signature-row" style="width:100%;">
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
