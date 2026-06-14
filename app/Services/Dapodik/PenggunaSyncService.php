<?php

namespace App\Services\Dapodik;

use App\Models\Ptk;
use App\Models\User;

class PenggunaSyncService
{
    public function __construct(private DapodikClient $client)
    {
    }

    public function sync(): array
    {
        $data = $this->client->get('getPengguna');

        if (empty($data)) {
            return ['success' => 0, 'failed' => 0, 'message' => 'Data kosong.'];
        }

        $deduplicated = $this->deduplicate($data);
        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($deduplicated as $item) {
            $email = $item['username'] ?? null;

            if (! $email) {
                $failed++;

                continue;
            }

            try {
                $this->syncUser($item, $email);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "User {$email}: {$e->getMessage()}";
            }
        }

        return $this->formatResult($success, $failed, $errors);
    }

    private function deduplicate(array $data): array
    {
        $deduplicated = [];

        foreach ($data as $item) {
            $item = (array) $item;
            $ptkId = $item['ptk_id'] ?? null;
            $roleStr = $item['peran_id_str'] ?? '';

            $priority = match ($roleStr) {
                'Kepala Sekolah', 'Kepsek' => 1,
                'Operator Sekolah', 'Tata Usaha', 'Admin', 'Bendahara BOS' => 2,
                default => 3,
            };

            $key = $ptkId ?: ($item['username'] ?? uniqid());

            if (! isset($deduplicated[$key]) || $priority < $deduplicated[$key]['_priority']) {
                $item['_priority'] = $priority;
                $deduplicated[$key] = $item;
            }
        }

        return $deduplicated;
    }

    private function syncUser(array $item, string $email): void
    {
        $username = $email;
        $ptk_id = $item['ptk_id'] ?? null;

        $role = match ($item['peran_id_str'] ?? '') {
            'Operator Sekolah', 'Tata Usaha', 'Admin', 'Bendahara BOS' => 2,
            'Kepala Sekolah', 'Kepsek' => 4,
            default => 3,
        };

        $existingUser = $this->findExistingUser($ptk_id, $email, $username, $item, $role);

        if ($existingUser) {
            $this->updateExistingUser($existingUser, $item, $username, $email, $role, $ptk_id);

            return;
        }

        $this->createNewUser($item, $username, $email, $role, $ptk_id);
    }

    private function findExistingUser(?string $ptk_id, string $email, string $username, array $item, int $role): ?User
    {
        if ($ptk_id) {
            $ptk = Ptk::where('ptk_id', $ptk_id)->first();
            if ($ptk?->user) {
                return $ptk->user;
            }
        }

        $existingUser = User::where('email', $email)->first();

        if (! $existingUser) {
            $existingUser = User::where('username', $username)->first();
        }

        if (! $existingUser) {
            $existingUser = User::whereRaw('LOWER(nama) = ?', [strtolower($item['nama'] ?? '')])
                ->where('jabatan', $role)
                ->first();
        }

        return $existingUser;
    }

    private function updateExistingUser(User $existingUser, array $item, string $username, string $email, int $role, ?string $ptk_id): void
    {
        $updateData = [
            'username' => $username,
            'email' => $email,
            'jabatan' => $role,
        ];

        if (! empty($item['no_hp'])) {
            $updateData['kontak'] = $item['no_hp'];
        } elseif (! empty($item['no_telepon'])) {
            $updateData['kontak'] = $item['no_telepon'];
        }

        if (($item['nama'] ?? null) && $item['nama'] !== $existingUser->nama) {
            $updateData['nama'] = $item['nama'];
        }

        $updateData['updated_at'] = now();
        $existingUser->update($updateData);

        // Sync Spatie role
        $newRoleName = $this->getRoleName($role);
        if (! $existingUser->hasRole($newRoleName)) {
            $existingUser->syncRoles([$newRoleName]);
        }

        if (! $existingUser->ptk_id && $ptk_id) {
            $ptk = Ptk::where('ptk_id', $ptk_id)->first();
            if ($ptk) {
                $existingUser->update(['ptk_id' => $ptk->id]);
            }
        }
    }

    private function createNewUser(array $item, string $username, string $email, int $role, ?string $ptk_id): void
    {
        $newUser = User::create([
            'nama' => $item['nama'] ?? '-',
            'username' => $username,
            'email' => $email,
            'password' => bcrypt($username),
            'jabatan' => $role,
            'kontak' => $item['no_hp'] ?? $item['no_telepon'] ?? null,
        ]);

        // Assign Spatie role
        $newUser->assignRole($this->getRoleName($role));

        if ($ptk_id) {
            $ptk = Ptk::where('ptk_id', $ptk_id)->first();
            if ($ptk) {
                $newUser->update(['ptk_id' => $ptk->id]);
            }
        }
    }

    private function getRoleName(int $jabatan): string
    {
        return match ($jabatan) {
            2 => 'TU',
            3 => 'Guru',
            4 => 'Kepsek',
            default => 'Guru',
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
