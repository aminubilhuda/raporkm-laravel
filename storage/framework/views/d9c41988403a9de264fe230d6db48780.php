<?php

use App\Livewire\Forms\LoginForm;
use App\Models\PwaToken;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

?>

<div>

<div class="auth-brand">
    <div class="auth-brand-tile">
        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-book-open'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-7 h-7 text-teal-primary']); ?>
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
    <h1 class="auth-brand-title">E-Rapor KM</h1>
    <p class="auth-brand-tagline">Kurikulum Merdeka</p>
    <p class="auth-brand-school">SMK Abdi Negara Tuban</p>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
    <div class="auth-status mb-5"><?php echo e(session('status')); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<form wire:submit="login" class="space-y-5">

    
    <div class="field">
        <label for="username" class="field-label"><?php echo e(__('Username')); ?></label>
        <input
            wire:model="form.username"
            id="username"
            type="text"
            name="username"
            required autofocus autocomplete="username"
            class="field-input <?php $__errorArgs = ['form.username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-input--error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            placeholder="Masukkan username"
        />
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="field-error"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="field">
        <label for="password" class="field-label"><?php echo e(__('Password')); ?></label>
        <input
            wire:model="form.password"
            id="password"
            type="password"
            name="password"
            required autocomplete="current-password"
            class="field-input <?php $__errorArgs = ['form.password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-input--error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            placeholder="Masukkan password"
        />
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="field-error"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="flex items-center">
        <input
            wire:model="form.remember"
            id="remember"
            type="checkbox"
            class="check"
        />
        <label for="remember" class="check-label"><?php echo e(__('Remember me')); ?></label>
    </div>

    
    <div class="pwa-row">
        <input
            wire:model="installPwa"
            id="install_pwa"
            type="checkbox"
            class="check mt-0.5"
        />
        <div class="pwa-row-body">
            <label for="install_pwa" class="pwa-row-title">Simpan sebagai aplikasi</label>
            <p class="pwa-row-help">Aktifkan auto-login di perangkat Android</p>
        </div>
    </div>

    
    <div class="auth-actions">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Route::has('password.request')): ?>
            <a
                href="<?php echo e(route('password.request')); ?>"
                wire:navigate
                class="auth-link"
            >
                <?php echo e(__('Forgot your password?')); ?>

            </a>
        <?php else: ?>
            <span></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <button type="submit" class="auth-submit">
            <?php echo e(__('Log in')); ?>

        </button>
    </div>

</form>
</div>

<script>
    document.addEventListener('livewire:navigated', function() {
        const pwaToken = '<?php echo e(session("pwa_token")); ?>';
        if (pwaToken && pwaToken !== '') {
            localStorage.setItem('pwa_token', pwaToken);
            localStorage.setItem('pwa_user', JSON.stringify({
                nama: '<?php echo e(auth()->user()->nama ?? ""); ?>',
                jabatan: '<?php echo e(auth()->user()->jabatan ?? ""); ?>'
            }));
        }
    });
</script><?php /**PATH D:\PROJECT\php\laravel\raporkm-laravel\resources\views\livewire/pages/auth/login.blade.php ENDPATH**/ ?>