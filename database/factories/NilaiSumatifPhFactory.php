<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\NilaiSumatifPh;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
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
            'kelas_id' => Kelas::factory(),
            'mapel_id' => Mapel::factory(),
            'tujuan_pembelajaran_id' => TujuanPembelajaran::factory(),
            'siswa_id' => Siswa::factory(),
            'nilai' => fake()->numberBetween(60, 100),
            'deskripsi' => fake()->sentence(),
        ];
    }
}
