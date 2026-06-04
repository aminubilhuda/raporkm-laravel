<?php

namespace App\Policies;

use App\Models\NilaiMapel;
use App\Models\User;

class NilaiMapelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isTU() || $user->isKepsek() || $user->isGuru();
    }

    public function view(User $user, NilaiMapel $nilai): bool
    {
        if ($user->isTU() || $user->isKepsek()) {
            return true;
        }

        if ($user->isGuru()) {
            return $user->mapelKelas()
                ->where('mapel_id', $nilai->mapel_id)
                ->where('kelas_id', $nilai->kelas_id)
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isTU() || $user->isGuru();
    }

    public function update(User $user, NilaiMapel $nilai): bool
    {
        if ($user->isTU()) {
            return true;
        }

        if ($user->isGuru()) {
            return $user->mapelKelas()
                ->where('mapel_id', $nilai->mapel_id)
                ->where('kelas_id', $nilai->kelas_id)
                ->exists();
        }

        return false;
    }

    public function delete(User $user, NilaiMapel $nilai): bool
    {
        return $user->isTU();
    }
}
