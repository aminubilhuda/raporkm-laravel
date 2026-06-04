<?php $__env->startSection('content'); ?>
<div class="space-y-6 md:space-y-8">
    
    <?php if (isset($component)) { $__componentOriginal183e7fae59744e715a1d11086aff17e0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal183e7fae59744e715a1d11086aff17e0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.welcome-banner','data' => ['panel' => 'TU','tahun' => '2025/2026','semester' => 'Genap','accent' => 'teal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('welcome-banner'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['panel' => 'TU','tahun' => '2025/2026','semester' => 'Genap','accent' => 'teal']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal183e7fae59744e715a1d11086aff17e0)): ?>
<?php $attributes = $__attributesOriginal183e7fae59744e715a1d11086aff17e0; ?>
<?php unset($__attributesOriginal183e7fae59744e715a1d11086aff17e0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal183e7fae59744e715a1d11086aff17e0)): ?>
<?php $component = $__componentOriginal183e7fae59744e715a1d11086aff17e0; ?>
<?php unset($__componentOriginal183e7fae59744e715a1d11086aff17e0); ?>
<?php endif; ?>

    
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4">
        <?php if (isset($component)) { $__componentOriginala8d780f63e732384c5d89ba5ec71ad14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8d780f63e732384c5d89ba5ec71ad14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.quick-action','data' => ['icon' => 'heroicon-o-user-plus','label' => 'Tambah Siswa','href' => '#','accent' => 'teal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('quick-action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-user-plus','label' => 'Tambah Siswa','href' => '#','accent' => 'teal']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8d780f63e732384c5d89ba5ec71ad14)): ?>
<?php $attributes = $__attributesOriginala8d780f63e732384c5d89ba5ec71ad14; ?>
<?php unset($__attributesOriginala8d780f63e732384c5d89ba5ec71ad14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8d780f63e732384c5d89ba5ec71ad14)): ?>
<?php $component = $__componentOriginala8d780f63e732384c5d89ba5ec71ad14; ?>
<?php unset($__componentOriginala8d780f63e732384c5d89ba5ec71ad14); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginala8d780f63e732384c5d89ba5ec71ad14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8d780f63e732384c5d89ba5ec71ad14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.quick-action','data' => ['icon' => 'heroicon-o-arrow-up-tray','label' => 'Import Data','href' => '#','accent' => 'gold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('quick-action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-arrow-up-tray','label' => 'Import Data','href' => '#','accent' => 'gold']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8d780f63e732384c5d89ba5ec71ad14)): ?>
<?php $attributes = $__attributesOriginala8d780f63e732384c5d89ba5ec71ad14; ?>
<?php unset($__attributesOriginala8d780f63e732384c5d89ba5ec71ad14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8d780f63e732384c5d89ba5ec71ad14)): ?>
<?php $component = $__componentOriginala8d780f63e732384c5d89ba5ec71ad14; ?>
<?php unset($__componentOriginala8d780f63e732384c5d89ba5ec71ad14); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginala8d780f63e732384c5d89ba5ec71ad14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8d780f63e732384c5d89ba5ec71ad14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.quick-action','data' => ['icon' => 'heroicon-o-printer','label' => 'Cetak Rapor','href' => '#','accent' => 'sky']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('quick-action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-printer','label' => 'Cetak Rapor','href' => '#','accent' => 'sky']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8d780f63e732384c5d89ba5ec71ad14)): ?>
<?php $attributes = $__attributesOriginala8d780f63e732384c5d89ba5ec71ad14; ?>
<?php unset($__attributesOriginala8d780f63e732384c5d89ba5ec71ad14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8d780f63e732384c5d89ba5ec71ad14)): ?>
<?php $component = $__componentOriginala8d780f63e732384c5d89ba5ec71ad14; ?>
<?php unset($__componentOriginala8d780f63e732384c5d89ba5ec71ad14); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginala8d780f63e732384c5d89ba5ec71ad14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8d780f63e732384c5d89ba5ec71ad14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.quick-action','data' => ['icon' => 'heroicon-o-document-chart-bar','label' => 'Lihat Laporan','href' => '#','accent' => 'coral']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('quick-action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-document-chart-bar','label' => 'Lihat Laporan','href' => '#','accent' => 'coral']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8d780f63e732384c5d89ba5ec71ad14)): ?>
<?php $attributes = $__attributesOriginala8d780f63e732384c5d89ba5ec71ad14; ?>
<?php unset($__attributesOriginala8d780f63e732384c5d89ba5ec71ad14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8d780f63e732384c5d89ba5ec71ad14)): ?>
<?php $component = $__componentOriginala8d780f63e732384c5d89ba5ec71ad14; ?>
<?php unset($__componentOriginala8d780f63e732384c5d89ba5ec71ad14); ?>
<?php endif; ?>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
        <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['title' => 'Total Siswa','value' => ''.e($totalSiswa).'','icon' => 'heroicon-o-academic-cap','accent' => 'teal','stagger' => 'stagger-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Total Siswa','value' => ''.e($totalSiswa).'','icon' => 'heroicon-o-academic-cap','accent' => 'teal','stagger' => 'stagger-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['title' => 'Total Kelas','value' => ''.e($totalKelas).'','icon' => 'heroicon-o-building-office-2','accent' => 'teal','stagger' => 'stagger-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Total Kelas','value' => ''.e($totalKelas).'','icon' => 'heroicon-o-building-office-2','accent' => 'teal','stagger' => 'stagger-3']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['title' => 'Total Mapel','value' => ''.e($totalMapel).'','icon' => 'heroicon-o-book-open','accent' => 'gold','stagger' => 'stagger-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Total Mapel','value' => ''.e($totalMapel).'','icon' => 'heroicon-o-book-open','accent' => 'gold','stagger' => 'stagger-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['title' => 'Total Guru','value' => ''.e($totalGuru).'','icon' => 'heroicon-o-users','accent' => 'sky','stagger' => 'stagger-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Total Guru','value' => ''.e($totalGuru).'','icon' => 'heroicon-o-users','accent' => 'sky','stagger' => 'stagger-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5">
        <?php if (isset($component)) { $__componentOriginaleee771d96edcf8b2233a3aebf102d654 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaleee771d96edcf8b2233a3aebf102d654 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.progress-card','data' => ['title' => 'Progress Semester','icon' => 'heroicon-o-clock','accent' => 'teal','progress' => 65,'footer' => '78 hari tersisa']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('progress-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Progress Semester','icon' => 'heroicon-o-clock','accent' => 'teal','progress' => 65,'footer' => '78 hari tersisa']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaleee771d96edcf8b2233a3aebf102d654)): ?>
<?php $attributes = $__attributesOriginaleee771d96edcf8b2233a3aebf102d654; ?>
<?php unset($__attributesOriginaleee771d96edcf8b2233a3aebf102d654); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaleee771d96edcf8b2233a3aebf102d654)): ?>
<?php $component = $__componentOriginaleee771d96edcf8b2233a3aebf102d654; ?>
<?php unset($__componentOriginaleee771d96edcf8b2233a3aebf102d654); ?>
<?php endif; ?>

        <div class="stat-card stat-card-gold animate-fade-up stagger-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="icon-circle icon-circle-gold w-9 h-9">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-calendar-days'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
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
                </div>
                <h3 class="text-sm font-extrabold text-gray-700">Pembagian Rapor</h3>
            </div>
            <div class="space-y-2.5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Rapor Mid Semester</span>
                    <span class="font-bold text-gold-dark">15 Jun 2026</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Rapor Semester</span>
                    <span class="font-bold text-gold-dark">20 Des 2026</span>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-sky animate-fade-up stagger-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="icon-circle icon-circle-sky w-9 h-9">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-users'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
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
                </div>
                <h3 class="text-sm font-extrabold text-gray-700">Sebaran Siswa</h3>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Laki-laki</span>
                    <span class="flex items-center gap-2">
                        <span class="font-bold text-sky">120</span>
                        <span class="text-xs text-gray-400">48%</span>
                    </span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill progress-fill-teal" style="width: 48%"></div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Perempuan</span>
                    <span class="flex items-center gap-2">
                        <span class="font-bold text-coral">130</span>
                        <span class="text-xs text-gray-400">52%</span>
                    </span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill progress-fill-coral" style="width: 52%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.tu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views\tu\dashboard.blade.php ENDPATH**/ ?>