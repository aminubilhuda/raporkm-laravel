<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\NilaiSumatifAs;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NilaiSumatifAs>
 */
class NilaiSumatifAsFactory extends Factory
{
    protected $model = NilaiSumatifAs::class;

    public function definition(): array
    {
        return [
            'tahun_pelajaran_id' => null,
            'semester_id' => null,
            'kelas_id' => Kelas::factory(),
            'mapel_id' => Mapel::factory(),
            'siswa_id' => Siswa::factory(),
            'nilai' => fake()->numberBetween(60, 100),
            'deskripsi' => fake()->sentence(),
        ];
    }
}
