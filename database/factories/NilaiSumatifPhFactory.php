<?php

namespace Database\Factories;

use App\Models\NilaiSumatifPh;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NilaiSumatifPh>
 */
class NilaiSumatifPhFactory extends Factory
{
    protected $model = NilaiSumatifPh::class;

    public function definition(): array
    {
        return [
            'tahun_pelajaran_id' => null,
            'semester_id' => null,
            'kelas_id' => \App\Models\Kelas::factory(),
            'mapel_id' => \App\Models\Mapel::factory(),
            'tujuan_pembelajaran_id' => \App\Models\TujuanPembelajaran::factory(),
            'siswa_id' => \App\Models\Siswa::factory(),
            'nilai' => fake()->numberBetween(60, 100),
            'deskripsi' => fake()->sentence(),
        ];
    }
}
