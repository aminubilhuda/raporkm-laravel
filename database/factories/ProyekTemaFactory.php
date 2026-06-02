<?php

namespace Database\Factories;

use App\Models\ProyekTema;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProyekTema>
 */
class ProyekTemaFactory extends Factory
{
    protected $model = ProyekTema::class;

    public function definition(): array
    {
        $themes = [
            'Gaya Hidup Berkelanjutan',
            'Bhinneka Tunggal Ika',
            'Bangunlah Jiwa dan Ragimu',
            'Suara Demokrasi',
            'Kearifan Lokal',
            'Rekayasa Teknologi',
        ];

        return [
            'nama_tema' => fake()->randomElement($themes),
            'keterangan' => fake()->paragraph(),
            'tahun_pelajaran_id' => TahunPelajaran::factory(),
            'semester_id' => Semester::factory(),
        ];
    }

    public function configure(): static
    {
        return $this;
    }
}
