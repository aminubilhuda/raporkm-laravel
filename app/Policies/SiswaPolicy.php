<?php

namespace App\Policies;

use App\Models\Siswa;
use App\Models\User;

class SiswaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isTU() || $user->isKepsek();
    }

    public function view(User $user, Siswa $siswa): bool
    {
        if ($user->isTU() || $user->isKepsek()) {
            return true;
        }

        if ($user->isGuru()) {
            return $user->mapelKelas()
                ->whereHas('kelas.siswaKelas', fn ($q) => $q->where('siswa_id', $siswa->id))
                ->exists()
                || $user->kelasWali()
                    ->whereHas('siswaKelas', fn ($q) => $q->where('siswa_id', $siswa->id))
                    ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isTU();
    }

    public function update(User $user, Siswa $siswa): bool
    {
        return $user->isTU();
    }

    public function delete(User $user, Siswa $siswa): bool
    {
        return $user->isTU();
    }
}
