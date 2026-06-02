<?php

namespace Database\Factories;

use App\Models\Pengingat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pengingat>
 */
class PengingatFactory extends Factory
{
    protected $model = Pengingat::class;

    public function definition(): array
    {
        return [
            'judul' => fake()->words(3, true),
            'pesan' => fake()->paragraph(),
            'untuk_role' => fake()->randomElement([2, 3, 4]),
            'tanggal' => fake()->dateTimeBetween('now', '+1 month'),
            'waktu' => fake()->dateTime(),
            'dikirim' => 0,
        ];
    }
}
