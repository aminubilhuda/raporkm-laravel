<?php

namespace Database\Factories;

use App\Models\TahunPelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TahunPelajaran>
 */
class TahunPelajaranFactory extends Factory
{
    protected $model = TahunPelajaran::class;

    public function definition(): array
    {
        $start = fake()->numberBetween(2020, 2025);
        $end = $start + 1;

        return [
            'tahun' => "{$start}/{$end}",
            'status' => 0,
        ];
    }

    public function aktif(): static
    {
        return $this->state(fn (): array => ['status' => 1]);
    }
}
