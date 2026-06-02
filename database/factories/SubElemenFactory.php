<?php

namespace Database\Factories;

use App\Models\Elemen;
use App\Models\SubElemen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubElemen>
 */
class SubElemenFactory extends Factory
{
    protected $model = SubElemen::class;

    public function definition(): array
    {
        return [
            'elemen_id' => Elemen::factory(),
            'nama' => fake()->words(4, true),
            'capaian' => fake()->sentence(),
            'urutan' => fake()->numberBetween(1, 10),
        ];
    }
}
