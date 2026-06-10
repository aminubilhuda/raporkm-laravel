<div class="flex flex-col h-full">
    <div class="p-4 border-b border-white/20 flex items-center justify-between">
        <div class="flex items-center gap-3">
            @if($sekolah?->logo)
                <img src="{{ asset('storage/'.$sekolah->logo) }}" alt="Logo" class="h-10 w-auto">
            @endif
            <div>
                <h1 class="text-lg font-extrabold tracking-wide">{{ $sekolah?->nama_sekolah ?? 'E-Rapor KM' }}</h1>
                <p class="text-xs text-coral-light mt-0.5 tracking-widest uppercase font-bold opacity-80">Panel Guru</p>
            </div>
        </div>
        <button data-close-sidebar class="md:hidden p-1 rounded-lg hover:bg-white/10 transition-colors" aria-label="Close menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <x-semester-switcher route="guru.set-semester" />

    <nav class="flex-1 overflow-y-auto no-scrollbar py-4">
        <div class="px-4 mb-3">
            <span class="sidebar-section-title border-coral-light/30 text-coral-light/70">
                <x-heroicon-o-home class="w-4 h-4 inline-block mr-1" />
                Utama
            </span>
        </div>

        @foreach([
            ['route' => 'guru.dashboard', 'slug' => 'dashboard', 'icon' => 'o-chart-bar', 'label' => 'Dashboard'],
            ['route' => 'guru.kelas-ku.index', 'slug' => 'kelas-ku', 'icon' => 'o-building-office-2', 'label' => 'Kelas Saya'],
        ] as $item)
            @if(in_array($item['slug'], $guruMenus ?? []))
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active-coral' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
            @endif
        @endforeach

        @php
            $penilaianMenus = [
                ['route' => 'guru.tujuan-pembelajaran.index', 'slug' => 'tujuan-pembelajaran', 'icon' => 'o-check-badge', 'label' => 'Tujuan Pembelajaran'],
                ['route' => 'guru.penilaian.index', 'slug' => 'penilaian', 'icon' => 'o-pencil-square', 'label' => 'Penilaian'],
                ['route' => 'guru.lager-nilai-kelas.index', 'slug' => 'lager-nilai', 'icon' => 'o-document-chart-bar', 'label' => 'Lager Nilai'],
                ['route' => 'guru.catatan-rapor.index', 'slug' => 'catatan-rapor', 'icon' => 'o-clipboard-document-list', 'label' => 'Catatan Rapor'],
                ['route' => 'guru.cetak-rapor.index', 'slug' => 'cetak-rapor', 'icon' => 'o-printer', 'label' => 'Cetak Rapor'],
            ];
            $visiblePenilaian = collect($penilaianMenus)->filter(fn ($m) => in_array($m['slug'], $guruMenus ?? []));
        @endphp

        @if($visiblePenilaian->isNotEmpty())
        <div class="px-4 mt-5 mb-3">
            <span class="sidebar-section-title border-coral-light/30 text-coral-light/70">
                <x-heroicon-o-pencil class="w-4 h-4 inline-block mr-1" />
                Penilaian
            </span>
        </div>

        @foreach($visiblePenilaian as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active-coral' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
        @endif

        @php
            $p5Menus = [
                ['route' => 'guru.project-kelas.index', 'slug' => 'project-kelas', 'icon' => 'o-sparkles', 'label' => 'Project Kelas'],
                ['route' => 'guru.p5bk.index', 'slug' => 'p5bk', 'icon' => 'o-star', 'label' => 'P5BK'],
                ['route' => 'guru.kokurikuler.index', 'slug' => 'kokurikuler', 'icon' => 'o-sparkles', 'label' => 'Kokurikuler'],
                ['route' => 'guru.penilaian-kokurikuler.index', 'slug' => 'penilaian-kokurikuler', 'icon' => 'o-pencil-square', 'label' => 'Nilai Kokurikuler'],
                ['route' => 'guru.ekstra.index', 'slug' => 'ekstra', 'icon' => 'o-trophy', 'label' => 'Ekstrakurikuler'],
                ['route' => 'guru.presensi.index', 'slug' => 'presensi', 'icon' => 'o-clipboard-document-check', 'label' => 'Presensi'],
                ['route' => 'guru.presensi.rekap', 'slug' => 'rekap-presensi', 'icon' => 'o-clipboard-document-list', 'label' => 'Rekap Presensi'],
                ['route' => 'guru.absensi-bk.index', 'slug' => 'absensi-bk', 'icon' => 'o-clipboard-document', 'label' => 'Absensi BK'],
            ];
            $visibleP5 = collect($p5Menus)->filter(fn ($m) => in_array($m['slug'], $guruMenus ?? []));
        @endphp

        @if($visibleP5->isNotEmpty())
        <div class="px-4 mt-5 mb-3">
            <span class="sidebar-section-title border-coral-light/30 text-coral-light/70">
                <x-heroicon-o-star class="w-4 h-4 inline-block mr-1" />
                P5 &amp; Lainnya
            </span>
        </div>

        @foreach($visibleP5 as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active-coral' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
        @endif

        @php
            $lainnyaMenus = [
                ['route' => 'guru.prakerin.index', 'slug' => 'prakerin', 'icon' => 'o-building-office', 'label' => 'Prakerin'],
                ['route' => 'guru.nilai-prakerin.index', 'slug' => 'nilai-prakerin', 'icon' => 'o-pencil-square', 'label' => 'Nilai Prakerin'],
                ['route' => 'guru.rapor-pkl.index', 'slug' => 'rapor-pkl', 'icon' => 'o-document-text', 'label' => 'Rapor PKL'],
                ['route' => 'guru.piket-harian.index', 'slug' => 'piket-harian', 'icon' => 'o-calendar-days', 'label' => 'Piket Harian'],
                ['route' => 'guru.organisasi.index', 'slug' => 'organisasi', 'icon' => 'o-user-group', 'label' => 'Organisasi'],
                ['route' => 'guru.absensi-guru.index', 'slug' => 'absensi-guru', 'icon' => 'o-map-pin', 'label' => 'Absensi GPS'],
            ];
            $visibleLainnya = collect($lainnyaMenus)->filter(fn ($m) => in_array($m['slug'], $guruMenus ?? []));
        @endphp

        @if($visibleLainnya->isNotEmpty())
        <div class="px-4 mt-5 mb-3">
            <span class="sidebar-section-title border-coral-light/30 text-coral-light/70">
                <x-heroicon-o-building-office class="w-4 h-4 inline-block mr-1" />
                Lainnya
            </span>
        </div>

        @foreach($visibleLainnya as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active-coral' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
        @endif
    </nav>

    <div class="p-4 border-t border-white/20">
        <p class="text-xs text-coral-light/50 text-center">E-Rapor KM v1.0</p>
    </div>
</div>
