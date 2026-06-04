<?php

namespace App\Services;

use App\Models\DeskripsiRapor;
use App\Models\NilaiFormatif;
use App\Models\NilaiMapel;
use App\Models\NilaiSumatifAs;
use App\Models\NilaiSumatifPh;
use App\Models\TujuanPembelajaran;

class NilaiService
{
    public function hitungNilaiAkhirMapel(int $siswaId, int $kelasId, int $mapelId, int $tahun, int $semester): array
    {
        $tpIds = TujuanPembelajaran::where('mapel_id', $mapelId)
            ->where('kelas_id', $kelasId)
            ->where('tahun_pelajaran_id', $tahun)
            ->where('semester_id', $semester)
            ->pluck('id');

        $rataFormatif = NilaiFormatif::where('siswa_id', $siswaId)
            ->whereIn('tujuan_pembelajaran_id', $tpIds)
            ->avg('nilai') ?? 0;

        $rataPh = NilaiSumatifPh::where('siswa_id', $siswaId)
            ->whereIn('tujuan_pembelajaran_id', $tpIds)
            ->avg('nilai') ?? 0;

        $sumatifAs = NilaiSumatifAs::where('siswa_id', $siswaId)
            ->where('mapel_id', $mapelId)
            ->where('kelas_id', $kelasId)
            ->where('tahun_pelajaran_id', $tahun)
            ->where('semester_id', $semester)
            ->value('nilai') ?? 0;

        $nilaiAkhir = round(($rataFormatif * 0.4) + ($rataPh * 0.3) + ($sumatifAs * 0.3), 0);

        return compact('rataFormatif', 'rataPh', 'sumatifAs', 'nilaiAkhir');
    }

    public function getPredikat(int $nilai): string
    {
        return match (true) {
            $nilai >= 90 => 'SB',
            $nilai >= 75 => 'B',
            $nilai >= 60 => 'C',
            default => 'PB',
        };
    }

    public function generateDeskripsi(int $nilai, int $kktp): string
    {
        $deskripsi = DeskripsiRapor::where('predikat', $this->getPredikat($nilai))->first();

        return $deskripsi?->deskripsi ?? '-';
    }

    public function simpanNilaiAkhir(int $siswaId, int $kelasId, int $mapelId, int $tahun, int $semester, ?int $kktp = null): NilaiMapel
    {
        $hasil = $this->hitungNilaiAkhirMapel($siswaId, $kelasId, $mapelId, $tahun, $semester);
        $predikat = $this->getPredikat($hasil['nilaiAkhir']);
        $deskripsi = $this->generateDeskripsi($hasil['nilaiAkhir'], $kktp ?? 75);

        return NilaiMapel::updateOrCreate(
            [
                'siswa_id' => $siswaId,
                'kelas_id' => $kelasId,
                'mapel_id' => $mapelId,
                'tahun_pelajaran_id' => $tahun,
                'semester_id' => $semester,
            ],
            [
                'nilai' => $hasil['nilaiAkhir'],
                'kktp' => $kktp ?? 75,
                'predikat' => $predikat,
                'deskripsi' => $deskripsi,
            ]
        );
    }
}
