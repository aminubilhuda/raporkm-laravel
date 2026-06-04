<button
    <?php echo e($attributes->merge([
        'type' => 'button',
        'class' => 'inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-cream border-2 border-teal-primary rounded-pill font-bold text-sm text-teal-primary tracking-wide hover:bg-teal-bg focus:outline-none focus:ring-2 focus:ring-teal-primary/30 focus:ring-offset-2 transition-all duration-200 active:scale-95 disabled:opacity-50',
    ])); ?>>
    <?php echo e($slot); ?>

</button><?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views\components\secondary-button.blade.php ENDPATH**/ ?>