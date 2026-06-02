<?php

namespace Database\Factories;

use App\Models\Organisasi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organisasi>
 */
class OrganisasiFactory extends Factory
{
    protected $model = Organisasi::class;

    public function definition(): array
    {
        $names = ['OSIS', 'MPK', 'Pramuka', 'PMR', 'Rohis', 'Rohkris'];

        return [
            'nama_organisasi' => fake()->randomElement($names),
            'keterangan' => fake()->sentence(),
        ];
    }
}
