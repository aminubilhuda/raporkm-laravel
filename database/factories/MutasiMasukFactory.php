<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\MutasiMasuk;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MutasiMasuk>
 */
class MutasiMasukFactory extends Factory
{
    protected $model = MutasiMasuk::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'kelas_id' => Kelas::factory(),
            'asal_sekolah' => 'SMP '.fake()->city(),
            'tanggal_masuk' => fake()->dateTimeBetween('-1 year', 'now'),
            'alasan' => fake()->sentence(),
            'tahun_pelajaran_id' => TahunPelajaran::factory(),
            'semester_id' => Semester::factory(),
        ];
    }
}
