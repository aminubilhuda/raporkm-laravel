<?php

namespace Database\Factories;

use App\Models\KelompokMapel;
use App\Models\Mapel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mapel>
 */
class MapelFactory extends Factory
{
    protected $model = Mapel::class;

    public function definition(): array
    {
        return [
            'kelompok_mapel_id' => KelompokMapel::factory(),
            'kode' => strtoupper(fake()->unique()->lexify('MP???')),
            'nama_mapel' => fake()->unique()->randomElement([
                'Matematika',
                'Bahasa Indonesia',
                'Bahasa Inggris',
                'Pemrograman Web',
                'Basis Data',
                'Jaringan Komputer',
                'Sistem Operasi',
            ]),
            'kkm' => 75,
            'kurikulum_id' => null,
        ];
    }
}
