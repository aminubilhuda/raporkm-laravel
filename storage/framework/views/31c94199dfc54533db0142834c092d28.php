<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-chart-bar'); ?>
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
<?php endif; ?>
                Laporan Pendidikan
            </h1>
            <p class="mt-1 text-sm text-gray-500">Rekap agregat nilai, presensi, dan distribusi predikat per tahun ajaran.</p>
        </div>
        <form method="GET" class="flex gap-2 items-end">
            <div>
                <label class="block text-xs text-gray-500">Tahun</label>
                <select name="tahun" class="border-gray-300 rounded-pill px-3 py-1.5 text-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tahunList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tp->id); ?>" <?php if($tahunId == $tp->id): echo 'selected'; endif; ?>><?php echo e($tp->tahun); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500">Semester</label>
                <select name="semester" class="border-gray-300 rounded-pill px-3 py-1.5 text-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $semesterList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->id); ?>" <?php if($semesterId == $s->id): echo 'selected'; endif; ?>><?php echo e($s->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <button class="bg-teal-primary hover:bg-teal-primary-dark text-white font-bold px-4 py-1.5 rounded-pill text-sm">Terapkan</button>
        </form>
    </div>

    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-white rounded-card shadow-card p-4">
            <p class="text-xs uppercase text-gray-500">Kelas Aktif</p>
            <p class="text-2xl font-extrabold text-teal-primary-dark"><?php echo e($kelas_list->count()); ?></p>
        </div>
        <div class="bg-white rounded-card shadow-card p-4">
            <p class="text-xs uppercase text-gray-500">Mapel Dinilai</p>
            <p class="text-2xl font-extrabold text-coral"><?php echo e(count($nilai_per_mapel)); ?></p>
        </div>
        <div class="bg-white rounded-card shadow-card p-4">
            <p class="text-xs uppercase text-gray-500">Hadir</p>
            <p class="text-2xl font-extrabold text-emerald-600"><?php echo e($presensi_rekap['hadir']); ?></p>
        </div>
        <div class="bg-white rounded-card shadow-card p-4">
            <p class="text-xs uppercase text-gray-500">Tanpa Keterangan</p>
            <p class="text-2xl font-extrabold text-rose-600"><?php echo e($presensi_rekap['alpha']); ?></p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        <div class="bg-white rounded-card shadow-card p-5">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-3">Rata-rata Nilai per Mapel</h2>
            <table class="w-full text-sm">
                <thead class="bg-teal-50 text-teal-primary-dark">
                    <tr>
                        <th class="text-left px-3 py-2 rounded-l-pill">Mapel</th>
                        <th class="text-center px-3 py-2">Rata-rata</th>
                        <th class="text-center px-3 py-2">Min</th>
                        <th class="text-center px-3 py-2">Max</th>
                        <th class="text-center px-3 py-2 rounded-r-pill">N</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $nilai_per_mapel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b last:border-0">
                            <td class="px-3 py-2 font-semibold"><?php echo e($row['mapel']->nama_mapel); ?></td>
                            <td class="text-center px-3 py-2 font-bold text-teal-primary-dark"><?php echo e($row['rata_rata'] ?? '—'); ?></td>
                            <td class="text-center px-3 py-2"><?php echo e($row['min'] ?? '—'); ?></td>
                            <td class="text-center px-3 py-2"><?php echo e($row['max'] ?? '—'); ?></td>
                            <td class="text-center px-3 py-2"><?php echo e($row['jumlah']); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-gray-400 py-3">Belum ada data nilai.</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-card shadow-card p-5">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-3">Distribusi Predikat</h2>
            <table class="w-full text-sm">
                <thead class="bg-coral-50 text-coral-dark">
                    <tr>
                        <th class="text-left px-3 py-2 rounded-l-pill">Predikat</th>
                        <th class="text-center px-3 py-2">Jumlah</th>
                        <th class="text-center px-3 py-2 rounded-r-pill">%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $distribusi_predikat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-b last:border-0">
                            <td class="px-3 py-2 font-bold"><?php echo e($d['predikat']); ?></td>
                            <td class="text-center px-3 py-2"><?php echo e($d['jumlah']); ?></td>
                            <td class="text-center px-3 py-2"><?php echo e($d['persen']); ?>%</td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        <div class="bg-white rounded-card shadow-card p-5">
            <h2 class="text-lg font-extrabold text-emerald-700 mb-3">Top 10 Siswa</h2>
            <ol class="space-y-1 text-sm">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $top_bottom['top']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="flex justify-between border-b pb-1 last:border-0">
                        <span><span class="font-bold text-emerald-700"><?php echo e($i+1); ?>.</span> <?php echo e($r['siswa']?->nama_siswa ?? '—'); ?></span>
                        <span class="font-bold"><?php echo e($r['rata_rata']); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="text-gray-400">Belum ada data.</li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ol>
        </div>
        <div class="bg-white rounded-card shadow-card p-5">
            <h2 class="text-lg font-extrabold text-rose-700 mb-3">Bottom 10 Siswa</h2>
            <ol class="space-y-1 text-sm">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $top_bottom['bottom']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="flex justify-between border-b pb-1 last:border-0">
                        <span><span class="font-bold text-rose-700"><?php echo e($i+1); ?>.</span> <?php echo e($r['siswa']?->nama_siswa ?? '—'); ?></span>
                        <span class="font-bold"><?php echo e($r['rata_rata']); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="text-gray-400">Belum ada data.</li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ol>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.tu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views/tu/laporan/pendidikan.blade.php ENDPATH**/ ?>