<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor Semester - <?php echo e($siswa->nama_siswa ?? 'Siswa'); ?></title>
    <style>
        @font-face { font-family: poppins; font-weight: normal; font-style: normal; src: url('<?php echo e(storage_path('fonts/Poppins-Regular.ttf')); ?>') format('truetype'); }
        @font-face { font-family: poppins; font-weight: bold; font-style: normal; src: url('<?php echo e(storage_path('fonts/Poppins-Bold.ttf')); ?>') format('truetype'); }
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

<?php if (isset($component)) { $__componentOriginale1d67046a2adf3b9baee55f5105a0e77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale1d67046a2adf3b9baee55f5105a0e77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rapor.header','data' => ['sekolah' => $sekolah,'siswa' => $siswa,'kelas' => $kelas,'tahun' => $tahun,'semester' => $semester,'jenis' => 'semester']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rapor.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['sekolah' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sekolah),'siswa' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($siswa),'kelas' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kelas),'tahun' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tahun),'semester' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($semester),'jenis' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('semester')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale1d67046a2adf3b9baee55f5105a0e77)): ?>
<?php $attributes = $__attributesOriginale1d67046a2adf3b9baee55f5105a0e77; ?>
<?php unset($__attributesOriginale1d67046a2adf3b9baee55f5105a0e77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale1d67046a2adf3b9baee55f5105a0e77)): ?>
<?php $component = $__componentOriginale1d67046a2adf3b9baee55f5105a0e77; ?>
<?php unset($__componentOriginale1d67046a2adf3b9baee55f5105a0e77); ?>
<?php endif; ?>

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
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $nilai_mapel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mapelId => $nilaiGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $first = $nilaiGroup->first();
                $mapel = $first->mapel;
            ?>
            <tr>
                <td class="center"><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($mapel?->nama_mapel ?? '-'); ?></td>
                <td class="center"><?php echo e($mapel?->kkm ?? 75); ?></td>
                <td class="center"><strong><?php echo e($first->nilai); ?></strong></td>
                <td class="center"><?php echo e($first->predikat ?? '-'); ?></td>
                <td><?php echo e($first->deskripsi ?? '-'); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="center" style="padding:14px;color:#6b7280;">Belum ada nilai untuk semester ini.</td></tr>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </tbody>
</table>

<div class="section-title">B. Catatan Wali Kelas</div>
<table class="data">
    <tbody>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $catatan_wali; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td style="padding:6px;"><?php echo e($c->catatan); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td class="center" style="padding:10px;color:#6b7280;">Tidak ada catatan.</td></tr>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $ekskul; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="center"><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($e->eskul?->nama_eskul ?? '-'); ?></td>
                <td class="center"><?php echo e($e->predikat ?? '-'); ?></td>
                <td><?php echo e($e->keterangan ?? '-'); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="center" style="padding:10px;color:#6b7280;">Tidak mengikuti eskul.</td></tr>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </tbody>
</table>

<div class="section-title">D. Ketidakhadiran</div>
<table style="width:50%;font-size:9pt;margin-bottom:14px;">
    <tr><td style="padding:2px 0;">Sakit</td><td style="width:10px;">:</td><td><strong><?php echo e($presensi['sakit'] ?? 0); ?></strong> hari</td></tr>
    <tr><td>Izin</td><td>:</td><td><strong><?php echo e($presensi['izin'] ?? 0); ?></strong> hari</td></tr>
    <tr><td>Tanpa Keterangan (Alpha)</td><td>:</td><td><strong><?php echo e($presensi['alpha'] ?? 0); ?></strong> hari</td></tr>
</table>

<table class="signature-row" style="width:100%;margin-top:30px;">
    <tr>
        <td style="width:50%;">Mengetahui,<br>Orang Tua/Wali</td>
        <td style="width:50%;"><?php echo e($sekolah?->nama_sekolah ?? 'Sekolah'); ?>, <?php echo e(now()->translatedFormat('d F Y')); ?><br>Wali Kelas</td>
    </tr>
    <tr>
        <td class="name">_________________</td>
        <td class="name"><?php echo e(auth()->user()?->nama ?? 'Wali Kelas'); ?></td>
    </tr>
</table>

</body>
</html>
<?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views\tu\rapor\semester-pdf.blade.php ENDPATH**/ ?>