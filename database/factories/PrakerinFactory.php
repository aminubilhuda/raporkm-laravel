<?php

namespace Database\Factories;

use App\Models\Prakerin;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prakerin>
 */
class PrakerinFactory extends Factory
{
    protected $model = Prakerin::class;

    public function definition(): array
    {
        return [
            'nama_perusahaan' => 'PT '.fake()->company(),
            'alamat' => fake()->address(),
            'kontak' => fake()->phoneNumber(),
            'PIC' => fake()->name(),
            'tanggal_mulai' => fake()->dateTimeBetween('-1 month', 'now'),
            'tanggal_selesai' => fake()->dateTimeBetween('now', '+3 months'),
            'keterangan' => fake()->sentence(),
            'tahun_pelajaran_id' => TahunPelajaran::factory(),
            'semester_id' => Semester::factory(),
        ];
    }
}
