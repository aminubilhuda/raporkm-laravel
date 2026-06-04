<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor PKL</title>
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
        .identitas td:first-child { width: 140px; }
        .ttd { margin-top: 40px; }
        .ttd td { border: none; text-align: center; padding: 20px 10px 0; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sekolah): ?>
        <h1><?php echo e($sekolah->nama_sekolah); ?></h1>
        <p>NPSN: <?php echo e($sekolah->npsn); ?> | Alamat: <?php echo e($sekolah->alamat); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <hr style="margin: 8px 0;">
        <h1>LAPORAN PRAKTIK KERJA LAPANGAN (PKL)</h1>
    </div>

    <table class="identitas">
        <tr><td>Nama Siswa</td><td>: <strong><?php echo e($siswa->nama_siswa); ?></strong></td></tr>
        <tr><td>NISN</td><td>: <?php echo e($siswa->nisn); ?></td></tr>
        <tr><td>Kelas</td><td>: <?php echo e($siswaPrakerin?->siswaKelas?->kelas?->nama_kelas ?? '-'); ?></td></tr>
    </table>

    <h3 style="margin: 10px 0 5px;">Data Prakerin</h3>
    <table class="identitas">
        <tr><td>Nama Mitra</td><td>: <?php echo e($prakerin->nama_mitra ?? '-'); ?></td></tr>
        <tr><td>Lokasi</td><td>: <?php echo e($prakerin->lokasi ?? '-'); ?></td></tr>
        <tr><td>Guru Pembimbing</td><td>: <?php echo e($prakerin->user?->nama ?? '-'); ?></td></tr>
    </table>

    <h3 style="margin: 10px 0 5px;">Nilai PKL</h3>
    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Tujuan Pembelajaran</th>
                <th style="width:60px">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $siswaPrakerin?->nilaiPrakerin ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $np): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="text-center"><?php echo e($i + 1); ?></td>
                <td><?php echo e($np->tujuanPembelajaran?->nama_tp ?? 'TP ' . ($i + 1)); ?></td>
                <td class="text-center"><?php echo e($np->nilai); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="3" class="text-center">Belum ada nilai.</td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Nilai Akhir PKL</th>
                <th class="text-center"><?php echo e($nilaiPkl); ?></th>
            </tr>
        </tfoot>
    </table>

    <?php $tgl = \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'); ?>
    <table class="ttd">
        <tr>
            <td style="width:33%">Guru Pembimbing</td>
            <td style="width:33%"><?php echo e($sekolah?->desa); ?>, <?php echo e($tgl); ?><br>Kepala Sekolah</td>
            <td style="width:33%">Orang Tua / Wali</td>
        </tr>
        <tr>
            <td style="padding-top:60px;"><u><?php echo e($prakerin->user?->nama ?? '______________'); ?></u></td>
            <td style="padding-top:60px;"><u><?php echo e($sekolah?->kepalaSekolah?->nama ?? '______________'); ?></u></td>
            <td style="padding-top:60px;"><u><?php echo e($siswa->nama_ayah ?? '______________'); ?></u></td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views\rapor\rapor-pkl.blade.php ENDPATH**/ ?>