<?php

namespace App\Services;

use App\Models\CatatanWali;
use App\Models\NilaiMapel;
use App\Models\NilaiPrakerin;
use App\Models\NilaiSumatifTs;
use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaEskul;
use App\Models\SiswaKelas;
use App\Models\SiswaPrakerin;
use App\Models\TahunPelajaran;

class RaporService
{
    public function getDataRaporSemester(int $siswaId, int $tahunId, int $semesterId): array
    {
        $siswa = Siswa::findOrFail($siswaId);

        $kelasAktif = SiswaKelas::where('siswa_id', $siswaId)
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->where('status', 'aktif')
            ->with('kelas.tingkat', 'kelas.kompetensiKeahlian')
            ->first();

        $nilaiMapel = NilaiMapel::with('mapel.kelompokMapel')
            ->where('siswa_id', $siswaId)
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->get()
            ->groupBy('mapel_id');

        $catatanWali = CatatanWali::where('siswa_id', $siswaId)
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->get();

        $ekskul = SiswaEskul::with('eskul')
            ->where('siswa_id', $siswaId)
            ->where('tahun_pelajaran_id', $tahunId)
            ->get();

        $presensi = $this->aggregatePresensi($siswaId, $tahunId, $semesterId);

        $pkl = SiswaPrakerin::with('prakerin')
            ->where('siswa_id', $siswaId)
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->get();

        return [
            'siswa' => $siswa,
            'sekolah' => Sekolah::first(),
            'kelas' => $kelasAktif?->kelas,
            'kelas_aktif' => $kelasAktif,
            'tahun' => TahunPelajaran::find($tahunId),
            'semester' => Semester::find($semesterId),
            'nilai_mapel' => $nilaiMapel,
            'catatan_wali' => $catatanWali,
            'ekskul' => $ekskul,
            'presensi' => $presensi,
            'pkl' => $pkl,
        ];
    }

    public function getDataRaporMid(int $siswaId, int $tahunId, int $semesterId): array
    {
        $siswa = Siswa::findOrFail($siswaId);

        $kelasAktif = SiswaKelas::where('siswa_id', $siswaId)
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->where('status', 'aktif')
            ->with('kelas.tingkat', 'kelas.kompetensiKeahlian')
            ->first();

        $nilaiSumatifTs = NilaiSumatifTs::with('mapel.kelompokMapel')
            ->where('siswa_id', $siswaId)
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->get();

        return [
            'siswa' => $siswa,
            'sekolah' => Sekolah::first(),
            'kelas' => $kelasAktif?->kelas,
            'kelas_aktif' => $kelasAktif,
            'tahun' => TahunPelajaran::find($tahunId),
            'semester' => Semester::find($semesterId),
            'nilai_sumatif_ts' => $nilaiSumatifTs,
        ];
    }

    public function getDataRaporPkl(int $siswaPrakerinId): array
    {
        $siswaPrakerin = SiswaPrakerin::with(['prakerin', 'siswa', 'kelas.tingkat', 'kelas.kompetensiKeahlian', 'user'])
            ->findOrFail($siswaPrakerinId);

        $nilaiPrakerin = NilaiPrakerin::with('mapel')
            ->where('siswa_prakerin_id', $siswaPrakerinId)
            ->get();

        return [
            'siswa_prakerin' => $siswaPrakerin,
            'siswa' => $siswaPrakerin->siswa,
            'prakerin' => $siswaPrakerin->prakerin,
            'kelas' => $siswaPrakerin->kelas,
            'sekolah' => Sekolah::first(),
            'nilai_prakerin' => $nilaiPrakerin,
        ];
    }

    public function getDataLagerNilai(int $kelasId, int $mapelId, int $tahunId, int $semesterId): array
    {
        $nilaiMapel = NilaiMapel::with('siswa')
            ->where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->get();

        $grid = [];
        foreach ($nilaiMapel as $nilai) {
            $grid[$nilai->siswa_id][$nilai->mapel_id] = $nilai;
        }

        $siswa = SiswaKelas::with('siswa')
            ->where('kelas_id', $kelasId)
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->where('status', 'aktif')
            ->get()
            ->pluck('siswa');

        return [
            'kelas_id' => $kelasId,
            'mapel_id' => $mapelId,
            'tahun' => TahunPelajaran::find($tahunId),
            'semester' => Semester::find($semesterId),
            'siswa' => $siswa,
            'grid' => $grid,
            'nilai_mapel' => $nilaiMapel,
        ];
    }

    private function aggregatePresensi(int $siswaId, int $tahunId, int $semesterId): array
    {
        $rows = Presensi::where('siswa_id', $siswaId)
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->get();

        $byJenis = $rows->groupBy('jenis_absen_id')->map->count();

        return [
            'total' => $rows->count(),
            'sakit' => $byJenis[2] ?? 0,
            'izin' => $byJenis[3] ?? 0,
            'alpha' => $byJenis[4] ?? 0,
        ];
    }
}
