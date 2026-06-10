<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\NilaiMapel;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use Illuminate\Support\Facades\DB;

class LaporanPendidikanService
{
    public function aggregate(int $tahunId, int $semesterId): array
    {
        $kelasAktif = SiswaKelas::query()
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->where('status', 'aktif')
            ->distinct()
            ->pluck('kelas_id');

        $kelasList = Kelas::with('tingkat', 'kompetensiKeahlian')
            ->whereIn('id', $kelasAktif)
            ->orderBy('nama_kelas')
            ->get();

        $mapelList = Mapel::orderBy('urutan')->get();

        $nilaiPerMapel = $this->rataRataPerMapel($tahunId, $semesterId);

        $distribusi = $this->distribusiPredikat($tahunId, $semesterId);

        $topBottom = $this->topBottomSiswa($tahunId, $semesterId, 10);

        $presensiRekap = $this->presensiRekap($tahunId, $semesterId);

        return [
            'kelas_list' => $kelasList,
            'mapel_list' => $mapelList,
            'nilai_per_mapel' => $nilaiPerMapel,
            'distribusi_predikat' => $distribusi,
            'top_bottom' => $topBottom,
            'presensi_rekap' => $presensiRekap,
        ];
    }

    private function rataRataPerMapel(int $tahunId, int $semesterId): array
    {
        $rows = NilaiMapel::query()
            ->select('mapel_id', DB::raw('AVG(nilai) as rata_rata'), DB::raw('MIN(nilai) as min_nilai'), DB::raw('MAX(nilai) as max_nilai'), DB::raw('COUNT(*) as jumlah'))
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->groupBy('mapel_id')
            ->get()
            ->keyBy('mapel_id');

        return Mapel::orderBy('urutan')->get()->map(function (Mapel $m) use ($rows) {
            $stat = $rows->get($m->id);

            return [
                'mapel' => $m,
                'rata_rata' => $stat ? round((float) $stat->rata_rata, 2) : null,
                'min' => $stat?->min_nilai,
                'max' => $stat?->max_nilai,
                'jumlah' => $stat?->jumlah ?? 0,
            ];
        })->all();
    }

    private function distribusiPredikat(int $tahunId, int $semesterId): array
    {
        $rows = NilaiMapel::query()
            ->select('predikat', DB::raw('COUNT(*) as jumlah'))
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->whereNotNull('predikat')
            ->groupBy('predikat')
            ->pluck('jumlah', 'predikat');

        $total = array_sum($rows->toArray()) ?: 1;

        return collect(['A', 'B', 'C', 'D'])->map(function (string $p) use ($rows, $total) {
            $jumlah = $rows[$p] ?? 0;

            return [
                'predikat' => $p,
                'jumlah' => $jumlah,
                'persen' => round(($jumlah / $total) * 100, 1),
            ];
        })->all();
    }

    private function topBottomSiswa(int $tahunId, int $semesterId, int $limit): array
    {
        $avgPerSiswa = NilaiMapel::query()
            ->select('siswa_id', DB::raw('AVG(nilai) as rata_rata'))
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->groupBy('siswa_id')
            ->get()
            ->keyBy('siswa_id');

        $top = $avgPerSiswa->sortByDesc('rata_rata')->take($limit);
        $bottom = $avgPerSiswa->sortBy('rata_rata')->take($limit);

        $siswaMap = Siswa::whereIn('id', $avgPerSiswa->keys())->get()->keyBy('id');

        return [
            'top' => $top->map(fn ($row) => [
                'siswa' => $siswaMap[$row->siswa_id] ?? null,
                'rata_rata' => round((float) $row->rata_rata, 2),
            ])->values()->all(),
            'bottom' => $bottom->map(fn ($row) => [
                'siswa' => $siswaMap[$row->siswa_id] ?? null,
                'rata_rata' => round((float) $row->rata_rata, 2),
            ])->values()->all(),
        ];
    }

    private function presensiRekap(int $tahunId, int $semesterId): array
    {
        $rows = Presensi::query()
            ->select('jenis_absen_id', DB::raw('COUNT(*) as jumlah'))
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->groupBy('jenis_absen_id')
            ->pluck('jumlah', 'jenis_absen_id');

        return [
            'sakit' => $rows[2] ?? 0,
            'izin' => $rows[3] ?? 0,
            'alpha' => $rows[4] ?? 0,
            'hadir' => $rows[1] ?? 0,
        ];
    }
}
