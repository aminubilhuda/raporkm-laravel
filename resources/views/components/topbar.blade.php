@props(['panel' => 'TU'])

@php
    $isKepsek = auth()->user()->jabatan === 4;
    $badgeColor = $panel === 'TU' ? 'bg-teal-primary text-white' : ($isKepsek ? 'bg-gold text-teal-dark' : 'bg-coral text-white');
    $badgeLabel = $panel === 'TU' ? 'TU' : ($isKepsek ? 'Kepsek' : 'Guru');
    $iconColor = $panel === 'TU' ? 'text-teal-primary' : 'text-coral';
    $iconBg = $panel === 'TU' ? 'bg-teal-primary/10 hover:bg-teal-primary/20' : 'bg-coral/10 hover:bg-coral/20';
    $dropdownBorder = $panel === 'TU' ? 'border-teal-primary/10' : 'border-coral/10';
    $hoverBg = $panel === 'TU' ? 'hover:bg-teal-bg' : 'hover:bg-coral/5';
    $nameColor = $panel === 'TU' ? 'text-teal-primary-dark' : 'text-coral-dark';
@endphp

<nav class="bg-cream shadow-card sticky top-0 z-50 {{ $panel === 'TU' ? 'border-b border-teal-primary/10' : 'border-b border-coral/10' }}">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center gap-3">
                <button data-toggle-sidebar class="md:hidden p-2 -ml-2 rounded-card {{ $iconColor }} {{ $iconBg }} transition-colors" aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <span class="text-xl font-extrabold {{ $iconColor }} hidden sm:block">E-Rapor KM</span>
                <span class="text-xl font-extrabold {{ $iconColor }} sm:hidden">ERKM</span>

                <span class="px-3 py-1 text-xs font-bold rounded-pill {{ $badgeColor }}">{{ $badgeLabel }}</span>
            </div>

            <div class="flex items-center gap-3">
                <span class="hidden sm:inline text-sm font-medium {{ $nameColor }}">{{ auth()->user()->nama }}</span>
                <span class="sm:hidden text-sm font-medium {{ $nameColor }} truncate max-w-[120px]">{{ auth()->user()->nama }}</span>

                <div id="user-dropdown" class="relative">
                    <button data-dropdown-trigger class="flex items-center justify-center w-9 h-9 rounded-full {{ $iconBg }} transition-colors">
                        <x-heroicon-o-user class="w-5 h-5" />
                    </button>

                    <div data-dropdown-menu class="hidden absolute right-0 mt-2 w-52 bg-white rounded-card shadow-card py-2 z-50 border {{ $dropdownBorder }}">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-bold {{ $nameColor }}">{{ auth()->user()->nama }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->username }}</p>
                        </div>
                        <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 {{ $hoverBg }} transition-colors">
                            <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                            <span>Profil</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 w-full text-left px-4 py-2.5 text-sm text-coral hover:bg-coral/5 transition-colors">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>