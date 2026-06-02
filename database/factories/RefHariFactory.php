<?php

namespace Database\Factories;

use App\Models\RefHari;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RefHari>
 */
class RefHariFactory extends Factory
{
    protected $model = RefHari::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']),
            'urutan' => fake()->numberBetween(1, 6),
        ];
    }
}
