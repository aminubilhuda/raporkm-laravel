<?php

namespace Database\Factories;

use App\Models\KelasWali;
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
            'kelas_id' => \App\Models\Kelas::factory(),
            'user_id' => \App\Models\User::factory(),
            'tahun_pelajaran_id' => null,
            'semester_id' => null,
        ];
    }
}
