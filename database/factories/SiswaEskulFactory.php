<?php

namespace Database\Factories;

use App\Models\Eskul;
use App\Models\Siswa;
use App\Models\SiswaEskul;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiswaEskul>
 */
class SiswaEskulFactory extends Factory
{
    protected $model = SiswaEskul::class;

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
