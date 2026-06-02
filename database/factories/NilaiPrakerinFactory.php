<?php

namespace Database\Factories;

use App\Models\NilaiPrakerin;
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
            'siswa_prakerin_id' => \App\Models\SiswaPrakerin::factory(),
            'mapel_id' => \App\Models\Mapel::factory(),
            'nilai' => fake()->numberBetween(60, 100),
            'deskripsi' => fake()->sentence(),
        ];
    }
}
