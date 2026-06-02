<?php

namespace Database\Factories;

use App\Models\DimensiKokurikuler;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DimensiKokurikuler>
 */
class DimensiKokurikulerFactory extends Factory
{
    protected $model = DimensiKokurikuler::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->words(3, true),
            'keterangan' => fake()->sentence(),
        ];
    }
}
