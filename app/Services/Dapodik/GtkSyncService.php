<?php

namespace App\Services\Dapodik;

use App\Models\Ptk;
use App\Models\User;

class GtkSyncService
{
    public function __construct(private DapodikClient $client)
    {
    }

    public function sync(): array
    {
        $data = $this->client->get('getGtk');

        if (empty($data)) {
            return ['success' => 0, 'failed' => 0, 'message' => 'Data kosong.'];
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($data as $item) {
            $item = (array) $item;
            $nuptk = $item['nuptk'] ?? null;
            $nik = $item['nik'] ?? null;
            $ptk_id = $item['ptk_id'] ?? null;

            if (! $nuptk && ! $nik && ! $ptk_id) {
                $failed++;

                continue;
            }

            try {
                $this->syncGtkRecord($item, $nuptk, $nik, $ptk_id);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $keyField = $nuptk ? 'nuptk' : ($nik ? 'nik' : 'ptk_id');
                $key = $nuptk ?: ($nik ?: $ptk_id);
                $errors[] = "{$keyField} {$key}: {$e->getMessage()}";
            }
        }

        return $this->formatResult($success, $failed, $errors);
    }

    private function syncGtkRecord(array $item, ?string $nuptk, ?string $nik, ?string $ptk_id): void
    {
        $kelamin = $this->mapKelamin($item['jenis_kelamin'] ?? '');

        $ptkData = [
            'ptk_id' => $ptk_id,
            'nuptk' => $nuptk,
            'nik' => $nik,
            'nip' => $item['nip'] ?? null,
            'kelamin' => $kelamin,
            'tempat_lahir' => $item['tempat_lahir'] ?? null,
            'tanggal_lahir' => isset($item['tanggal_lahir']) ? date('Y-m-d', strtotime($item['tanggal_lahir'])) : null,
            'agama' => isset($item['agama_id']) ? (int) $item['agama_id'] : null,
            'pendidikan_terakhir' => $item['pendidikan_terakhir'] ?? null,
            'bidang_studi_terakhir' => $item['bidang_studi_terakhir'] ?? null,
            'pangkat_golongan' => $item['pangkat_golongan_terakhir'] ?? null,
            'status_kepegawaian' => $item['status_kepegawaian_id_str'] ?? null,
            'jenis_ptk' => $item['jenis_ptk_id_str'] ?? null,
            'jabatan_ptk' => $item['jabatan_ptk_id_str'] ?? null,
        ];

        $existingPtk = $this->findExistingPtk($ptk_id, $nuptk, $nik);

        if ($existingPtk) {
            $existingPtk->update($ptkData);

            return;
        }

        $existingUser = $this->findExistingUser($ptk_id, $item);

        if (! $existingUser) {
            $username = $nuptk ?? $nik ?? 'gtk-' . uniqid();
            $existingUser = User::create([
                'nama' => $item['nama'] ?? '-',
                'username' => $username,
                'email' => "{$username}@gtk.e-rapor.sch.id",
                'password' => bcrypt($username),
                'jabatan' => 3,
            ]);
        }

        $ptk = Ptk::create(array_merge($ptkData, ['user_id' => $existingUser->id]));

        if (! $existingUser->ptk_id) {
            $existingUser->update(['ptk_id' => $ptk->id]);
        }
    }

    private function findExistingPtk(?string $ptk_id, ?string $nuptk, ?string $nik): ?Ptk
    {
        $existingPtk = null;

        if ($ptk_id) {
            $existingPtk = Ptk::where('ptk_id', $ptk_id)->first();
        }
        if (! $existingPtk && $nuptk) {
            $existingPtk = Ptk::where('nuptk', $nuptk)->first();
        }
        if (! $existingPtk && $nik) {
            $existingPtk = Ptk::where('nik', $nik)->first();
        }

        return $existingPtk;
    }

    private function findExistingUser(?string $ptk_id, array $item): ?User
    {
        if ($ptk_id) {
            $ptk = Ptk::where('ptk_id', $ptk_id)->first();
            if ($ptk?->user) {
                return $ptk->user;
            }
        }

        return User::whereRaw('LOWER(nama) = ?', [strtolower($item['nama'] ?? '')])
            ->where('jabatan', 3)
            ->first();
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
