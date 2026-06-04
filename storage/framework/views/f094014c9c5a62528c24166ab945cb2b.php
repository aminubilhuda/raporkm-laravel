<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['sekolah', 'siswa', 'kelas', 'tahun', 'semester']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['sekolah', 'siswa', 'kelas', 'tahun', 'semester']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<table style="width:100%;border:none;border-collapse:collapse;margin-bottom:8px;">
    <tr>
        <td style="width:80px;vertical-align:top;padding:0;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sekolah && $sekolah->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($sekolah->logo)): ?>
                <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url($sekolah->logo)); ?>" style="width:70px;height:auto;" alt="Logo" />
            <?php else: ?>
                <div style="width:70px;height:70px;background:#e5e7eb;text-align:center;line-height:70px;font-size:10px;color:#6b7280;">LOGO</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </td>
        <td style="text-align:center;vertical-align:top;padding:0;">
            <div style="font-size:9pt;font-weight:400;text-transform:uppercase;letter-spacing:0.5px;"><?php echo e($sekolah?->nama_sekolah ?? 'PEMERINTAH PROVINSI'); ?></div>
            <div style="font-size:14pt;font-weight:700;text-transform:uppercase;margin-top:2px;"><?php echo e(strtoupper($sekolah?->nama_sekolah ?? 'NAMA SEKOLAH')); ?></div>
            <div style="font-size:9pt;font-weight:400;">NPSN: <?php echo e($sekolah?->npsn ?? '-'); ?></div>
            <div style="font-size:8pt;font-weight:400;"><?php echo e($sekolah?->alamat ?? 'Alamat sekolah'); ?></div>
        </td>
        <td style="width:80px;vertical-align:top;padding:0;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sekolah && $sekolah->logo_prov && \Illuminate\Support\Facades\Storage::disk('public')->exists($sekolah->logo_prov)): ?>
                <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url($sekolah->logo_prov)); ?>" style="width:70px;height:auto;" alt="Logo Prov" />
            <?php else: ?>
                <div style="width:70px;height:70px;"></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </td>
    </tr>
</table>
<hr style="border:none;border-top:2px solid #000;margin:0 0 4px 0;" />
<hr style="border:none;border-top:1px solid #000;margin:0 0 12px 0;" />

<div style="text-align:center;font-size:12pt;font-weight:700;text-transform:uppercase;margin-bottom:10px;">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($jenis) && $jenis === 'mid'): ?>
        Laporan Hasil Penilaian Tengah Semester
    <?php else: ?>
        Laporan Hasil Penilaian Akhir Semester
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<table style="width:100%;font-size:9pt;margin-bottom:14px;">
    <tr>
        <td style="width:18%;padding:1px 0;">Nama Siswa</td>
        <td style="width:2%;">:</td>
        <td style="width:30%;font-weight:700;"><?php echo e($siswa->nama_siswa ?? '-'); ?></td>
        <td style="width:18%;">Kelas</td>
        <td style="width:2%;">:</td>
        <td style="width:30%;"><?php echo e($kelas?->nama_kelas ?? '-'); ?></td>
    </tr>
    <tr>
        <td>NISN / NIS</td>
        <td>:</td>
        <td><?php echo e($siswa->nisn ?? '-'); ?> / <?php echo e($siswa->nis ?? '-'); ?></td>
        <td>Tahun Pelajaran</td>
        <td>:</td>
        <td><?php echo e($tahun?->tahun ?? '-'); ?> — <?php echo e($semester?->nama ?? '-'); ?></td>
    </tr>
    <tr>
        <td>Sekolah</td>
        <td>:</td>
        <td><?php echo e($sekolah?->nama_sekolah ?? '-'); ?></td>
        <td>Fase</td>
        <td>:</td>
        <td><?php echo e($kelas?->tingkat?->fase ?? '-'); ?></td>
    </tr>
</table>
<?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views\components\rapor\header.blade.php ENDPATH**/ ?>