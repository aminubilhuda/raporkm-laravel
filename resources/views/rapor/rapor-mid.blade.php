<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor Mid Semester</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: serif; font-size: 11pt; color: #000; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 6px; border: 1px solid #000; text-align: left; font-size: 10pt; }
        th { background: #f0f0f0; text-align: center; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 14pt; margin: 2px 0; }
        .header h2 { font-size: 12pt; margin: 2px 0; font-weight: normal; }
        .identitas td { border: none; padding: 2px 6px; }
        .identitas td:first-child { width: 120px; }
        .ttd { margin-top: 30px; }
        .ttd td { border: none; text-align: center; padding: 20px 10px 0; }
        .text-center { text-align: center; }
        .nilai { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        @if($sekolah)
        <h1>{{ $sekolah->nama_sekolah }}</h1>
        <p>NPSN: {{ $sekolah->npsn }} | Alamat: {{ $sekolah->alamat }}</p>
        @endif
        <hr style="margin: 8px 0;">
        <h1>LAPORAN HASIL BELAJAR MID SEMESTER</h1>
        <h2>SEMESTER {{ $semester == 1 ? 'GANJIL' : 'GENAP' }} TAHUN PELAJARAN {{ $tahun ?? $sekolah?->tahun_aktif }}</h2>
    </div>

    <table class="identitas">
        <tr><td>Nama Siswa</td><td>: <strong>{{ $siswa->nama_siswa }}</strong></td></tr>
        <tr><td>NISN / NIS</td><td>: {{ $siswa->nisn }} / {{ $siswa->nis }}</td></tr>
        <tr><td>Kelas</td><td>: {{ $kelas?->nama_kelas ?? '-' }}</td></tr>
    </table>

    <h3 style="margin: 10px 0 5px;">Nilai Tengah Semester</h3>
    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Mata Pelajaran</th>
                <th style="width:50px">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilaiMapel as $i => $nm)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $nm->mapel->nama_mapel ?? '-' }}</td>
                <td class="text-center"><strong>{{ $nm->nilai }}</strong></td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center">Belum ada nilai.</td></tr>
            @endforelse
        </tbody>
    </table>

    @php $tgl = \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'); @endphp
    <table class="ttd">
        <tr>
            <td style="width:50%">Mengetahui,<br>{{ $sekolah?->desa }}, {{ $tgl }}<br>Wali Kelas</td>
            <td style="width:50%">Orang Tua / Wali</td>
        </tr>
        <tr>
            <td style="padding-top:60px;"><u>______________</u></td>
            <td style="padding-top:60px;"><u>{{ $siswa->nama_ayah ?? '______________' }}</u></td>
        </tr>
    </table>
</body>
</html>
