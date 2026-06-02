<?php

namespace Database\Factories;

use App\Models\Eskul;
use App\Models\Sekolah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Eskul>
 */
class EskulFactory extends Factory
{
    protected $model = Eskul::class;

    public function definition(): array
    {
        $names = ['Pramuka', 'Paskibraka', 'PMR', 'Basket', 'Futsal', 'Voli', 'Seni Tari', 'Musik'];

        return [
            'sekolah_id' => Sekolah::factory(),
            'nama_eskul' => fake()->randomElement($names),
            'keterangan' => fake()->sentence(),
        ];
    }
}
