<?php

namespace Database\Factories;

use App\Models\CatatanWali;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatatanWali>
 */
class CatatanWaliFactory extends Factory
{
    protected $model = CatatanWali::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'kelas_id' => Kelas::factory(),
            'user_id' => User::factory(),
            'tahun_pelajaran_id' => null,
            'semester_id' => null,
            'catatan' => fake()->sentence(8),
        ];
    }
}
