<?php

namespace Database\Factories;

use App\Models\KelompokMapel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KelompokMapel>
 */
class KelompokMapelFactory extends Factory
{
    protected $model = KelompokMapel::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement(['A', 'B', 'C']),
            'keterangan' => 'Kelompok '.fake()->word(),
        ];
    }
}
