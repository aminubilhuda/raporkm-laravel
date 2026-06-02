<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Tingkat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kelas>
 */
class KelasFactory extends Factory
{
    protected $model = Kelas::class;

    public function definition(): array
    {
        return [
            'tingkat_id' => Tingkat::factory(),
            'kompetensi_keahlian_id' => KompetensiKeahlian::factory(),
            'nama_kelas' => fake()->randomElement(['A', 'B', 'C']).' - '.fake()->numberBetween(1, 3),
            'tahun_pelajaran_id' => null,
            'semester_id' => null,
        ];
    }
}
