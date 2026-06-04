<?php $__env->startSection('content'); ?>
<div class="space-y-6 md:space-y-8">
    <?php if (isset($component)) { $__componentOriginal183e7fae59744e715a1d11086aff17e0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal183e7fae59744e715a1d11086aff17e0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.welcome-banner','data' => ['panel' => 'Guru','title' => 'Selamat Datang, '.e(auth()->user()->nama).'','subtitle' => ''.e($totalKelasWali > 0 ? $totalKelasWali.' Kelas Wali' : '').' · '.e($totalMapelDiajar).' Mapel Diajar','accent' => 'coral']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('welcome-banner'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['panel' => 'Guru','title' => 'Selamat Datang, '.e(auth()->user()->nama).'','subtitle' => ''.e($totalKelasWali > 0 ? $totalKelasWali.' Kelas Wali' : '').' · '.e($totalMapelDiajar).' Mapel Diajar','accent' => 'coral']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.quick-action','data' => ['icon' => 'heroicon-o-pencil-square','label' => 'Input Nilai','href' => ''.e(route('guru.penilaian.index')).'','accent' => 'coral']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('quick-action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-pencil-square','label' => 'Input Nilai','href' => ''.e(route('guru.penilaian.index')).'','accent' => 'coral']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.quick-action','data' => ['icon' => 'heroicon-o-check-badge','label' => 'TP Baru','href' => ''.e(route('guru.tujuan-pembelajaran.index')).'','accent' => 'gold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('quick-action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-check-badge','label' => 'TP Baru','href' => ''.e(route('guru.tujuan-pembelajaran.index')).'','accent' => 'gold']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.quick-action','data' => ['icon' => 'heroicon-o-building-office-2','label' => 'Kelas Saya','href' => ''.e(route('guru.kelas-ku.index')).'','accent' => 'sky']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('quick-action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-building-office-2','label' => 'Kelas Saya','href' => ''.e(route('guru.kelas-ku.index')).'','accent' => 'sky']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.quick-action','data' => ['icon' => 'heroicon-o-clipboard-document-check','label' => 'Presensi','href' => ''.e(route('guru.presensi.rekap')).'','accent' => 'teal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('quick-action'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-clipboard-document-check','label' => 'Presensi','href' => ''.e(route('guru.presensi.rekap')).'','accent' => 'teal']); ?>
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

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-5">
        <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['title' => 'Kelas Wali','value' => ''.e($totalKelasWali).'','icon' => 'heroicon-o-building-office-2','accent' => 'coral','stagger' => 'stagger-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Kelas Wali','value' => ''.e($totalKelasWali).'','icon' => 'heroicon-o-building-office-2','accent' => 'coral','stagger' => 'stagger-2']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['title' => 'Mapel Diajar','value' => ''.e($totalMapelDiajar).'','icon' => 'heroicon-o-book-open','accent' => 'coral','stagger' => 'stagger-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Mapel Diajar','value' => ''.e($totalMapelDiajar).'','icon' => 'heroicon-o-book-open','accent' => 'coral','stagger' => 'stagger-3']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['title' => 'Total Siswa','value' => ''.e($totalSiswa).'','icon' => 'heroicon-o-users','accent' => 'gold','stagger' => 'stagger-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Total Siswa','value' => ''.e($totalSiswa).'','icon' => 'heroicon-o-users','accent' => 'gold','stagger' => 'stagger-4']); ?>
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
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelasWaliList->isNotEmpty()): ?>
                    <?php $first = $kelasWaliList->first(); ?>
                    <?php if (isset($component)) { $__componentOriginaleee771d96edcf8b2233a3aebf102d654 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaleee771d96edcf8b2233a3aebf102d654 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.progress-card','data' => ['title' => 'Kelas Wali: '.e($first->nama_kelas ?? '-').'','icon' => 'heroicon-o-academic-cap','accent' => 'coral','progress' => 0,'footer' => 'Siswa: '.e($siswaWali).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('progress-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Kelas Wali: '.e($first->nama_kelas ?? '-').'','icon' => 'heroicon-o-academic-cap','accent' => 'coral','progress' => 0,'footer' => 'Siswa: '.e($siswaWali).'']); ?>
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
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginaleee771d96edcf8b2233a3aebf102d654 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaleee771d96edcf8b2233a3aebf102d654 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.progress-card','data' => ['title' => 'Belum Ada Kelas Wali','icon' => 'heroicon-o-academic-cap','accent' => 'coral','progress' => 0,'footer' => '-']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('progress-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Belum Ada Kelas Wali','icon' => 'heroicon-o-academic-cap','accent' => 'coral','progress' => 0,'footer' => '-']); ?>
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
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

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
                <h3 class="text-sm font-extrabold text-gray-700">Timeline Penting</h3>
            </div>
            <div class="space-y-2.5">
                <div class="flex items-center gap-2 text-sm">
                    <div class="w-2 h-2 rounded-full bg-coral flex-shrink-0"></div>
                    <span class="text-gray-500">Batas input nilai</span>
                    <span class="ml-auto font-bold text-coral">10 Jun</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <div class="w-2 h-2 rounded-full bg-gold flex-shrink-0"></div>
                    <span class="text-gray-500">Cetak rapor mid</span>
                    <span class="ml-auto font-bold text-gold-dark">15 Jun</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <div class="w-2 h-2 rounded-full bg-sky flex-shrink-0"></div>
                    <span class="text-gray-500">Pembagian rapor</span>
                    <span class="ml-auto font-bold text-sky">20 Jun</span>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-sky animate-fade-up stagger-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="icon-circle icon-circle-sky w-9 h-9">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-chart-bar'); ?>
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
                <h3 class="text-sm font-extrabold text-gray-700">Ringkasan</h3>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Total siswa diampu</span>
                    <span class="font-bold text-sky"><?php echo e($totalSiswa); ?></span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Kelas wali</span>
                    <span class="font-bold text-coral"><?php echo e($siswaWali); ?> siswa</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Kelas diajar</span>
                    <span class="font-bold text-teal-primary"><?php echo e($mapelDiajar->pluck('kelas_id')->unique()->count()); ?> kelas</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views/guru/dashboard.blade.php ENDPATH**/ ?>