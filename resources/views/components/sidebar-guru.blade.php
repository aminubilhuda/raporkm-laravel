<div class="flex flex-col h-full">
    <div class="p-4 border-b border-white/20 flex items-center justify-between">
        <div>
            <h1 class="text-lg font-extrabold tracking-wide">E-Rapor KM</h1>
            <p class="text-xs text-coral-light mt-0.5 tracking-widest uppercase font-bold opacity-80">Panel Guru</p>
        </div>
        <button data-close-sidebar class="md:hidden p-1 rounded-lg hover:bg-white/10 transition-colors" aria-label="Close menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto no-scrollbar py-4">
        <div class="px-4 mb-3">
            <span class="sidebar-section-title border-coral-light/30 text-coral-light/70">
                <x-heroicon-o-home class="w-4 h-4 inline-block mr-1" />
                Utama
            </span>
        </div>

        @foreach([
            ['route' => 'guru.dashboard', 'icon' => 'o-chart-bar', 'label' => 'Dashboard'],
            ['route' => 'guru.kelas-ku.index', 'icon' => 'o-building-office-2', 'label' => 'Kelas Saya'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active-coral' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="px-4 mt-5 mb-3">
            <span class="sidebar-section-title border-coral-light/30 text-coral-light/70">
                <x-heroicon-o-pencil class="w-4 h-4 inline-block mr-1" />
                Penilaian
            </span>
        </div>

        @foreach([
            ['route' => 'guru.tujuan-pembelajaran.index', 'icon' => 'o-check-badge', 'label' => 'Tujuan Pembelajaran'],
            ['route' => 'guru.penilaian.index', 'icon' => 'o-pencil-square', 'label' => 'Penilaian'],
            ['route' => 'guru.lager-nilai-kelas.index', 'icon' => 'o-document-chart-bar', 'label' => 'Lager Nilai'],
            ['route' => 'guru.catatan-rapor.index', 'icon' => 'o-clipboard-document-list', 'label' => 'Catatan Rapor'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active-coral' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="px-4 mt-5 mb-3">
            <span class="sidebar-section-title border-coral-light/30 text-coral-light/70">
                <x-heroicon-o-star class="w-4 h-4 inline-block mr-1" />
                P5 &amp; Lainnya
            </span>
        </div>

        @foreach([
            ['route' => 'guru.project-kelas.index', 'icon' => 'o-sparkles', 'label' => 'Project Kelas'],
            ['route' => 'guru.p5bk.index', 'icon' => 'o-star', 'label' => 'P5BK'],
            ['route' => 'guru.kokurikuler.index', 'icon' => 'o-sparkles', 'label' => 'Kokurikuler'],
            ['route' => 'guru.penilaian-kokurikuler.index', 'icon' => 'o-pencil-square', 'label' => 'Nilai Kokurikuler'],
            ['route' => 'guru.ekstra.index', 'icon' => 'o-trophy', 'label' => 'Ekstrakurikuler'],
            ['route' => 'guru.presensi.rekap', 'icon' => 'o-clipboard-document-list', 'label' => 'Presensi'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active-coral' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-white/20">
        <p class="text-xs text-coral-light/50 text-center">E-Rapor KM v1.0</p>
    </div>
</div>