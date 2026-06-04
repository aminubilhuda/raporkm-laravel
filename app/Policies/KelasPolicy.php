<?php

namespace App\Policies;

use App\Models\Kelas;
use App\Models\User;

class KelasPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isTU() || $user->isKepsek() || $user->isGuru();
    }

    public function view(User $user, Kelas $kelas): bool
    {
        if ($user->isTU() || $user->isKepsek()) {
            return true;
        }

        if ($user->isGuru()) {
            return $user->kelasWali()->where('kelas_id', $kelas->id)->exists()
                || $user->mapelKelas()->where('kelas_id', $kelas->id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isTU();
    }

    public function update(User $user, Kelas $kelas): bool
    {
        return $user->isTU();
    }

    public function delete(User $user, Kelas $kelas): bool
    {
        return $user->isTU();
    }
}
