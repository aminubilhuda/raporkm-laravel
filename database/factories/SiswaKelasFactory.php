<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiswaKelas>
 */
class SiswaKelasFactory extends Factory
{
    protected $model = SiswaKelas::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'kelas_id' => Kelas::factory(),
            'tahun_pelajaran_id' => null,
            'semester_id' => null,
            'status' => 'aktif',
        ];
    }
}
