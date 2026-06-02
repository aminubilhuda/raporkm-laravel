<?php

namespace Database\Factories;

use App\Models\Dimensi;
use App\Models\Elemen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Elemen>
 */
class ElemenFactory extends Factory
{
    protected $model = Elemen::class;

    public function definition(): array
    {
        return [
            'dimensi_id' => Dimensi::factory(),
            'nama' => fake()->words(3, true),
            'keterangan' => fake()->sentence(),
            'urutan' => fake()->numberBetween(1, 10),
        ];
    }
}
