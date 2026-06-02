<?php

namespace App\Observers;

use App\Models\NilaiSumatifAs;
use App\Services\NilaiService;

class NilaiSumatifAsObserver
{
    public function __construct(private readonly NilaiService $nilaiService) {}

    public function created(NilaiSumatifAs $nilaiSumatifAs): void
    {
        $this->recalculate($nilaiSumatifAs);
    }

    public function updated(NilaiSumatifAs $nilaiSumatifAs): void
    {
        $this->recalculate($nilaiSumatifAs);
    }

    private function recalculate(NilaiSumatifAs $nilaiSumatifAs): void
    {
        if (! $nilaiSumatifAs->tahun_pelajaran_id || ! $nilaiSumatifAs->semester_id) {
            return;
        }

        $this->nilaiService->simpanNilaiAkhir(
            (int) $nilaiSumatifAs->siswa_id,
            (int) $nilaiSumatifAs->kelas_id,
            (int) $nilaiSumatifAs->mapel_id,
            (int) $nilaiSumatifAs->tahun_pelajaran_id,
            (int) $nilaiSumatifAs->semester_id,
        );
    }
}
