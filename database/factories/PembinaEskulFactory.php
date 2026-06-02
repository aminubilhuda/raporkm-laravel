<?php

namespace Database\Factories;

use App\Models\Eskul;
use App\Models\PembinaEskul;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PembinaEskul>
 */
class PembinaEskulFactory extends Factory
{
    protected $model = PembinaEskul::class;

    public function definition(): array
    {
        return [
            'eskul_id' => Eskul::factory(),
            'user_id' => User::factory()->guru(),
            'tahun_pelajaran_id' => TahunPelajaran::factory(),
        ];
    }
}
