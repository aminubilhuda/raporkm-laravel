<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\ProyekKelas;
use App\Models\ProyekTema;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProyekKelas>
 */
class ProyekKelasFactory extends Factory
{
    protected $model = ProyekKelas::class;

    public function definition(): array
    {
        return [
            'kelas_id' => Kelas::factory(),
            'proyek_tema_id' => ProyekTema::factory(),
            'judul' => fake()->sentence(4),
            'deskripsi' => fake()->paragraph(),
            'tahun_pelajaran_id' => TahunPelajaran::factory(),
            'semester_id' => Semester::factory(),
            'user_id' => User::factory()->guru(),
        ];
    }
}
