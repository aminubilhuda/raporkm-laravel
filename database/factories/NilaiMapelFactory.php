<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\NilaiMapel;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NilaiMapel>
 */
class NilaiMapelFactory extends Factory
{
    protected $model = NilaiMapel::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'kelas_id' => Kelas::factory(),
            'mapel_id' => Mapel::factory(),
            'tahun_pelajaran_id' => null,
            'semester_id' => null,
            'nilai' => fake()->numberBetween(60, 100),
            'deskripsi' => fake()->sentence(),
            'kktp' => 75,
            'predikat' => 'B',
        ];
    }
}
