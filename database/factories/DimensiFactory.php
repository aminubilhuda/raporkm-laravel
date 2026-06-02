<?php

namespace Database\Factories;

use App\Models\Dimensi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dimensi>
 */
class DimensiFactory extends Factory
{
    protected $model = Dimensi::class;

    public function definition(): array
    {
        $names = [
            'Beriman, Bertakwa kepada Tuhan YME, dan Berakhlak Mulia',
            'Berkebinekaan Global',
            'Bergotong Royong',
            'Mandiri',
            'Bernalar Kritis',
            'Kreatif',
        ];

        return [
            'nama' => fake()->randomElement($names),
            'keterangan' => fake()->sentence(),
            'urutan' => fake()->numberBetween(1, 10),
        ];
    }
}
