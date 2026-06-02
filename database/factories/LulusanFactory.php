<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Lulusan;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lulusan>
 */
class LulusanFactory extends Factory
{
    protected $model = Lulusan::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'kelas_id' => Kelas::factory(),
            'tahun_pelajaran_id' => TahunPelajaran::factory(),
            'tanggal_lulus' => fake()->dateTimeBetween('-1 year', 'now'),
            'no_ijazah' => 'IJZ-' . fake()->unique()->numerify('####/####'),
            'lanjut_ke' => fake()->randomElement(['SMA', 'SMK', 'Kerja', 'Kuliah']),
            'keterangan' => fake()->sentence(),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Lulusan $l) {
            // Lulusan.tanggal_lulus NOT NULL
        });
    }
}
