<?php

namespace Database\Factories;

use App\Models\Eskul;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\SiswaEskul>
 */
class SiswaEskulFactory extends Factory
{
    protected $model = \App\Models\SiswaEskul::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'eskul_id' => Eskul::factory(),
            'tahun_pelajaran_id' => null,
            'predikat' => fake()->randomElement(['SB', 'B', 'C', 'PB']),
            'keterangan' => fake()->sentence(),
        ];
    }
}
