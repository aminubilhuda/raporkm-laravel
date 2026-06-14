<?php

namespace App\Services\Dapodik;

use App\Models\KelompokMapel;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\Ptk;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Services\SekolahService;

class PembelajaranSyncService
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
            return ['success' => 0, 'failed' => 0, 'message' => 'Data rombel kosong.'];
        }

        [$taId, $semesterId] = $this->resolveActivePeriod();

        if (! $taId || ! $semesterId) {
            return ['success' => 0, 'failed' => 0, 'message' => 'Tahun pelajaran atau semester belum tersedia.'];
        }

        $kelompokMapelId = $this->ensureKelompokMapel();
        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($data as $item) {
            $item = (array) $item;
            $namaRombel = $item['nama'] ?? null;
            $pembelajaran = $item['pembelajaran'] ?? [];

            if (! $namaRombel || ! is_array($pembelajaran)) {
                continue;
            }

            $kelas = Kelas::where('nama_kelas', $namaRombel)
                ->where('tahun_pelajaran_id', $taId)
                ->first();

            if (! $kelas) {
                $failed++;
                $errors[] = "Kelas {$namaRombel} belum di-sync.";

                continue;
            }

            foreach ($pembelajaran as $pel) {
                $pel = (array) $pel;

                try {
                    $this->syncPembelajaran($pel, $kelas, $taId, $semesterId, $kelompokMapelId);
                    $success++;
                } catch (\Exception $e) {
                    $failed++;
                    $namaMapel = $pel['nama_mata_pelajaran'] ?? $pel['mata_pelajaran_id_str'] ?? 'unknown';
                    $errors[] = "{$namaMapel} di {$namaRombel}: {$e->getMessage()}";
                }
            }
        }

        return $this->formatResult($success, $failed, $errors);
    }

    private function resolveActivePeriod(): array
    {
        $taId = $this->sekolahService->getTahunAktif();
        $semesterId = $this->sekolahService->getSemesterAktif();

        if (! $taId) {
            $tp = TahunPelajaran::where('status', 1)->orderBy('id', 'desc')->first();
            $taId = $tp?->id;
        }
        if (! $semesterId) {
            $sem = Semester::where('status', 1)->orderBy('id', 'desc')->first();
            $semesterId = $sem?->id;
        }

        return [$taId, $semesterId];
    }

    private function ensureKelompokMapel(): int
    {
        $kelompokMapel = KelompokMapel::first();

        if (! $kelompokMapel) {
            $kelompokMapel = KelompokMapel::create([
                'nama' => 'Umum',
                'keterangan' => 'Default dari sync Dapodik',
            ]);
        }

        return $kelompokMapel->id;
    }

    private function syncPembelajaran(array $pel, Kelas $kelas, int $taId, int $semesterId, int $kelompokMapelId): void
    {
        $namaMapel = $pel['nama_mata_pelajaran'] ?? $pel['mata_pelajaran_id_str'] ?? null;
        $ptkId = $pel['ptk_id'] ?? null;
        $kodeMapel = isset($pel['mata_pelajaran_id']) ? (string) $pel['mata_pelajaran_id'] : null;

        if (! $namaMapel) {
            throw new \Exception('Nama mata pelajaran kosong');
        }

        $mapel = Mapel::firstOrCreate(
            ['nama_mapel' => $namaMapel],
            [
                'dapodik_id' => $kodeMapel,
                'kode' => $kodeMapel,
                'kelompok_mapel_id' => $kelompokMapelId,
                'kkm' => 75,
            ]
        );

        $guru = null;
        if ($ptkId) {
            $ptk = Ptk::where('ptk_id', $ptkId)->first();
            $guru = $ptk?->user;
        }

        MapelKelas::updateOrCreate(
            [
                'mapel_id' => $mapel->id,
                'kelas_id' => $kelas->id,
                'tahun_pelajaran_id' => $taId,
                'semester_id' => $semesterId,
            ],
            [
                'dapodik_id' => $pel['pembelajaran_id'] ?? null,
                'user_id' => $guru?->id,
                'kkm' => 75,
            ]
        );
    }

    private function formatResult(int $success, int $failed, array $errors): array
    {
        $msg = "{$success} berhasil, {$failed} gagal.";
        if (! empty($errors)) {
            $msg .= ' ' . implode('; ', array_slice($errors, 0, 5));
        }

        return ['success' => $success, 'failed' => $failed, 'message' => $msg];
    }
}
