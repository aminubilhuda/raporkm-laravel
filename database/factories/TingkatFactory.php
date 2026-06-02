<?php

namespace Database\Factories;

use App\Models\Tingkat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tingkat>
 */
class TingkatFactory extends Factory
{
    protected $model = Tingkat::class;

    public function definition(): array
    {
        $angka = fake()->unique()->numberBetween(10, 99);

        return [
            'nama' => "Kelas {$angka}",
            'angka' => $angka,
            'fase' => $angka === 10 ? 'E' : 'F',
            'urutan' => $angka,
        ];
    }
}
