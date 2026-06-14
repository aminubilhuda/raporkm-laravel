<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Identitas Siswa - <?php echo e($siswa->nama_siswa ?? 'Siswa'); ?></title>
    <style>
        @font-face { font-family: poppins; font-weight: normal; font-style: normal; src: url('<?php echo e(storage_path('fonts/Poppins-Regular.ttf')); ?>') format('truetype'); }
        @font-face { font-family: poppins; font-weight: bold; font-style: normal; src: url('<?php echo e(storage_path('fonts/Poppins-Bold.ttf')); ?>') format('truetype'); }
        @page { margin: 8mm; }
        body { font-family: poppins, sans-serif; font-size: 10pt; color: #000; line-height: 1.3; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .page-frame { border: 3px double #000; padding: 5mm; box-sizing: border-box; min-height: 95%; }

        .cover-page { text-align: center; padding-top: 2px; }
        .cover-page .logo-prov { width: 60px; height: auto; margin-bottom: 2px; }
        .cover-page .logo-sekolah { width: 75px; height: auto; margin: 4px auto; }
        .cover-page .title-rapot { font-size: 15pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin: 4px 0; }
        .cover-page .sub-title { font-size: 10pt; text-transform: uppercase; font-weight: 400; margin: 1px 0; }
        .cover-page .school-name { font-size: 13pt; font-weight: 700; text-transform: uppercase; margin: 3px 0; }
        .cover-page .keahlian { font-size: 10pt; font-weight: 700; margin: 4px 0; }
        .cover-page .border-box { border: 2px solid #000; padding: 4px 12px; margin: 4px auto; width: 75%; }
        .cover-page .border-box-small { border: 2px solid #000; padding: 3px 10px; margin: 4px auto; width: 55%; }
        .cover-page .footer-ministry { font-size: 6.5pt; text-transform: uppercase; margin-top: 6px; letter-spacing: 0.2px; }

        .identity-page { }
        .identity-page .title { font-size: 13pt; font-weight: 700; text-transform: uppercase; text-align: center; margin-bottom: 10px; letter-spacing: 0.5px; }
        .identity-table { width: 100%; border-collapse: collapse; font-size: 9pt; }
        .identity-table td { padding: 2px 4px; vertical-align: middle; }
        .identity-table .no { width: 28px; text-align: center; font-weight: 700; }
        .identity-table .sep { width: 16px; text-align: center; }
        .identity-table .label { padding-left: 4px; }
        .identity-table .value { font-weight: 600; padding-left: 4px; }
        .identity-table .sub-label { padding-left: 16px; }

        .signature { text-align: right; font-size: 9pt; margin-top: 20px; }
        .signature .name { font-weight: 700; text-decoration: underline; margin-top: 50px; display: block; }
    </style>
</head>
<body>


<div class="page-frame cover-page">
    <img src="<?php echo e($logoProv); ?>" class="logo-prov" alt="Logo Provinsi" />

    <div class="title-rapot">RAPOR</div>
    <div class="sub-title">SEKOLAH MENENGAH KEJURUAN (SMK)</div>

    <img src="<?php echo e($logoSekolah); ?>" class="logo-sekolah" alt="Logo Sekolah" />

    <div class="school-name"><?php echo e($sekolah->nama_sekolah ?? 'SMK'); ?></div>
    <div class="keahlian">KOMPETENSI KEAHLIAN<br><?php echo e($kompetensiKeahlian); ?></div>

    <div class="border-box">
        <div style="font-size:8pt;text-transform:uppercase;letter-spacing:0.3px;">Nama Peserta Didik</div>
        <div style="font-size:13pt;font-weight:700;margin-top:2px;"><?php echo e($siswa->nama_siswa); ?></div>
    </div>

    <div class="border-box-small">
        <div style="font-size:8pt;">NISN / NIS</div>
        <div style="font-size:11pt;font-weight:700;margin-top:2px;"><?php echo e($siswa->nisn ?? '-'); ?> / <?php echo e($siswa->nis ?? '-'); ?></div>
    </div>

    <div class="footer-ministry">
        KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI<br>
        REPUBLIK INDONESIA
    </div></div><div class="page-frame identity-page" style="page-break-before: always;">
    <?php
        $agamaMap = [1=>'Islam',2=>'Kristen',3=>'Katolik',4=>'Hindu',5=>'Buddha',6=>'Konghucu'];
        $kelaminMap = [1=>'Laki-laki', 2=>'Perempuan'];
    ?>

    <div class="title">IDENTITAS PESERTA DIDIK</div>

    <table class="identity-table">
        <tr>
            <td class="no">1.</td>
            <td class="label">Nama Lengkap Peserta Didik</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->nama_siswa); ?></td>
        </tr>
        <tr>
            <td class="no">2.</td>
            <td class="label">NIS / NISN</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->nis ?? '-'); ?> / <?php echo e($siswa->nisn ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">3.</td>
            <td class="label">Tempat, Tanggal Lahir</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->tempat_lahir ?? '-'); ?>, <?php echo e($siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d F Y') : '-'); ?></td>
        </tr>
        <tr>
            <td class="no">4.</td>
            <td class="label">Jenis Kelamin</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($kelaminMap[$siswa->kelamin] ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">5.</td>
            <td class="label">Agama</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($agamaMap[$siswa->agama] ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">6.</td>
            <td class="label">Status Dalam Keluarga</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->hub_keluarga ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">7.</td>
            <td class="label">Anak Ke</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->anak_ke ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">8.</td>
            <td class="label">Alamat Peserta Didik</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->alamat ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">9.</td>
            <td class="label">No. Telepon</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->kontak_siswa ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">10.</td>
            <td class="label">Sekolah Asal</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->sekolah_asal ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">11.</td>
            <td class="label">Diterima di Sekolah Ini</td>
            <td class="sep">:</td>
            <td class="value"></td>
        </tr>
        <tr>
            <td class="no"></td>
            <td class="sub-label">a. Di Kelas</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->terima_kelas ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no"></td>
            <td class="sub-label">b. Pada Tanggal</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->terima_tanggal ? $siswa->terima_tanggal->format('d F Y') : '-'); ?></td>
        </tr>
        <tr>
            <td class="no">12.</td>
            <td class="label">Nama Orang Tua</td>
            <td class="sep">:</td>
            <td class="value"></td>
        </tr>
        <tr>
            <td class="no"></td>
            <td class="sub-label">a. Ayah</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->nama_ayah ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no"></td>
            <td class="sub-label">b. Ibu</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->nama_ibu ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">13.</td>
            <td class="label">Alamat Orang Tua</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->alamat_orang_tua ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">14.</td>
            <td class="label">No. Telepon Rumah</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->kontak_ayah ?? ($siswa->kontak_ibu ?? '-')); ?></td>
        </tr>
        <tr>
            <td class="no">15.</td>
            <td class="label">Pekerjaan Orang Tua</td>
            <td class="sep">:</td>
            <td class="value"></td>
        </tr>
        <tr>
            <td class="no"></td>
            <td class="sub-label">a. Ayah</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->pekerjaan_ayah ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no"></td>
            <td class="sub-label">b. Ibu</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->pekerjaan_ibu ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">16.</td>
            <td class="label">Nama Wali</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->nama_wali ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">17.</td>
            <td class="label">Pekerjaan Wali</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->pekerjaan_wali ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">18.</td>
            <td class="label">Alamat Wali</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->alamat_wali ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="no">19.</td>
            <td class="label">Kontak Wali</td>
            <td class="sep">:</td>
            <td class="value"><?php echo e($siswa->kontak_wali ?? '-'); ?></td>
        </tr>
    </table>

    <div class="signature">
        <div><?php echo e($sekolah->kabupaten ?? 'Tuban'); ?>, <?php echo e($tanggalCetak); ?></div>
        <div style="margin-top:4px;">Kepala Sekolah,</div>
        <div style="height:55px;"></div>
        <div class="name"><?php echo e($kepalaSekolah?->nama ?? $sekolah->nama_sekolah ?? '-'); ?></div>
        <div>NIP. <?php echo e($kepalaSekolah?->nip ?? '-'); ?></div>
    </div>
</div>

</body>
</html><?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views/tu/rapor/identitas-pdf.blade.php ENDPATH**/ ?>