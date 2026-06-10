<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['route']));

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

foreach (array_filter((['route']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="px-4 mt-3 mb-2">
    <form method="POST" action="<?php echo e(route($route)); ?>" id="semester-switcher-form">
        <?php echo csrf_field(); ?>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-teal-light/60 mb-1">Semester Aktif</label>
        <div class="relative">
            <select name="semester_select" onchange="window.__submitSemester(this)" class="w-full text-xs rounded-lg px-2 py-1.5 cursor-pointer focus:border-gold focus:ring-1 focus:ring-gold appearance-none pr-7 bg-cream"
                style="background: rgba(255,255,255,0.12); color: #fff; border: 1px solid rgba(255,255,255,0.2);">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $semesterOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <option value="<?php echo e($opt['tahun_id']); ?>-<?php echo e($opt['semester_id']); ?>"
                        <?php echo e($activeTpId == $opt['tahun_id'] && $activeSemesterId == $opt['semester_id'] ? 'selected' : ''); ?>

                        style="background: #fff; color: #1a1a1a;">
                        <?php echo e($opt['label']); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <option value="" disabled style="background: #fff; color: #999;">Tidak ada data</option>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <div class="pointer-events-none absolute inset-y-0 flex items-center" style="right: 12px;">
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-chevron-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3.5 h-3.5 text-white/70']); ?>
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
        </div>
        <input type="hidden" name="tahun_pelajaran_id" id="tp-hidden" class="bg-cream rounded-field border-teal-primary/20">
        <input type="hidden" name="semester_id" id="sem-hidden" class="bg-cream rounded-field border-teal-primary/20">
    </form>
</div>

<script>
(function() {
    function submitSemester(select) {
        var parts = select.value.split('-');
        document.getElementById('tp-hidden').value = parts[0];
        document.getElementById('sem-hidden').value = parts[1];
        select.form.submit();
    }
    window.__submitSemester = submitSemester;

    var sel = document.querySelector('[name="semester_select"]');
    if (sel && sel.value && sel.value.indexOf('-') !== -1) {
        var parts = sel.value.split('-');
        var tpH = document.getElementById('tp-hidden');
        var semH = document.getElementById('sem-hidden');
        if (tpH) tpH.value = parts[0];
        if (semH) semH.value = parts[1];
    }
})();
</script>
<?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views/components/semester-switcher.blade.php ENDPATH**/ ?>