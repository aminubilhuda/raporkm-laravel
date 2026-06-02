<?php

namespace Database\Factories;

use App\Models\RefJenisKeluar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RefJenisKeluar>
 */
class RefJenisKeluarFactory extends Factory
{
    protected $model = RefJenisKeluar::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement(['Pindah', 'Lulus', 'Mutasi', 'Dikeluarkan', 'Meninggal']),
        ];
    }
}
