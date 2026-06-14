<?php

namespace App\Services\Dapodik;

use App\Models\Siswa;

class SiswaSyncService
{
    public function __construct(private DapodikClient $client)
    {
    }

    public function sync(): array
    {
        $data = $this->client->get('getPesertaDidik');

        if (empty($data)) {
            return ['success' => 0, 'failed' => 0, 'message' => 'Data kosong.'];
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($data as $item) {
            $item = (array) $item;
            $nisn = $item['nisn'] ?? null;

            if (! $nisn) {
                $failed++;

                continue;
            }

            try {
                $this->syncSiswaRecord($item, $nisn);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "NISN {$nisn}: {$e->getMessage()}";
            }
        }

        return $this->formatResult($success, $failed, $errors);
    }

    private function syncSiswaRecord(array $item, string $nisn): void
    {
        $kelamin = $this->mapKelamin($item['jenis_kelamin'] ?? '');

        Siswa::updateOrCreate(
            ['nisn' => $nisn],
            [
                'nis' => $item['nis'] ?? $item['nisn'] ?? $nisn,
                'nama_siswa' => $item['nama'] ?? $item['nama_siswa'] ?? '-',
                'nik_pd' => $item['nik'] ?? $item['nik_pd'] ?? null,
                'nkk' => $item['nkk'] ?? null,
                'dapodik_pd_id' => $item['peserta_didik_id'] ?? null,
                'tempat_lahir' => $item['tempat_lahir'] ?? null,
                'tanggal_lahir' => isset($item['tanggal_lahir']) ? date('Y-m-d', strtotime($item['tanggal_lahir'])) : null,
                'kelamin' => $kelamin,
                'agama' => isset($item['agama_id']) ? (int) $item['agama_id'] : null,
                'alamat' => $item['alamat_jalan'] ?? $item['alamat'] ?? null,
                'kontak_siswa' => $item['nomor_telepon_seluler'] ?? $item['kontak_siswa'] ?? null,
                'nama_ayah' => $item['nama_ayah'] ?? null,
                'pekerjaan_ayah' => $item['pekerjaan_ayah_id_str'] ?? null,
                'nama_ibu' => $item['nama_ibu'] ?? null,
                'pekerjaan_ibu' => $item['pekerjaan_ibu_id_str'] ?? null,
                'nik_ayah' => $item['nik_ayah'] ?? null,
                'nik_ibu' => $item['nik_ibu'] ?? null,
                'anak_ke' => isset($item['anak_keberapa']) ? (int) $item['anak_keberapa'] : null,
                'sekolah_asal' => $item['sekolah_asal'] ?? null,
                'aktif' => 1,
            ]
        );
    }

    private function mapKelamin(string $value): ?int
    {
        return match (strtoupper($value)) {
            'L', 'LAKI-LAKI' => 1,
            'P', 'PEREMPUAN' => 2,
            default => null,
        };
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
