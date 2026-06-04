<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\KelasWali;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KelasWali>
 */
class KelasWaliFactory extends Factory
{
    protected $model = KelasWali::class;

    public function definition(): array
    {
        return [
            'kelas_id' => Kelas::factory(),
            'user_id' => User::factory(),
            'tahun_pelajaran_id' => null,
            'semester_id' => null,
        ];
    }
}
