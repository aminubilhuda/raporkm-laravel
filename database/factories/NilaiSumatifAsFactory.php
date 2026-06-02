<?php

namespace Database\Factories;

use App\Models\NilaiSumatifAs;
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
            'kelas_id' => \App\Models\Kelas::factory(),
            'mapel_id' => \App\Models\Mapel::factory(),
            'siswa_id' => \App\Models\Siswa::factory(),
            'nilai' => fake()->numberBetween(60, 100),
            'deskripsi' => fake()->sentence(),
        ];
    }
}
