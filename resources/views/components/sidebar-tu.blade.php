<div class="flex flex-col h-full">
    <div class="p-4 border-b border-white/20 flex items-center justify-between">
        <div class="flex items-center gap-3">
            @if($sekolah?->logo)
                <img src="{{ asset('storage/'.$sekolah->logo) }}" alt="Logo" class="h-10 w-auto">
            @endif
            <div>
                <h1 class="text-lg font-extrabold tracking-wide">{{ $sekolah?->nama_sekolah ?? 'E-Rapor KM' }}</h1>
                <p class="text-xs text-teal-light mt-0.5 tracking-widest uppercase font-bold opacity-80">Panel Tata Usaha</p>
            </div>
        </div>
        <button data-close-sidebar class="md:hidden p-1 rounded-lg hover:bg-white/10 transition-colors" aria-label="Close menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto no-scrollbar py-4">
        <div class="px-4 mb-3">
            <span class="sidebar-section-title border-teal-light/30 text-teal-light/70">
                <x-heroicon-o-squares-2x2 class="w-4 h-4 inline-block mr-1" />
                Utama
            </span>
        </div>

        @foreach([
            ['route' => 'tu.dashboard', 'icon' => 'o-chart-bar', 'label' => 'Dashboard'],
            ['route' => 'tu.sekolah.index', 'icon' => 'o-building-office-2', 'label' => 'Profil Sekolah'],
            ['route' => 'tu.pengaturan.index', 'icon' => 'o-cog-6-tooth', 'label' => 'Pengaturan'],
            ['route' => 'tu.pegawai.index', 'icon' => 'o-users', 'label' => 'Pegawai/Guru'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="px-4 mt-5 mb-3">
            <span class="sidebar-section-title border-teal-light/30 text-teal-light/70">
                <x-heroicon-o-academic-cap class="w-4 h-4 inline-block mr-1" />
                Master Akademik
            </span>
        </div>

        @foreach([
            ['route' => 'tu.mapel.index', 'icon' => 'o-book-open', 'label' => 'Mata Pelajaran'],
            ['route' => 'tu.kelompok-mapel.index', 'icon' => 'o-rectangle-stack', 'label' => 'Kelompok Mapel'],
            ['route' => 'tu.kompetensi.index', 'icon' => 'o-wrench-screwdriver', 'label' => 'Kompetensi Keahlian'],
            ['route' => 'tu.tingkat.index', 'icon' => 'o-numbered-list', 'label' => 'Tingkat'],
            ['route' => 'tu.deskripsi-rapor.index', 'icon' => 'o-pencil-square', 'label' => 'Deskripsi Rapor'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="px-4 mt-5 mb-3">
            <span class="sidebar-section-title border-teal-light/30 text-teal-light/70">
                <x-heroicon-o-user-group class="w-4 h-4 inline-block mr-1" />
                Kesiswaan
            </span>
        </div>

        @foreach([
            ['route' => 'tu.kesiswaan.index', 'icon' => 'o-academic-cap', 'label' => 'Data Siswa'],
            ['route' => 'tu.rombel.index', 'icon' => 'o-building-office', 'label' => 'Kelas/Rombel'],
            ['route' => 'tu.anggota-kelas.index', 'icon' => 'o-user-group', 'label' => 'Anggota Kelas'],
            ['route' => 'tu.mapel-kelas.index', 'icon' => 'o-squares-plus', 'label' => 'Mapel-Kelas'],
            ['route' => 'tu.mapel-siswa.index', 'icon' => 'o-user-plus', 'label' => 'Mapel-Siswa'],
            ['route' => 'tu.naik-kelas.index', 'icon' => 'o-arrow-up-on-square', 'label' => 'Naik Kelas'],
            ['route' => 'tu.mutasi-masuk.index', 'icon' => 'o-arrow-left-on-rectangle', 'label' => 'Mutasi Masuk'],
            ['route' => 'tu.mutasi-keluar.index', 'icon' => 'o-arrow-right-on-rectangle', 'label' => 'Mutasi Keluar'],
            ['route' => 'tu.lulusan.index', 'icon' => 'o-academic-cap', 'label' => 'Lulusan'],
            ['route' => 'tu.prestasi.index', 'icon' => 'o-trophy', 'label' => 'Prestasi'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="px-4 mt-5 mb-3">
            <span class="sidebar-section-title border-teal-light/30 text-teal-light/70">
                <x-heroicon-o-star class="w-4 h-4 inline-block mr-1" />
                P5BK
            </span>
        </div>

        @foreach([
            ['route' => 'tu.p5bk.index', 'icon' => 'o-star', 'label' => 'P5BK Dimensi'],
            ['route' => 'tu.p5bk.tema.index', 'icon' => 'o-sparkles', 'label' => 'P5BK Tema'],
            ['route' => 'tu.p5bk.proyek.index', 'icon' => 'o-rocket-launch', 'label' => 'P5BK Proyek'],
            ['route' => 'tu.kokurikuler.index', 'icon' => 'o-bookmark', 'label' => 'Kokurikuler'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="px-4 mt-5 mb-3">
            <span class="sidebar-section-title border-teal-light/30 text-teal-light/70">
                <x-heroicon-o-trophy class="w-4 h-4 inline-block mr-1" />
                Ekstrakurikuler &amp; PKL
            </span>
        </div>

        @foreach([
            ['route' => 'tu.ekstra.index', 'icon' => 'o-trophy', 'label' => 'Ekstrakurikuler'],
            ['route' => 'tu.prakerin.index', 'icon' => 'o-building-office-2', 'label' => 'Prakerin'],
            ['route' => 'tu.piket-harian.index', 'icon' => 'o-calendar-days', 'label' => 'Piket Harian'],
            ['route' => 'tu.organisasi.index', 'icon' => 'o-flag', 'label' => 'Organisasi'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="px-4 mt-5 mb-3">
            <span class="sidebar-section-title border-teal-light/30 text-teal-light/70">
                <x-heroicon-o-chart-bar-square class="w-4 h-4 inline-block mr-1" />
                Laporan &amp; Ekspor
            </span>
        </div>

        @foreach([
            ['route' => 'tu.presensi.rekap', 'icon' => 'o-table-cells', 'label' => 'Rekap Presensi'],
            ['route' => 'tu.pengingat.index', 'icon' => 'o-bell-alert', 'label' => 'Pengingat'],
            ['route' => 'tu.rapor.pilih', 'icon' => 'o-document-text', 'label' => 'Cetak Rapor'],
            ['route' => 'tu.laporan.pendidikan', 'icon' => 'o-document-chart-bar', 'label' => 'Laporan Pendidikan'],
            ['route' => 'tu.ekspor.index', 'icon' => 'o-arrow-down-tray', 'label' => 'Ekspor Data'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="px-4 mt-5 mb-3">
            <span class="sidebar-section-title border-teal-light/30 text-teal-light/70">
                <x-heroicon-o-cloud-arrow-down class="w-4 h-4 inline-block mr-1" />
                Integrasi
            </span>
        </div>

        @foreach([
            ['route' => 'tu.dapodik.index', 'icon' => 'o-cloud-arrow-down', 'label' => 'Dapodik'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active' : 'hover:bg-white/10' }}">
                <x-dynamic-component :component="'heroicon-'.$item['icon']" class="w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-white/20">
        <p class="text-xs text-teal-light/50 text-center">E-Rapor KM v1.0</p>
    </div>
</div>
