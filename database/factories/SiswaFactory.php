<?php

namespace Database\Factories;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Siswa>
 */
class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition(): array
    {
        return [
            'nama_siswa' => fake()->name(),
            'nik_pd' => (string) fake()->unique()->numberBetween(1000000000000000, 9999999999999999),
            'nkk' => (string) fake()->unique()->numberBetween(1000000000000000, 9999999999999999),
            'nisn' => (string) fake()->unique()->numberBetween(1000000000, 9999999999),
            'nis' => (string) fake()->unique()->numberBetween(1000, 9999),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-18 years', '-15 years')->format('Y-m-d'),
            'kelamin' => 1,
            'agama' => 1,
            'kontak_siswa' => fake()->phoneNumber(),
            'hub_keluarga' => 1,
            'jumlah_saudara' => fake()->numberBetween(0, 5),
            'anak_ke' => fake()->numberBetween(1, 5),
            'nama_ayah' => fake()->name('male'),
            'nik_ayah' => (string) fake()->unique()->numberBetween(1000000000000000, 9999999999999999),
            'tahun_ayah' => fake()->numberBetween(1960, 1990),
            'pendidikan_ayah' => 'SMA',
            'pekerjaan_ayah' => 'Wiraswasta',
            'kontak_ayah' => fake()->phoneNumber(),
            'nama_ibu' => fake()->name('female'),
            'nik_ibu' => (string) fake()->unique()->numberBetween(1000000000000000, 9999999999999999),
            'tahun_ibu' => fake()->numberBetween(1960, 1990),
            'pendidikan_ibu' => 'SMA',
            'pekerjaan_ibu' => 'Ibu Rumah Tangga',
            'kontak_ibu' => fake()->phoneNumber(),
            'alamat' => fake()->address(),
            'alamat_orang_tua' => fake()->address(),
            'nama_wali' => null,
            'alamat_wali' => null,
            'pekerjaan_wali' => null,
            'kontak_wali' => null,
            'terima_tingkat' => 10,
            'jurusan' => 1,
            'sekolah_asal' => 'SMP Negeri '.fake()->numberBetween(1, 50),
            'terima_tanggal' => fake()->dateTimeBetween('-2 years')->format('Y-m-d'),
            'terima_kelas' => '10',
            'foto' => null,
            'jenis_siswa' => 1,
            'aktif' => 1,
        ];
    }
}
