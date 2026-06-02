<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\MutasiKeluar;
use App\Models\RefJenisKeluar;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MutasiKeluar>
 */
class MutasiKeluarFactory extends Factory
{
    protected $model = MutasiKeluar::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'kelas_id' => Kelas::factory(),
            'tujuan_sekolah' => 'SMA ' . fake()->city(),
            'tanggal_keluar' => fake()->dateTimeBetween('-1 year', 'now'),
            'alasan' => fake()->sentence(),
            'jenis_keluar_id' => RefJenisKeluar::factory(),
            'tahun_pelajaran_id' => TahunPelajaran::factory(),
            'semester_id' => Semester::factory(),
        ];
    }
}
