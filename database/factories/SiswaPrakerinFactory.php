<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Prakerin;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaPrakerin;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiswaPrakerin>
 */
class SiswaPrakerinFactory extends Factory
{
    protected $model = SiswaPrakerin::class;

    public function definition(): array
    {
        return [
            'prakerin_id' => Prakerin::factory(),
            'siswa_id' => Siswa::factory(),
            'kelas_id' => Kelas::factory(),
            'user_id' => User::factory(),
            'status' => 'aktif',
            'tahun_pelajaran_id' => TahunPelajaran::factory(),
            'semester_id' => Semester::factory(),
        ];
    }
}
