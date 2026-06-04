<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-document-chart-bar'); ?>
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
            Lager Nilai Kelas
        </h1>
        <p class="mt-1 text-sm text-gray-500">Konsolidasi nilai akhir per mapel per siswa.</p>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelasList->isNotEmpty()): ?>
    <div class="bg-white rounded-card shadow-card p-4">
        <label class="block text-sm font-bold text-gray-500 mb-2">Pilih Kelas</label>
        <div class="flex flex-wrap gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('guru.lager-nilai-kelas.index', $k)); ?>"
                    class="px-4 py-2 text-sm font-bold rounded-pill transition-all <?php echo e($authorized && $kelas->id === $k->id ? 'bg-coral text-white shadow-coral-glow' : 'bg-surface-base text-gray-600 hover:bg-coral/10 hover:text-coral'); ?>">
                    <?php echo e($k->nama_kelas); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($authorized && $siswa->isNotEmpty() && $mapelGuru->isNotEmpty()): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $mapelGuru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-card shadow-card overflow-hidden">
            <div class="px-4 py-3 bg-surface-base border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-extrabold text-gray-700">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-book-open'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 inline-block mr-1 text-sky']); ?>
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
                    <?php echo e($mg->mapel->nama_mapel ?? '-'); ?>

                </h3>
                <span class="text-xs text-gray-400">KKM: <?php echo e($mg->kkm); ?></span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface-base text-left">
                        <tr>
                            <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider min-w-[160px]">Siswa</th>
                            <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider w-16">Formatif</th>
                            <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider w-16">PH</th>
                            <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider w-16">AS</th>
                            <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider w-16">NA</th>
                            <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">Predikat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $siswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $key = "{$sk->siswa_id}_{$mg->mapel_id}";
                            $nm = $nilaiMapel->get($key);
                            $formatif = $rataFormatif->get($sk->siswa_id) ?? 0;
                            $ph = $rataPh->get($sk->siswa_id) ?? 0;
                            $as = $nilaiAs->get($key)?->nilai ?? 0;
                            $na = $nm?->nilai ?? round(($formatif * 0.4 + $ph * 0.3 + $as * 0.3), 0);
                        ?>
                        <tr class="hover:bg-coral/5 transition-colors">
                            <td class="px-3 py-2 font-bold text-gray-700 text-xs"><?php echo e($sk->siswa->nama_siswa ?? '-'); ?></td>
                            <td class="px-2 py-2 text-center text-gray-600"><?php echo e($formatif ?: '-'); ?></td>
                            <td class="px-2 py-2 text-center text-gray-600"><?php echo e($ph ?: '-'); ?></td>
                            <td class="px-2 py-2 text-center text-gray-600"><?php echo e($as ?: '-'); ?></td>
                            <td class="px-2 py-2 text-center font-bold <?php echo e($na >= 75 ? 'text-success' : 'text-coral'); ?>"><?php echo e($na ?: '-'); ?></td>
                            <td class="px-2 py-2 text-center">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nm?->predikat): ?>
                                <span class="px-2 py-0.5 text-xs font-bold rounded-pill
                                    <?php echo e($nm->predikat == 'SB' ? 'bg-teal/10 text-teal-primary' : ''); ?>

                                    <?php echo e($nm->predikat == 'B' ? 'bg-sky/10 text-sky' : ''); ?>

                                    <?php echo e($nm->predikat == 'C' ? 'bg-gold/10 text-gold-dark' : ''); ?>

                                    <?php echo e($nm->predikat == 'PB' ? 'bg-coral/10 text-coral' : ''); ?>">
                                    <?php echo e($nm->predikat); ?>

                                </span>
                                <?php else: ?>
                                <span class="text-gray-300">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php elseif($authorized && $siswa->isEmpty()): ?>
        <div class="text-center py-12 text-gray-400">Belum ada siswa di kelas ini.</div>
    <?php elseif($authorized && $mapelGuru->isEmpty()): ?>
        <div class="text-center py-12 text-gray-400">Anda tidak mengajar mapel di kelas ini.</div>
    <?php elseif($kelasList->isNotEmpty()): ?>
        <div class="text-center py-12 text-gray-400">Pilih kelas terlebih dahulu.</div>
    <?php else: ?>
        <div class="text-center py-12 text-gray-400">Anda belum memiliki kelas.</div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views/guru/lager-nilai-kelas/index.blade.php ENDPATH**/ ?>