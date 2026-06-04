<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-pencil-square'); ?>
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
            Penilaian
        </h1>
        <p class="mt-1 text-sm text-gray-500">Input nilai formatif, sumatif PH, sumatif TS, dan sumatif AS.</p>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mapelKelasList->isNotEmpty()): ?>
    <div class="bg-white rounded-card shadow-card p-4">
        <label class="block text-sm font-bold text-gray-500 mb-2">Pilih Kelas & Mapel</label>
        <div class="flex flex-wrap gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $mapelKelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('guru.penilaian.index', ['kelas' => $mk->kelas_id, 'mapel' => $mk->mapel_id])); ?>"
                    class="px-4 py-2 text-sm font-bold rounded-pill transition-all <?php echo e($authorized && $kelas->id === $mk->kelas_id && $mapel->id === $mk->mapel_id ? 'bg-coral text-white shadow-coral-glow' : 'bg-surface-base text-gray-600 hover:bg-coral/10 hover:text-coral'); ?>">
                    <?php echo e($mk->kelas->nama_kelas); ?> · <?php echo e($mk->mapel->nama_mapel); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($authorized && $tujuanPembelajaran->isNotEmpty() && $siswa->isNotEmpty()): ?>
        
        <div class="bg-white rounded-card shadow-card overflow-hidden">
            <div class="px-4 py-3 bg-surface-base border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-extrabold text-gray-700 flex items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-document-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-coral']); ?>
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
                    Nilai Formatif
                </h3>
                <span class="text-xs text-gray-400">Bobot 40%</span>
            </div>
            <div class="overflow-x-auto">
                <form method="POST" action="<?php echo e(route('guru.penilaian.formatif')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="kelas_id" value="<?php echo e($kelas->id); ?>">
                    <input type="hidden" name="mapel_id" value="<?php echo e($mapel->id); ?>">
                    <table class="w-full text-sm">
                        <thead class="bg-surface-base text-left">
                            <tr>
                                <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider sticky left-0 bg-surface-base min-w-[160px]">Siswa</th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tujuanPembelajaran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider min-w-[140px]">
                                    <div><?php echo e($tp->kode_tp); ?></div>
                                    <div class="flex gap-1 mt-1 justify-center text-[10px]">
                                        <span class="text-coral">N</span>
                                        <span class="text-sky">M</span>
                                        <span class="text-teal-primary">S</span>
                                    </div>
                                </th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $siswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-coral/5 transition-colors">
                                <td class="px-3 py-2 sticky left-0 bg-white font-bold text-gray-700 text-xs">
                                    <input type="hidden" name="siswa_id[]" value="<?php echo e($sk->siswa_id); ?>">
                                    <?php echo e($sk->siswa->nama_siswa ?? '-'); ?>

                                </td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tujuanPembelajaran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $key = "{$sk->siswa_id}_{$tp->id}"; $nf = $nilaiFormatif->get($key); ?>
                                <td class="px-1 py-2">
                                    <div class="flex gap-1 justify-center">
                                        <input type="number" name="nilai[<?php echo e($tp->id); ?>][<?php echo e($sk->siswa_id); ?>]" value="<?php echo e($nf->nilai ?? ''); ?>" min="0" max="100"
                                            class="w-10 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-coral/30 focus:border-coral outline-none">
                                        <input type="number" name="middle[<?php echo e($tp->id); ?>][<?php echo e($sk->siswa_id); ?>]" value="<?php echo e($nf->middle ?? ''); ?>" min="0" max="100"
                                            class="w-10 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-sky/30 focus:border-sky outline-none">
                                        <input type="number" name="nas[<?php echo e($tp->id); ?>][<?php echo e($sk->siswa_id); ?>]" value="<?php echo e($nf->nas ?? ''); ?>" min="0" max="100"
                                            class="w-10 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-teal/30 focus:border-teal outline-none">
                                    </div>
                                </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                    <div class="px-4 py-3 border-t border-gray-100 text-right">
                        <button type="submit" class="bg-coral text-white px-5 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                            Simpan Formatif
                        </button>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="bg-white rounded-card shadow-card overflow-hidden">
            <div class="px-4 py-3 bg-surface-base border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-extrabold text-gray-700 flex items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-document-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-sky']); ?>
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
                    Nilai Sumatif PH
                </h3>
                <span class="text-xs text-gray-400">Bobot 30%</span>
            </div>
            <div class="overflow-x-auto">
                <form method="POST" action="<?php echo e(route('guru.penilaian.sumatif-ph')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="kelas_id" value="<?php echo e($kelas->id); ?>">
                    <input type="hidden" name="mapel_id" value="<?php echo e($mapel->id); ?>">
                    <table class="w-full text-sm">
                        <thead class="bg-surface-base text-left">
                            <tr>
                                <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider sticky left-0 bg-surface-base min-w-[160px]">Siswa</th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tujuanPembelajaran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider min-w-[120px]">
                                    <div><?php echo e($tp->kode_tp); ?></div>
                                    <div class="text-[10px] font-normal text-gray-400">Nilai</div>
                                </th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $siswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-coral/5 transition-colors">
                                <td class="px-3 py-2 sticky left-0 bg-white font-bold text-gray-700 text-xs">
                                    <input type="hidden" name="siswa_id[]" value="<?php echo e($sk->siswa_id); ?>">
                                    <?php echo e($sk->siswa->nama_siswa ?? '-'); ?>

                                </td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tujuanPembelajaran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $key = "{$sk->siswa_id}_{$tp->id}"; $nph = $nilaiSumatifPh->get($key); ?>
                                <td class="px-1 py-2">
                                    <input type="number" name="nilai[<?php echo e($tp->id); ?>][<?php echo e($sk->siswa_id); ?>]" value="<?php echo e($nph->nilai ?? ''); ?>" min="0" max="100"
                                        class="w-16 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-sky/30 focus:border-sky outline-none mx-auto block">
                                </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                    <div class="px-4 py-3 border-t border-gray-100 text-right">
                        <button type="submit" class="bg-coral text-white px-5 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                            Simpan Sumatif PH
                        </button>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-card shadow-card overflow-hidden">
                <div class="px-4 py-3 bg-surface-base border-b border-gray-100">
                    <h3 class="font-extrabold text-gray-700 flex items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-document-arrow-up'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-gold-dark']); ?>
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
                        Nilai Sumatif TS
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <form method="POST" action="<?php echo e(route('guru.penilaian.sumatif-ts')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="kelas_id" value="<?php echo e($kelas->id); ?>">
                        <input type="hidden" name="mapel_id" value="<?php echo e($mapel->id); ?>">
                        <table class="w-full text-sm">
                            <thead class="bg-surface-base text-left">
                                <tr>
                                    <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Siswa</th>
                                    <th class="px-2 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $siswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $nts = $nilaiSumatifTs->get("{$sk->siswa_id}"); ?>
                                <tr class="hover:bg-coral/5 transition-colors">
                                    <td class="px-3 py-2 font-bold text-gray-700 text-xs"><?php echo e($sk->siswa->nama_siswa ?? '-'); ?></td>
                                    <td class="px-2 py-2">
                                        <input type="hidden" name="siswa_id[]" value="<?php echo e($sk->siswa_id); ?>">
                                        <input type="number" name="nilai[<?php echo e($sk->siswa_id); ?>]" value="<?php echo e($nts->nilai ?? ''); ?>" min="0" max="100"
                                            class="w-16 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-gold/30 focus:border-gold outline-none">
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                        <div class="px-4 py-3 border-t border-gray-100 text-right">
                            <button type="submit" class="bg-coral text-white px-4 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                                Simpan TS
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-card shadow-card overflow-hidden">
                <div class="px-4 py-3 bg-surface-base border-b border-gray-100">
                    <h3 class="font-extrabold text-gray-700 flex items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-document-arrow-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-teal-primary']); ?>
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
                        Nilai Sumatif AS
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <form method="POST" action="<?php echo e(route('guru.penilaian.sumatif-as')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="kelas_id" value="<?php echo e($kelas->id); ?>">
                        <input type="hidden" name="mapel_id" value="<?php echo e($mapel->id); ?>">
                        <table class="w-full text-sm">
                            <thead class="bg-surface-base text-left">
                                <tr>
                                    <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Siswa</th>
                                    <th class="px-2 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $siswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $nas = $nilaiSumatifAs->get("{$sk->siswa_id}"); ?>
                                <tr class="hover:bg-coral/5 transition-colors">
                                    <td class="px-3 py-2 font-bold text-gray-700 text-xs"><?php echo e($sk->siswa->nama_siswa ?? '-'); ?></td>
                                    <td class="px-2 py-2">
                                        <input type="hidden" name="siswa_id[]" value="<?php echo e($sk->siswa_id); ?>">
                                        <input type="number" name="nilai[<?php echo e($sk->siswa_id); ?>]" value="<?php echo e($nas->nilai ?? ''); ?>" min="0" max="100"
                                            class="w-16 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-teal/30 focus:border-teal outline-none">
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                        <div class="px-4 py-3 border-t border-gray-100 text-right">
                            <button type="submit" class="bg-coral text-white px-4 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                                Simpan AS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php elseif($authorized && $tujuanPembelajaran->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center min-h-[30vh] text-center">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-check-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-12 h-12 text-gray-300 mb-3']); ?>
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
            <p class="text-gray-400">Belum ada Tujuan Pembelajaran untuk kelas & mapel ini.</p>
            <a href="<?php echo e(route('guru.tujuan-pembelajaran.index', ['kelas' => $kelas?->id, 'mapel' => $mapel?->id])); ?>" class="mt-3 text-coral font-bold text-sm hover:underline">
                Buat TP dulu →
            </a>
        </div>
    <?php elseif($authorized && $siswa->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center min-h-[30vh] text-center">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-users'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-12 h-12 text-gray-300 mb-3']); ?>
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
            <p class="text-gray-400">Belum ada siswa di kelas ini.</p>
        </div>
    <?php elseif($mapelKelasList->isNotEmpty()): ?>
        <div class="flex flex-col items-center justify-center min-h-[30vh] text-center">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-cursor-arrow-rays'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-12 h-12 text-gray-300 mb-3']); ?>
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
            <p class="text-gray-400">Pilih kelas & mapel terlebih dahulu.</p>
        </div>
    <?php else: ?>
        <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-pencil-square'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-16 h-16 text-gray-300 mb-4']); ?>
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
            <h2 class="text-xl font-extrabold text-gray-400 mb-2">Belum Ada Mapel</h2>
            <p class="text-gray-400">Anda belum terdaftar sebagai pengajar mapel apapun.</p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views/guru/penilaian/index.blade.php ENDPATH**/ ?>