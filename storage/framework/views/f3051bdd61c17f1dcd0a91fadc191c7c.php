<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'E-Rapor KM')); ?></title>
    <link rel="manifest" href="<?php echo e(asset('manifest.json')); ?>">
    <meta name="theme-color" content="#2BA8A2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 bg-gradient-to-br from-teal-bg to-teal-primary/10">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-card bg-white shadow-teal-glow mb-4">
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-book-open'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8 text-teal-primary']); ?>
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
            <h1 class="text-3xl font-extrabold text-teal-primary-dark tracking-wide">E-Rapor KM</h1>
            <p class="text-sm font-medium text-teal-primary/70 mt-1 tracking-widest uppercase">Kurikulum Merdeka</p>
            <p class="text-xs text-gray-500 mt-0.5">SMK Abdi Negara Tuban</p>
        </div>

        <div class="w-full max-w-md bg-white rounded-card shadow-card overflow-hidden">
            <?php echo e($slot); ?>

        </div>

        <p class="mt-8 text-xs text-teal-primary/50">&copy; <?php echo e(date('Y')); ?> E-Rapor KM</p>
    </div>

    <script src="<?php echo e(asset('js/pwa.js')); ?>"></script>
    <script>
        // Auto-login check for PWA
        document.addEventListener('DOMContentLoaded', async function() {
            const loggedIn = await window.pwaAutoLogin();
            if (loggedIn) {
                const user = window.getPwaUser();
                if (user) {
                    const redirectTo = user.jabatan == 2 ? '/tu/dashboard' : '/guru/dashboard';
                    window.location.href = redirectTo;
                }
            }
        });
    </script>
</body>
</html><?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views\layouts\guest.blade.php ENDPATH**/ ?>