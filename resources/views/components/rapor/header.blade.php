@props(['sekolah', 'siswa', 'kelas', 'tahun', 'semester'])
<table style="width:100%;border:none;border-collapse:collapse;margin-bottom:8px;">
    <tr>
        <td style="width:80px;vertical-align:top;padding:0;">
            @if($sekolah && $sekolah->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($sekolah->logo))
                <img src="{{ \Illuminate\Support\Facades\Storage::url($sekolah->logo) }}" style="width:70px;height:auto;" alt="Logo" />
            @else
                <div style="width:70px;height:70px;background:#e5e7eb;text-align:center;line-height:70px;font-size:10px;color:#6b7280;">LOGO</div>
            @endif
        </td>
        <td style="text-align:center;vertical-align:top;padding:0;">
            <div style="font-size:9pt;font-weight:400;text-transform:uppercase;letter-spacing:0.5px;">{{ $sekolah?->nama_sekolah ?? 'PEMERINTAH PROVINSI' }}</div>
            <div style="font-size:14pt;font-weight:700;text-transform:uppercase;margin-top:2px;">{{ strtoupper($sekolah?->nama_sekolah ?? 'NAMA SEKOLAH') }}</div>
            <div style="font-size:9pt;font-weight:400;">NPSN: {{ $sekolah?->npsn ?? '-' }}</div>
            <div style="font-size:8pt;font-weight:400;">{{ $sekolah?->alamat ?? 'Alamat sekolah' }}</div>
        </td>
        <td style="width:80px;vertical-align:top;padding:0;">
            @if($sekolah && $sekolah->logo_prov && \Illuminate\Support\Facades\Storage::disk('public')->exists($sekolah->logo_prov))
                <img src="{{ \Illuminate\Support\Facades\Storage::url($sekolah->logo_prov) }}" style="width:70px;height:auto;" alt="Logo Prov" />
            @else
                <div style="width:70px;height:70px;"></div>
            @endif
        </td>
    </tr>
</table>
<hr style="border:none;border-top:2px solid #000;margin:0 0 4px 0;" />
<hr style="border:none;border-top:1px solid #000;margin:0 0 12px 0;" />

<div style="text-align:center;font-size:12pt;font-weight:700;text-transform:uppercase;margin-bottom:10px;">
    @if(isset($jenis) && $jenis === 'mid')
        Laporan Hasil Penilaian Tengah Semester
    @else
        Laporan Hasil Penilaian Akhir Semester
    @endif
</div>

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
        <td>Tahun Pelajaran</td>
        <td>:</td>
        <td>{{ $tahun?->tahun ?? '-' }} — {{ $semester?->nama ?? '-' }}</td>
    </tr>
    <tr>
        <td>Sekolah</td>
        <td>:</td>
        <td>{{ $sekolah?->nama_sekolah ?? '-' }}</td>
        <td>Fase</td>
        <td>:</td>
        <td>{{ $kelas?->tingkat?->fase ?? '-' }}</td>
    </tr>
</table>
