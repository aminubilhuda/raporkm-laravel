<?php

namespace Database\Factories;

use App\Models\Prestasi;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prestasi>
 */
class PrestasiFactory extends Factory
{
    protected $model = Prestasi::class;

    public function definition(): array
    {
        $levels = ['Sekolah', 'Kecamatan', 'Kabupaten', 'Provinsi', 'Nasional', 'Internasional'];

        return [
            'siswa_id' => Siswa::factory(),
            'nama_prestasi' => 'Juara ' . fake()->numberBetween(1, 3) . ' ' . fake()->randomElement(['Olimpiade', 'Lomba', 'Kompetisi']) . ' ' . fake()->word(),
            'tingkat' => fake()->randomElement($levels),
            'penyelenggara' => fake()->company(),
            'tahun' => fake()->numberBetween(2020, 2026),
            'keterangan' => fake()->sentence(),
        ];
    }
}
