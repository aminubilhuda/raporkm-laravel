<?php

namespace Database\Factories;

use App\Models\PiketHarian;
use App\Models\RefHari;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PiketHarian>
 */
class PiketHarianFactory extends Factory
{
    protected $model = PiketHarian::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->guru(),
            'hari_id' => RefHari::factory(),
            'tahun_pelajaran_id' => TahunPelajaran::factory(),
            'semester_id' => Semester::factory(),
        ];
    }
}
