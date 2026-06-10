<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Induk Siswa</title>
    <style>
        body { font-family: 'Poppins', sans-serif; font-size: 8pt; margin: 15px; color: #1a1a1a; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 3px 4px; vertical-align: top; }
        th { background: #1e8c86; color: white; font-size: 7pt; text-transform: uppercase; text-align: center; font-weight: 700; }
        td { font-size: 7.5pt; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header-table td { border: none; padding: 2px 0; font-size: 9pt; }
        .header-table img { width: 65px; height: auto; }
        .title { font-size: 13pt; font-weight: 700; text-align: center; text-transform: uppercase; margin: 8px 0 6px 0; }
        .info-table td { border: none; padding: 1px 0; font-size: 9pt; }
        .info-table td:first-child { width: 14%; }
        .info-table td:nth-child(2) { width: 2%; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

<table class="header-table" style="width:100%;border:none;">
    <tr>
        <td style="width:80px;vertical-align:top;">
            @if($sekolah && $sekolah->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($sekolah->logo))
                <img src="{{ \Illuminate\Support\Facades\Storage::url($sekolah->logo) }}" alt="Logo" />
            @else
                <div style="width:65px;height:65px;background:#e5e7eb;text-align:center;line-height:65px;font-size:8px;color:#6b7280;">LOGO</div>
            @endif
        </td>
        <td style="text-align:center;vertical-align:top;">
            <div style="font-size:9pt;text-transform:uppercase;letter-spacing:0.5px;">{{ strtoupper($sekolah?->alamat ?? '') }}</div>
            <div style="font-size:14pt;font-weight:700;text-transform:uppercase;margin-top:2px;">{{ strtoupper($sekolah?->nama_sekolah ?? 'NAMA SEKOLAH') }}</div>
            <div style="font-size:9pt;">NPSN: {{ $sekolah?->npsn ?? '-' }}</div>
        </td>
        <td style="width:80px;vertical-align:top;">
            @if($sekolah && $sekolah->logo_prov && \Illuminate\Support\Facades\Storage::disk('public')->exists($sekolah->logo_prov))
                <img src="{{ \Illuminate\Support\Facades\Storage::url($sekolah->logo_prov) }}" alt="Logo Prov" />
            @endif
        </td>
    </tr>
</table>
<hr style="border:none;border-top:2px solid #000;margin:4px 0 2px 0;">
<hr style="border:none;border-top:1px solid #000;margin:0 0 8px 0;">

<div class="title">Buku Induk Siswa</div>

<table class="info-table" style="margin-bottom:10px;">
    <tr>
        <td>Total Siswa</td>
        <td>:</td>
        <td style="font-weight:700;">{{ $siswaList->count() }} siswa</td>
        <td style="width:14%;">Status</td>
        <td style="width:2%;">:</td>
        <td style="font-weight:700;">Aktif</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width:22px;">No</th>
            <th style="width:55px;">NISN</th>
            <th style="width:42px;">NIS</th>
            <th style="width:90px;">Nama Siswa</th>
            <th style="width:18px;">JK</th>
            <th style="width:22px;">Tgl Lhr</th>
            <th style="width:32px;">Agama</th>
            <th style="width:10px;">Jrs</th>
            <th style="width:80px;">Alamat</th>
            <th style="width:65px;">Nama Ayah</th>
            <th style="width:65px;">Nama Ibu</th>
            <th style="width:38px;">Kelas</th>
        </tr>
    </thead>
    <tbody>
        @forelse($siswaList as $i => $s)
            @php
                $kelasAktif = $s->siswaKelas->firstWhere('status', 'aktif');
                $agamaMap = [1=>'Islam',2=>'Kristen',3=>'Katolik',4=>'Hindu',5=>'Buddha',6=>'Konghucu'];
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $s->nisn }}</td>
                <td>{{ $s->nis }}</td>
                <td style="font-weight:600;">{{ $s->nama_siswa }}</td>
                <td class="text-center">{{ $s->kelamin == 1 ? 'L' : ($s->kelamin == 2 ? 'P' : '-') }}</td>
                <td class="text-center">{{ $s->tanggal_lahir ? $s->tanggal_lahir->format('d/m/y') : '-' }}</td>
                <td>{{ $agamaMap[$s->agama] ?? '-' }}</td>
                <td class="text-center">{{ $s->kompetensiKeahlian?->singkatan ?? '-' }}</td>
                <td>{{ \Illuminate\Support\Str::limit($s->alamat ?? '-', 35) }}</td>
                <td>{{ \Illuminate\Support\Str::limit($s->nama_ayah ?? '-', 22) }}</td>
                <td>{{ \Illuminate\Support\Str::limit($s->nama_ibu ?? '-', 22) }}</td>
                <td class="text-center" style="font-weight:700;">{{ $kelasAktif?->kelas?->nama_kelas ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="12" class="text-center" style="padding:20px;">Tidak ada data siswa.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top:12px;font-size:7pt;color:#666;">
    Dicetak pada: {{ now()->format('d F Y H:i') }}
</div>

</body>
</html>
