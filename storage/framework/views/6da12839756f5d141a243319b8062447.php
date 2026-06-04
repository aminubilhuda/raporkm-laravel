<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'E-Rapor KM')); ?> — <?php echo e($title ?? 'Profil'); ?></title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-sans antialiased h-full bg-surface-base">
    <div class="min-h-screen flex flex-col">
        <!-- Topbar -->
        <nav class="bg-cream shadow-card sticky top-0 z-30 border-b border-teal-primary/10">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center gap-3">
                        <a href="<?php echo e(auth()->user()->jabatan === 2 ? route('tu.dashboard') : route('guru.dashboard')); ?>" class="flex items-center gap-2 text-teal-primary font-extrabold text-xl">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-book-open'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-6 h-6']); ?>
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
                            <span class="hidden sm:inline">E-Rapor KM</span>
                        </a>
                        <span class="px-3 py-1 text-xs font-bold rounded-pill bg-teal-primary text-white">Profil</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="hidden sm:inline text-sm font-medium text-teal-primary-dark"><?php echo e(auth()->user()->nama); ?></span>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="px-4 py-2 text-sm font-bold text-coral hover:bg-coral/5 rounded-pill transition-colors">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main class="flex-1 p-4 md:p-8">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($header)): ?>
                <div class="max-w-2xl mx-auto mb-6">
                    <?php echo e($header); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="max-w-2xl mx-auto space-y-6">
                <?php echo e($slot); ?>

            </div>
        </main>

        <footer class="py-4 text-center text-xs text-teal-primary/50">
            &copy; <?php echo e(date('Y')); ?> E-Rapor KM — SMK Abdi Negara Tuban
        </footer>
    </div>
</body>
</html><?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views\layouts\app.blade.php ENDPATH**/ ?>