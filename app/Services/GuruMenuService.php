<?php

namespace App\Services;

use App\Models\GuruMenuAkses;
use App\Models\Sekolah;
use App\Models\SiswaPrakerin;
use App\Models\User;

class GuruMenuService
{
    public const MENU_SLUGS = [
        'dashboard',
        'kelas-ku',
        'tujuan-pembelajaran',
        'penilaian',
        'lager-nilai',
        'catatan-rapor',
        'cetak-rapor',
        'project-kelas',
        'p5bk',
        'kokurikuler',
        'penilaian-kokurikuler',
        'ekstra',
        'presensi',
        'rekap-presensi',
        'absensi-bk',
        'absensi-guru',
        'prakerin',
        'nilai-prakerin',
        'rapor-pkl',
        'piket-harian',
        'organisasi',
    ];

    private const ALWAYS_VISIBLE = [
        'dashboard',
        'kelas-ku',
        'prakerin',
        'piket-harian',
        'organisasi',
        'absensi-guru',
    ];

    private const MAPEL_KELAS_MENUS = [
        'tujuan-pembelajaran',
        'penilaian',
        'lager-nilai',
    ];

    private const WALI_KELAS_MENUS = [
        'catatan-rapor',
        'cetak-rapor',
        'project-kelas',
        'p5bk',
        'kokurikuler',
        'penilaian-kokurikuler',
    ];

    private const PEMBINA_ESKUL_MENUS = [
        'ekstra',
    ];

    private const PIKET_MENUS = [
        'presensi',
        'rekap-presensi',
        'absensi-bk',
    ];

    private const PEMBIMBING_PKL_MENUS = [
        'nilai-prakerin',
        'rapor-pkl',
    ];

    public function getVisibleMenus(User $user, ?int $taId = null, ?int $semesterId = null): array
    {
        if ($taId === null || $semesterId === null) {
            $sekolah = Sekolah::first();
            $taId = $taId ?? $sekolah?->tahun_aktif;
            $semesterId = $semesterId ?? $sekolah?->semester_aktif;
        }

        // Kepsek: akses semua menu
        if ($user->isKepsek()) {
            return self::MENU_SLUGS;
        }

        // Load overrides
        $overrides = GuruMenuAkses::where('user_id', $user->id)
            ->get()
            ->keyBy('menu_slug');

        // Auto-detect flags
        $hasMapelKelas = $user->mapelKelas()
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->exists();

        $isWaliKelas = $user->kelasWali()
            ->when($taId, fn ($q) => $q->where('kelas_wali.tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('kelas_wali.semester_id', $semesterId))
            ->exists();

        $isPembinaEskul = $user->pembinaEskul()
            ->when($taId, fn ($q) => $q->where('pembina_eskul.tahun_pelajaran_id', $taId))
            ->exists();

        $isPiket = $user->piketHarian()
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->exists();

        $isPembimbingPkl = SiswaPrakerin::where('user_id', $user->id)
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->exists();

        $visible = [];

        foreach (self::MENU_SLUGS as $slug) {
            // 1. Check override first
            if ($overrides->has($slug)) {
                $tipe = $overrides[$slug]->tipe;
                if ($tipe === 'revoke') {
                    continue; // Force hidden
                }
                // 'grant' → force visible
                $visible[] = $slug;

                continue;
            }

            // 2. Always visible
            if (in_array($slug, self::ALWAYS_VISIBLE, true)) {
                $visible[] = $slug;

                continue;
            }

            // 3. Auto-detect by category
            if (in_array($slug, self::MAPEL_KELAS_MENUS, true) && $hasMapelKelas) {
                $visible[] = $slug;

                continue;
            }

            if (in_array($slug, self::WALI_KELAS_MENUS, true) && $isWaliKelas) {
                $visible[] = $slug;

                continue;
            }

            if (in_array($slug, self::PEMBINA_ESKUL_MENUS, true) && $isPembinaEskul) {
                $visible[] = $slug;

                continue;
            }

            if (in_array($slug, self::PIKET_MENUS, true) && $isPiket) {
                $visible[] = $slug;

                continue;
            }

            if (in_array($slug, self::PEMBIMBING_PKL_MENUS, true) && $isPembimbingPkl) {
                $visible[] = $slug;

                continue;
            }
        }

        return $visible;
    }
}
