<?php

namespace Database\Factories;

use App\Models\DeskripsiKokurikuler;
use App\Models\DimensiKokurikuler;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeskripsiKokurikuler>
 */
class DeskripsiKokurikulerFactory extends Factory
{
    protected $model = DeskripsiKokurikuler::class;

    public function definition(): array
    {
        return [
            'dimensi_kokurikuler_id' => DimensiKokurikuler::factory(),
            'predikat' => fake()->randomElement(['Sangat Baik', 'Baik', 'Cukup', 'Perlu Bimbingan']),
            'deskripsi' => fake()->paragraph(),
        ];
    }
}
