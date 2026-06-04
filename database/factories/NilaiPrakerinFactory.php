<?php

namespace Database\Factories;

use App\Models\Mapel;
use App\Models\NilaiPrakerin;
use App\Models\SiswaPrakerin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NilaiPrakerin>
 */
class NilaiPrakerinFactory extends Factory
{
    protected $model = NilaiPrakerin::class;

    public function definition(): array
    {
        return [
            'siswa_prakerin_id' => SiswaPrakerin::factory(),
            'mapel_id' => Mapel::factory(),
            'nilai' => fake()->numberBetween(60, 100),
            'deskripsi' => fake()->sentence(),
        ];
    }
}
