<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Presensi>
 */
class PresensiFactory extends Factory
{
    protected $model = Presensi::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'kelas_id' => Kelas::factory(),
            'jenis_absen_id' => fake()->numberBetween(1, 4),
            'tanggal' => fake()->dateTimeBetween('-3 months')->format('Y-m-d'),
            'tahun_pelajaran_id' => null,
            'semester_id' => null,
            'keterangan' => fake()->optional(0.3)->sentence(),
        ];
    }
}
