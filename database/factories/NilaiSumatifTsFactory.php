<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\NilaiSumatifTs;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NilaiSumatifTs>
 */
class NilaiSumatifTsFactory extends Factory
{
    protected $model = NilaiSumatifTs::class;

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
