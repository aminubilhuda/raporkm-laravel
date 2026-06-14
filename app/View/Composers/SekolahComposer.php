<?php

namespace App\View\Composers;

use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Services\GuruMenuService;
use App\Services\SekolahService;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SekolahComposer
{
    public function compose(View $view): void
    {
        if (Schema::hasTable('sekolah')) {
            $sekolah = Sekolah::first();
        } else {
            $sekolah = null;
        }

        $tpList = collect();
        $semesterList = collect();
        $semesterOptions = [];
        $activeTpId = null;
        $activeSemesterId = null;

        if (Schema::hasTable('tahun_pelajaran') && Schema::hasTable('semester')) {
            $tpList = TahunPelajaran::orderByDesc('status')->orderByDesc('tahun')->get();
            $semesterList = Semester::orderBy('urutan')->get();

            $activeTpId = (int) session('selected_tahun', $sekolah?->tahun_aktif);
            $activeSemesterId = (int) session('selected_semester', $sekolah?->semester_aktif);

            foreach ($tpList as $tp) {
                foreach ($semesterList as $sem) {
                    $label = 'Semester '.$sem->urutan.' ('.$tp->tahun.')';
                    if ($tp->status && $sem->status) {
                        $label .= ' *(Aktif)';
                    }
                    $semesterOptions[] = [
                        'label' => $label,
                        'tahun_id' => $tp->id,
                        'semester_id' => $sem->id,
                    ];
                }
            }
        }

        $view->with([
            'sekolah' => $sekolah,
            'tpList' => $tpList,
            'semesterList' => $semesterList,
            'semesterOptions' => $semesterOptions,
            'activeTpId' => $activeTpId,
            'activeSemesterId' => $activeSemesterId,
            'guruMenus' => $this->getGuruMenus($activeTpId, $activeSemesterId),
        ]);
    }

    private function getGuruMenus(?int $taId, ?int $semesterId): array
    {
        if (! Schema::hasTable('guru_menu_akses')) {
            return GuruMenuService::MENU_SLUGS;
        }

        $user = auth()->user();
        if (! $user || ! $user->isGuru() && ! $user->isKepsek()) {
            return [];
        }

        return app(GuruMenuService::class)->getVisibleMenus($user, $taId, $semesterId);
    }
}
