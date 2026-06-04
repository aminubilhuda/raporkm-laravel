<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\User;
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
            'mapel_id' => Mapel::factory(),
            'kelas_id' => Kelas::factory(),
            'user_id' => User::factory(),
            'tahun_pelajaran_id' => null,
            'semester_id' => null,
            'kkm' => 75,
        ];
    }
}
