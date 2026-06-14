<?php

namespace App\Services;

use App\Models\GuruMenuAkses;
use App\Models\Ptk;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PegawaiService
{
    public function __construct(private GuruMenuService $guruMenuService)
    {
    }

    /**
     * Create a new user (pegawai) with optional GTK record and menu access.
     */
    public function createPegawai(array $validated): User
    {
        $user = User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'jabatan' => $validated['jabatan'],
            'kontak' => $validated['kontak'] ?? null,
            'id_tugas_tambahan' => $validated['id_tugas_tambahan'] ?? null,
            'moto' => $validated['moto'] ?? null,
        ]);

        // Assign Spatie role
        $user->assignRole($this->getRoleName($validated['jabatan']));

        $this->syncPtkRecord($user, $validated);

        if ($user->isGuru() || $user->isKepsek()) {
            $this->syncMenuAkses($user, $validated['menu_akses'] ?? []);
        }

        return $user;
    }

    /**
     * Update an existing user (pegawai) with optional GTK record and menu access.
     */
    public function updatePegawai(User $user, array $validated): User
    {
        $userData = [
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'jabatan' => $validated['jabatan'],
            'kontak' => $validated['kontak'] ?? null,
            'id_tugas_tambahan' => $validated['id_tugas_tambahan'] ?? null,
            'moto' => $validated['moto'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // Sync Spatie role
        $newRole = $this->getRoleName($validated['jabatan']);
        if (! $user->hasRole($newRole)) {
            $user->syncRoles([$newRole]);
        }

        $this->syncPtkRecord($user, $validated);

        if ($user->isGuru() || $user->isKepsek()) {
            $this->syncMenuAkses($user, $validated['menu_akses'] ?? []);
        } else {
            GuruMenuAkses::where('user_id', $user->id)->delete();
        }

        return $user;
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

    /**
     * Sync or create GTK (Ptk) record for a user.
     */
    private function syncPtkRecord(User $user, array $validated): void
    {
        $ptkData = [
            'nip' => $validated['nip'] ?? null,
            'nuptk' => $validated['nuptk'] ?? null,
            'kelamin' => $validated['kelamin'] ?? null,
            'agama' => $validated['agama'] ?? null,
        ];

        if ($user->ptk) {
            $user->ptk->update($ptkData);

            return;
        }

        if (! empty($validated['nip']) || ! empty($validated['nuptk'])) {
            $ptk = Ptk::create(array_merge($ptkData, ['user_id' => $user->id]));
            $user->update(['ptk_id' => $ptk->id]);
        }
    }

    /**
     * Sync menu access overrides for guru/kepsek users.
     */
    public function syncMenuAkses(User $user, array $menuOverrides): void
    {
        $records = [];

        foreach (GuruMenuService::MENU_SLUGS as $slug) {
            $tipe = $menuOverrides[$slug] ?? null;
            if ($tipe && in_array($tipe, ['grant', 'revoke'])) {
                $records[] = [
                    'user_id' => $user->id,
                    'menu_slug' => $slug,
                    'tipe' => $tipe,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        GuruMenuAkses::where('user_id', $user->id)->delete();

        if ($records) {
            GuruMenuAkses::insert($records);
        }
    }
}
