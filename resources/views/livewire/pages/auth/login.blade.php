<?php

use App\Livewire\Forms\LoginForm;
use App\Models\PwaToken;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;
    public bool $installPwa = false;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        if ($this->installPwa) {
            $user = auth()->user();
            $plainToken = Str::random(64);

            PwaToken::create([
                'user_id' => $user->id,
                'token' => Hash::make($plainToken),
                'expires_at' => now()->addYear(),
            ]);

            session(['pwa_token' => $plainToken]);
        }

        $user = auth()->user();

        $redirectTo = match ((int) $user->jabatan) {
            2 => route('tu.dashboard'),
            3, 4 => route('guru.dashboard'),
            default => '/dashboard',
        };

        $this->redirect($redirectTo, navigate: false);
    }
}; ?>

<div>
{{-- Brand --}}
<div class="auth-brand">
    <div class="auth-brand-tile">
        <x-heroicon-o-book-open class="w-7 h-7 text-teal-primary" />
    </div>
    <h1 class="auth-brand-title">E-Rapor KM</h1>
    <p class="auth-brand-tagline">Kurikulum Merdeka</p>
    <p class="auth-brand-school">SMK Abdi Negara Tuban</p>
</div>

{{-- Session status --}}
@if (session('status'))
    <div class="auth-status mb-5">{{ session('status') }}</div>
@endif

<form wire:submit="login" class="space-y-5">

    {{-- Username --}}
    <div class="field">
        <label for="username" class="field-label">{{ __('Username') }}</label>
        <input
            wire:model="form.username"
            id="username"
            type="text"
            name="username"
            required autofocus autocomplete="username"
            class="field-input @error('form.username') field-input--error @enderror"
            placeholder="Masukkan username"
        />
        @error('form.username')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    {{-- Password --}}
    <div class="field">
        <label for="password" class="field-label">{{ __('Password') }}</label>
        <input
            wire:model="form.password"
            id="password"
            type="password"
            name="password"
            required autocomplete="current-password"
            class="field-input @error('form.password') field-input--error @enderror"
            placeholder="Masukkan password"
        />
        @error('form.password')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    {{-- Remember me --}}
    <div class="flex items-center">
        <input
            wire:model="form.remember"
            id="remember"
            type="checkbox"
            class="check"
        />
        <label for="remember" class="check-label">{{ __('Remember me') }}</label>
    </div>

    {{-- PWA install --}}
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

    {{-- Actions --}}
    <div class="auth-actions">
        @if (Route::has('password.request'))
            <a
                href="{{ route('password.request') }}"
                wire:navigate
                class="auth-link"
            >
                {{ __('Forgot your password?') }}
            </a>
        @else
            <span></span>
        @endif

        <button type="submit" class="auth-submit">
            {{ __('Log in') }}
        </button>
    </div>

</form>
</div>

<script>
    document.addEventListener('livewire:navigated', function() {
        const pwaToken = '{{ session("pwa_token") }}';
        if (pwaToken && pwaToken !== '') {
            localStorage.setItem('pwa_token', pwaToken);
            localStorage.setItem('pwa_user', JSON.stringify({
                nama: '{{ auth()->user()->nama ?? "" }}',
                jabatan: '{{ auth()->user()->jabatan ?? "" }}'
            }));
        }
    });
</script>
