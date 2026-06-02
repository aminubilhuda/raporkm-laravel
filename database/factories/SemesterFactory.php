<?php

namespace Database\Factories;

use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Semester>
 */
class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement(['Ganjil', 'Genap']),
            'urutan' => fake()->randomElement([1, 2]),
            'status' => 0,
        ];
    }

    public function aktif(): static
    {
        return $this->state(fn (): array => ['status' => 1]);
    }
}
