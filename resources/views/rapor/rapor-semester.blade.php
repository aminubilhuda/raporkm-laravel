<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor Semester</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: serif; font-size: 11pt; color: #000; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 6px; border: 1px solid #000; text-align: left; font-size: 10pt; }
        th { background: #f0f0f0; text-align: center; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 14pt; margin: 2px 0; }
        .header h2 { font-size: 12pt; margin: 2px 0; font-weight: normal; }
        .header p { font-size: 10pt; margin: 1px 0; }
        .identitas { margin-bottom: 15px; }
        .identitas td { border: none; padding: 2px 6px; }
        .identitas td:first-child { width: 120px; }
        .ttd { margin-top: 30px; }
        .ttd td { border: none; text-align: center; padding: 20px 10px 0; }
        .catatan { margin-top: 10px; padding: 8px; border: 1px solid #000; }
        .page-break { page-break-before: always; }
        .text-center { text-align: center; }
        .nilai { text-align: center; }
        .deskripsi { font-size: 9pt; line-height: 1.3; }
    </style>
</head>
<body>
    <div class="header">
        @if($sekolah)
        <h1>{{ $sekolah->nama_sekolah }}</h1>
        <p>NPSN: {{ $sekolah->npsn }} | Alamat: {{ $sekolah->alamat }}</p>
        <p>{{ $sekolah->desa }}, {{ $sekolah->kecamatan }}, {{ $sekolah->kabupaten }}, {{ $sekolah->provinsi }}</p>
        @endif
        <hr style="margin: 8px 0;">
        <h1>LAPORAN HASIL BELAJAR</h1>
        <h2>SEMESTER {{ $semester == 1 ? 'GANJIL' : 'GENAP' }} TAHUN PELAJARAN {{ $tahun ?? $sekolah?->tahun_aktif }}</h2>
    </div>

    <table class="identitas">
        <tr><td>Nama Siswa</td><td>: <strong>{{ $siswa->nama_siswa }}</strong></td></tr>
        <tr><td>NISN / NIS</td><td>: {{ $siswa->nisn }} / {{ $siswa->nis }}</td></tr>
        <tr><td>Kelas</td><td>: {{ $kelas?->nama_kelas ?? '-' }}</td></tr>
        <tr><td>Program Keahlian</td><td>: {{ $kelas?->kompetensiKeahlian?->nama_kompetensi ?? '-' }}</td></tr>
    </table>

    <h3 style="margin: 10px 0 5px;">A. Nilai Akademik</h3>
    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Mata Pelajaran</th>
                <th style="width:40px">KKM</th>
                <th style="width:50px">Nilai</th>
                <th style="width:45px">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilaiMapel as $i => $nm)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $nm->mapel->nama_mapel ?? '-' }}</td>
                <td class="text-center">{{ $nm->kkm ?? 75 }}</td>
                <td class="text-center"><strong>{{ $nm->nilai }}</strong></td>
                <td class="text-center">{{ $nm->predikat }}</td>
            </tr>
            <tr>
                <td colspan="5" class="deskripsi">{{ $nm->deskripsi }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Belum ada nilai.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($eskul->isNotEmpty())
    <h3 style="margin: 10px 0 5px;">B. Ekstrakurikuler</h3>
    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Kegiatan</th>
                <th style="width:60px">Predikat</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($eskul as $i => $e)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $e->eskul->nama ?? '-' }}</td>
                <td class="text-center">{{ $e->predikat ?? '-' }}</td>
                <td>{{ $e->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h3 style="margin: 10px 0 5px;">C. Presensi</h3>
    <table>
        <tr>
            <th>Sakit</th>
            <th>Izin</th>
            <th>Alpa</th>
        </tr>
        <tr>
            <td class="text-center"><strong>{{ $presensi['sakit'] }}</strong></td>
            <td class="text-center"><strong>{{ $presensi['izin'] }}</strong></td>
            <td class="text-center"><strong>{{ $presensi['alpa'] }}</strong></td>
        </tr>
    </table>

    <h3 style="margin: 10px 0 5px;">D. Catatan Wali Kelas</h3>
    <div class="catatan">{{ $catatan ?: '-' }}</div>

    @php
        $tgl = \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY');
    @endphp

    <table class="ttd">
        <tr>
            <td style="width:33%">Mengetahui,<br>Kepala Sekolah</td>
            <td style="width:33%">{{ $sekolah?->desa }}, {{ $tgl }}<br>Wali Kelas</td>
            <td style="width:33%">Orang Tua / Wali</td>
        </tr>
        <tr>
            <td style="padding-top:60px;"><u>{{ $sekolah?->kepalaSekolah?->nama ?? '______________' }}</u></td>
            <td style="padding-top:60px;"><u>______________</u></td>
            <td style="padding-top:60px;"><u>{{ $siswa->nama_ayah ?? '______________' }}</u></td>
        </tr>
    </table>
</body>
</html>
