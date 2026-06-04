<?php $__env->startSection('content'); ?>
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2"><?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-clipboard-document-check'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-7 h-7']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?> Presensi Harian</h1><p class="mt-1 text-sm text-gray-500">Input kehadiran siswa per kelas.</p></div>
<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-coral">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
        <div><label class="block text-xs font-extrabold uppercase text-coral-dark mb-1">Kelas</label><select name="kelas_id" onchange="this.form.submit()" class="block w-full border-coral/20 rounded-card"><option value="">Pilih Kelas</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $kelass; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k->id); ?>" <?php echo e($kelasId==$k->id?'selected':''); ?>><?php echo e($k->nama_kelas); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></select></div>
        <div><label class="block text-xs font-extrabold uppercase text-coral-dark mb-1">Tanggal</label><input type="date" name="tanggal" value="<?php echo e($tanggal); ?>" onchange="this.form.submit()" class="block w-full border-coral/20 rounded-card"></div>
    </form>
</div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelasId && $siswa->isNotEmpty()): ?>
<div class="bg-white rounded-card shadow-card overflow-hidden">
    <form method="POST" action="<?php echo e(route('guru.presensi.store')); ?>"><?php echo csrf_field(); ?>
    <input type="hidden" name="kelas_id" value="<?php echo e($kelasId); ?>">
    <input type="hidden" name="tanggal" value="<?php echo e($tanggal); ?>">
    <div class="overflow-x-auto">
    <table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Siswa</th><th class="px-4 py-3 font-extrabold text-xs uppercase text-center">NISN</th><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $jenisAbsens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><th class="px-4 py-3 font-extrabold text-xs uppercase text-center"><?php echo e($ja->nama); ?></th><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></tr></thead>
    <tbody class="divide-y"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $siswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr class="hover:bg-coral-bg/30"><td class="px-4 py-3 font-bold text-sm"><?php echo e($sk->siswa->nama_siswa); ?></td><td class="px-4 py-3 text-center text-gray-500"><?php echo e($sk->siswa->nisn); ?></td>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $jenisAbsens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $checked = ($presensiHariIni->get($sk->siswa_id)?->jenis_absen_id ?? 1) == $ja->id; ?>
    <td class="px-4 py-3 text-center"><input type="radio" name="jenis_absen_id[<?php echo e($sk->siswa_id); ?>]" value="<?php echo e($ja->id); ?>" <?php echo e($checked?'checked':''); ?> class="w-4 h-4 text-coral border-coral/30 focus:ring-coral"></td>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <input type="hidden" name="siswa_id[]" value="<?php echo e($sk->siswa_id); ?>">
    </tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></tbody></table></div>
    <div class="px-4 py-3 border-t flex justify-end"><button class="btn-primary-coral">Simpan Presensi</button></div>
    </form>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views\guru\presensi\index.blade.php ENDPATH**/ ?>