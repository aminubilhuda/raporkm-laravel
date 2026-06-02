<?php

namespace Database\Factories;

use App\Models\Sekolah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sekolah>
 */
class SekolahFactory extends Factory
{
    protected $model = Sekolah::class;

    public function definition(): array
    {
        return [
            'npsn' => (string) fake()->unique()->numberBetween(10000000, 99999999),
            'nama_sekolah' => 'SMK Negeri '.fake()->numberBetween(1, 50),
            'id_jenjang' => 1,
            'bentuk_sekolah' => 1,
            'yayasan' => null,
            'website' => fake()->url(),
            'alamat' => fake()->address(),
            'email' => fake()->unique()->safeEmail(),
            'kontak' => fake()->phoneNumber(),
            'desa' => fake()->streetName(),
            'kecamatan' => 'Kecamatan '.fake()->word(),
            'kabupaten' => 'Kabupaten '.fake()->word(),
            'provinsi' => 'Jawa Timur',
            'logo_prov' => null,
            'logo' => null,
            'gambar1' => null,
            'lokasi' => 0,
            'visi' => 'Visi Sekolah',
            'misi' => 'Misi Sekolah',
            'frame_peta' => null,
            'tahun_aktif' => null,
            'semester_aktif' => null,
        ];
    }
}
