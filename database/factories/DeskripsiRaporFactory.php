<?php

namespace Database\Factories;

use App\Models\DeskripsiRapor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeskripsiRapor>
 */
class DeskripsiRaporFactory extends Factory
{
    protected $model = DeskripsiRapor::class;

    public function definition(): array
    {
        $predikat = fake()->randomElement(['SB', 'B', 'C', 'PB']);

        return [
            'nama' => 'Deskripsi '.$predikat,
            'kktp' => 75,
            'predikat' => $predikat,
            'deskripsi' => fake()->paragraph(),
        ];
    }

    public function predikat(string $predikat): static
    {
        return $this->state(fn (): array => [
            'predikat' => $predikat,
            'nama' => 'Deskripsi '.$predikat,
        ]);
    }
}
