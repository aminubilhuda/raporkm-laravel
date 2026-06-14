<?php

namespace App\Services\Dapodik;

use App\Models\Kelas;
use App\Models\KelasWali;
use App\Models\KompetensiKeahlian;
use App\Models\Ptk;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Services\SekolahService;
use Illuminate\Support\Str;

class RombelSyncService
{
    public function __construct(
        private DapodikClient $client,
        private SekolahService $sekolahService,
    ) {
    }

    public function sync(): array
    {
        $data = $this->client->get('getRombonganBelajar');

        if (empty($data)) {
            return ['success' => 0, 'failed' => 0, 'message' => 'Data kosong.'];
        }

        $tahunAktif = TahunPelajaran::where('status', 1)->first();
        $semesterAktif = Semester::where('status', 1)->first();

        [$tahunAktif, $semesterAktif] = $this->resolveActivePeriodFromData($data, $tahunAktif, $semesterAktif);

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($data as $item) {
            $item = (array) $item;

            try {
                $this->syncRombelRecord($item, $tahunAktif, $semesterAktif);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $namaRombel = $item['nama'] ?? 'unknown';
                $errors[] = "Rombel {$namaRombel}: {$e->getMessage()}";
            }
        }

        return $this->formatResult($success, $failed, $errors);
    }

    private function resolveActivePeriodFromData(array $data, ?TahunPelajaran $tahunAktif, ?Semester $semesterAktif): array
    {
        $first = $data[0] ?? null;
        if (! $first) {
            return [$tahunAktif, $semesterAktif];
        }

        $semesterId = $first['semester_id'] ?? null;
        if (! $semesterId) {
            return [$tahunAktif, $semesterAktif];
        }

        $tahunAwal = substr((string) $semesterId, 0, 4);
        $tahunAkhir = (int) $tahunAwal + 1;
        $semesterAngka = (int) substr((string) $semesterId, -1);

        if (strlen($tahunAwal) !== 4) {
            return [$tahunAktif, $semesterAktif];
        }

        $tahunLabel = "{$tahunAwal}/{$tahunAkhir}";
        $tahunAktif = TahunPelajaran::firstOrCreate(
            ['tahun' => $tahunLabel],
            ['status' => 1]
        );
        $tahunAktif->status = 1;
        $tahunAktif->save();

        $semesterAktif = Semester::firstOrCreate(
            ['urutan' => $semesterAngka],
            [
                'nama' => $semesterAngka === 1 ? 'Ganjil' : 'Genap',
                'status' => 1,
            ]
        );
        $semesterAktif->status = 1;
        $semesterAktif->save();

        $sekolah = $this->sekolahService->get();
        if ($sekolah) {
            $sekolah->tahun_aktif = $tahunAktif->id;
            $sekolah->semester_aktif = $semesterAktif->id;
            $sekolah->save();
        }

        return [$tahunAktif, $semesterAktif];
    }

    private function syncRombelRecord(array $item, ?TahunPelajaran $tahunAktif, ?Semester $semesterAktif): void
    {
        $namaRombel = $item['nama'] ?? null;

        if (! $namaRombel) {
            throw new \Exception('Nama rombel kosong');
        }

        $tingkat = $this->findOrCreateTingkat($item);
        $jurusan = $this->findOrCreateJurusan($item);

        $kelas = Kelas::firstOrCreate(
            [
                'tingkat_id' => $tingkat?->id,
                'kompetensi_keahlian_id' => $jurusan?->id,
                'nama_kelas' => $namaRombel,
            ],
            [
                'dapodik_id' => $item['rombongan_belajar_id'] ?? null,
                'tahun_pelajaran_id' => $tahunAktif?->id,
                'semester_id' => $semesterAktif?->id,
            ]
        );

        $this->syncWaliKelas($item, $kelas, $tahunAktif, $semesterAktif);
        $this->syncAnggotaRombel($item, $kelas, $jurusan, $tahunAktif, $semesterAktif);
    }

    private function findOrCreateTingkat(array $item): ?Tingkat
    {
        $tingkatRaw = $item['tingkat_pendidikan_id_str'] ?? '';
        $tingkatAngka = (int) filter_var($tingkatRaw, FILTER_SANITIZE_NUMBER_INT);

        if (! $tingkatAngka) {
            return null;
        }

        return Tingkat::firstOrCreate(
            ['angka' => $tingkatAngka],
            [
                'nama' => "Kelas {$tingkatAngka}",
                'fase' => match ($tingkatAngka) {
                    10 => 'E', 11 => 'F', 12 => 'F', default => 'E'
                },
                'urutan' => $tingkatAngka,
            ]
        );
    }

    private function findOrCreateJurusan(array $item): ?KompetensiKeahlian
    {
        $jurusanNama = $item['jurusan_id_str'] ?? null;

        if (! $jurusanNama) {
            return null;
        }

        return KompetensiKeahlian::firstOrCreate(
            ['nama' => $jurusanNama],
            ['singkatan' => strtoupper(Str::substr($jurusanNama, 0, 3))]
        );
    }

    private function syncWaliKelas(array $item, Kelas $kelas, ?TahunPelajaran $tahunAktif, ?Semester $semesterAktif): void
    {
        $ptkId = $item['ptk_id'] ?? null;
        if (! $ptkId || ! $tahunAktif?->id || ! $semesterAktif?->id) {
            return;
        }

        $wali = Ptk::where('ptk_id', $ptkId)->value('user_id');
        if ($wali) {
            KelasWali::updateOrCreate(
                [
                    'kelas_id' => $kelas->id,
                    'tahun_pelajaran_id' => $tahunAktif->id,
                    'semester_id' => $semesterAktif->id,
                ],
                ['user_id' => $wali]
            );
        }
    }

    private function syncAnggotaRombel(array $item, Kelas $kelas, ?KompetensiKeahlian $jurusan, ?TahunPelajaran $tahunAktif, ?Semester $semesterAktif): void
    {
        $anggota = $item['anggota_rombel'] ?? [];
        if (! is_array($anggota) || ! $tahunAktif || ! $semesterAktif) {
            return;
        }

        foreach ($anggota as $a) {
            $a = (array) $a;
            $pdId = $a['peserta_didik_id'] ?? null;
            if (! $pdId) {
                continue;
            }

            $siswa = Siswa::where('dapodik_pd_id', $pdId)->first();
            if (! $siswa) {
                continue;
            }

            SiswaKelas::firstOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $kelas->id,
                    'tahun_pelajaran_id' => $tahunAktif->id,
                    'semester_id' => $semesterAktif->id,
                ],
                ['status' => 'aktif']
            );

            if ($jurusan) {
                $siswa->update(['jurusan' => $jurusan->id]);
            }
        }
    }

    private function formatResult(int $success, int $failed, array $errors): array
    {
        $msg = "{$success} rombel berhasil, {$failed} gagal.";
        if (! empty($errors)) {
            $msg .= ' ' . implode('; ', array_slice($errors, 0, 5));
        }

        return ['success' => $success, 'failed' => $failed, 'message' => $msg];
    }
}
