<?php

namespace Database\Factories;

use App\Models\MapelKelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MapelKelas>
 */
class MapelKelasFactory extends Factory
{
    protected $model = MapelKelas::class;

    public function definition(): array
    {
        return [
            'mapel_id' => \App\Models\Mapel::factory(),
            'kelas_id' => \App\Models\Kelas::factory(),
            'user_id' => \App\Models\User::factory(),
            'tahun_pelajaran_id' => null,
            'semester_id' => null,
            'kkm' => 75,
        ];
    }
}
