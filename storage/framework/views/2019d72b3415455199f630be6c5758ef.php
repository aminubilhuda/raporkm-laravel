<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title',
    'icon' => 'heroicon-o-chart-bar',
    'progress' => 0,
    'accent' => 'teal',
    'footer' => null,
    'animate' => true,
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
    'title',
    'icon' => 'heroicon-o-chart-bar',
    'progress' => 0,
    'accent' => 'teal',
    'footer' => null,
    'animate' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $gradientClass = match($accent) {
        'coral' => 'stat-card-coral',
        'gold' => 'stat-card-gold',
        'sky' => 'stat-card-sky',
        default => 'stat-card-teal',
    };
    $fillClass = match($accent) {
        'coral' => 'progress-fill-coral',
        'gold' => 'progress-fill-gold',
        default => 'progress-fill-teal',
    };
    $circleClass = match($accent) {
        'coral' => 'icon-circle-coral',
        'gold' => 'icon-circle-gold',
        'sky' => 'icon-circle-sky',
        default => 'icon-circle-teal',
    };
    $textClass = match($accent) {
        'coral' => 'text-coral',
        'gold' => 'text-gold-dark',
        'sky' => 'text-sky',
        default => 'text-teal-primary',
    };
    $pct = max(0, min(100, (int) $progress));
?>

<div class="stat-card <?php echo e($gradientClass); ?> animate-fade-up">
    <div class="flex items-center gap-3 mb-3">
        <div class="icon-circle <?php echo e($circleClass); ?> w-9 h-9">
            <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $icon] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
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
        <h3 class="text-sm font-extrabold text-gray-700"><?php echo e($title); ?></h3>
    </div>

    <div class="progress-track">
        <div class="progress-fill <?php echo e($fillClass); ?>" style="width: <?php echo e($pct); ?>%"></div>
    </div>

    <div class="flex items-center justify-between mt-2">
        <span class="text-xs font-bold <?php echo e($textClass); ?>"><?php echo e($pct); ?>%</span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footer): ?>
            <span class="text-xs text-gray-400"><?php echo e($footer); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div><?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views\components\progress-card.blade.php ENDPATH**/ ?>