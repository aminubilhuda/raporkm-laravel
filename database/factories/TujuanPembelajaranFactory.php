<?php

namespace Database\Factories;

use App\Models\TujuanPembelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TujuanPembelajaran>
 */
class TujuanPembelajaranFactory extends Factory
{
    protected $model = TujuanPembelajaran::class;

    public function definition(): array
    {
        return [
            'mapel_id' => \App\Models\Mapel::factory(),
            'kelas_id' => \App\Models\Kelas::factory(),
            'kode_tp' => 'TP-'.fake()->unique()->numberBetween(1, 999),
            'nama_tp' => 'Mampu '.fake()->sentence(6),
            'tahun_pelajaran_id' => null,
            'semester_id' => null,
        ];
    }
}
