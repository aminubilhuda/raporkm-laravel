<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor PKL - {{ $siswa->nama_siswa ?? 'Siswa' }}</title>
    <style>
        @font-face { font-family: poppins; font-weight: normal; font-style: normal; src: url('{{ storage_path('fonts/Poppins-Regular.ttf') }}') format('truetype'); }
        @font-face { font-family: poppins; font-weight: bold; font-style: normal; src: url('{{ storage_path('fonts/Poppins-Bold.ttf') }}') format('truetype'); }
        @page { margin: 18mm 16mm; }
        body { font-family: poppins, sans-serif; font-size: 9pt; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #9ca3af; padding: 5px 6px; font-size: 9pt; }
        table.data th { background: #f3f4f6; font-weight: 700; text-align: left; }
        .center { text-align: center; }
        .section-title { font-size: 10pt; font-weight: 700; margin: 12px 0 4px; text-transform: uppercase; color: #0f766e; border-bottom: 1px solid #0f766e; padding-bottom: 2px; }
        .signature-row { margin-top: 30px; }
        .signature-row td { text-align: center; font-size: 9pt; }
        .signature-row .name { font-weight: 700; text-decoration: underline; margin-top: 50px; }
    </style>
</head>
<body>

<table style="width:100%;font-size:9pt;margin-bottom:14px;border:none;">
    <tr>
        <td style="width:50%;vertical-align:top;border:none;">
            <div style="font-weight:700;font-size:11pt;">LAPORAN PRAKTIK KERJA LAPANGAN (PKL)</div>
            <div style="margin-top:4px;font-size:9pt;">{{ $sekolah?->nama_sekolah ?? '-' }}</div>
        </td>
        <td style="width:50%;vertical-align:top;text-align:right;border:none;font-size:9pt;">
            <div>Tahun Pelajaran: {{ $siswa_prakerin?->tahunPelajaran?->tahun ?? '-' }}</div>
            <div>Semester: {{ $siswa_prakerin?->semester?->nama ?? '-' }}</div>
        </td>
    </tr>
</table>

<table style="width:100%;font-size:9pt;margin-bottom:14px;">
    <tr>
        <td style="width:18%;padding:1px 0;">Nama Siswa</td>
        <td style="width:2%;">:</td>
        <td style="width:30%;font-weight:700;">{{ $siswa->nama_siswa ?? '-' }}</td>
        <td style="width:18%;">Kelas</td>
        <td style="width:2%;">:</td>
        <td style="width:30%;">{{ $kelas?->nama_kelas ?? '-' }}</td>
    </tr>
    <tr>
        <td>NISN / NIS</td>
        <td>:</td>
        <td>{{ $siswa->nisn ?? '-' }} / {{ $siswa->nis ?? '-' }}</td>
        <td>Perusahaan</td>
        <td>:</td>
        <td>{{ $prakerin?->nama_perusahaan ?? '-' }}</td>
    </tr>
    <tr>
        <td>Pembimbing</td>
        <td>:</td>
        <td>{{ $siswa_prakerin?->user?->nama ?? '-' }}</td>
        <td>Periode</td>
        <td>:</td>
        <td>
            @if($prakerin)
                {{ $prakerin->tanggal_mulai?->format('d/m/Y') }} – {{ $prakerin->tanggal_selesai?->format('d/m/Y') }}
            @else
                -
            @endif
        </td>
    </tr>
</table>

<div class="section-title">Nilai PKL (4 Tujuan Pembelajaran, bobot masing-masing 25%)</div>
<table class="data">
    <thead>
        <tr>
            <th style="width:5%;" class="center">No</th>
            <th style="width:50%;">Tujuan Pembelajaran / Mata Pelajaran</th>
            <th style="width:10%;" class="center">Nilai</th>
            <th>Deskripsi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($nilai_prakerin as $n)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>{{ $n->mapel?->nama_mapel ?? 'Tujuan Pembelajaran' }}</td>
                <td class="center"><strong>{{ $n->nilai }}</strong></td>
                <td>{{ $n->deskripsi ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="center" style="padding:14px;color:#6b7280;">Belum ada nilai PKL.</td></tr>
        @endforelse

        @php
            $rata = $nilai_prakerin->count() > 0 ? round($nilai_prakerin->avg('nilai'), 0) : null;
        @endphp
        @if($rata !== null)
            <tr style="background:#f9fafb;font-weight:700;">
                <td colspan="2" class="center">RATA-RATA NILAI PKL</td>
                <td class="center">{{ $rata }}</td>
                <td></td>
            </tr>
        @endif
    </tbody>
</table>

<table class="signature-row" style="width:100%;">
    <tr>
        <td style="width:50%;">Mengetahui,<br>Orang Tua/Wali</td>
        <td style="width:50%;">{{ $sekolah?->nama_sekolah ?? 'Sekolah' }}, {{ now()->translatedFormat('d F Y') }}<br>Pembimbing</td>
    </tr>
    <tr>
        <td class="name">_________________</td>
        <td class="name">{{ $siswa_prakerin?->user?->nama ?? 'Pembimbing' }}</td>
    </tr>
</table>

</body>
</html>
