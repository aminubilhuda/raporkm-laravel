<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => auth()->user()->nama,
    'panel' => 'TU',
    'tahun' => '2025/2026',
    'semester' => 'Genap',
    'subtitle' => null,
    'accent' => 'teal',
]));

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

foreach (array_filter(([
    'name' => auth()->user()->nama,
    'panel' => 'TU',
    'tahun' => '2025/2026',
    'semester' => 'Genap',
    'subtitle' => null,
    'accent' => 'teal',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $today = $days[now()->dayOfWeek] . ', ' . now()->day . ' ' . [
    'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
][now()->month - 1] . ' ' . now()->year;
    $gradient = $accent === 'coral' ? 'from-coral/5 to-coral/10' : 'from-teal-bg to-teal-primary/5';
    $iconClass = $accent === 'coral' ? 'bg-coral/10 text-coral' : 'bg-teal-primary/10 text-teal-primary';
    $nameClass = $accent === 'coral' ? 'text-coral-dark' : 'text-teal-primary-dark';
    $badgeBg = $accent === 'coral' ? 'bg-coral' : 'bg-teal-primary';
    $userIcon = auth()->user()->jabatan === 2 ? 'heroicon-o-user' : (auth()->user()->jabatan === 4 ? 'heroicon-o-user' : 'heroicon-o-academic-cap');
?>

<div class="animate-fade-up stagger-1">
    <div class="relative overflow-hidden rounded-card bg-gradient-to-br <?php echo e($gradient); ?> p-5 md:p-6 shadow-card border border-teal-primary/5">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="icon-circle <?php echo e($iconClass); ?> w-14 h-14 flex-shrink-0">
                <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $userIcon] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-7 h-7']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
            </div>

            <div class="flex-1 min-w-0">
                <h1 class="text-xl md:text-2xl font-extrabold <?php echo e($nameClass); ?> leading-tight">
                    Selamat datang, <?php echo e(explode(' ', trim($name))[0]); ?>

                </h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    Panel <?php echo e($panel === 'TU' ? 'Tata Usaha' : ($panel === 'Guru' ? 'Guru' : $panel)); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subtitle): ?>
                        <span class="mx-1.5 text-gray-300">·</span> <?php echo e($subtitle); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>

            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="text-right">
                    <p class="text-xs text-gray-400"><?php echo e($today); ?></p>
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-pill text-xs font-bold <?php echo e($badgeBg); ?> text-white">
                            <svg class="w-2.5 h-2.5" viewBox="0 0 10 10"><circle cx="5" cy="5" r="4" fill="currentColor"/></svg>
                            <?php echo e($semester); ?> <?php echo e($tahun); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views\components\welcome-banner.blade.php ENDPATH**/ ?>