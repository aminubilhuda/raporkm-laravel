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

        // Generate PWA token if checkbox is checked
        if ($this->installPwa) {
            $user = auth()->user();
            $plainToken = Str::random(64);

            PwaToken::create([
                'user_id' => $user->id,
                'token' => Hash::make($plainToken),
                'expires_at' => now()->addYear(),
            ]);

            // Store token in session for JavaScript to pick up
            session(['pwa_token' => $plainToken]);
        }

        $user = auth()->user();

        $redirectTo = match ((int) $user->jabatan) {
            2 => route('tu.dashboard'),
            3, 4 => route('guru.dashboard'),
            default => '/dashboard',
        };

        $this->redirect($redirectTo, navigate: true);
    }
}; ?>

<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input wire:model="form.username" id="username" class="block mt-1 w-full" type="text" name="username" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Install as PWA -->
        <div class="block mt-3">
            <label for="install_pwa" class="inline-flex items-center">
                <input wire:model="installPwa" id="install_pwa" type="checkbox" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500" name="install_pwa">
                <span class="ms-2 text-sm text-gray-600">Simpan sebagai aplikasi (auto-login di Android)</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('livewire:navigated', function() {
        // Check if there's a PWA token in session (after login)
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
