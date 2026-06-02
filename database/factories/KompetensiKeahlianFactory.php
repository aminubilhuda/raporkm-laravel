<?php

namespace Database\Factories;

use App\Models\KompetensiKeahlian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KompetensiKeahlian>
 */
class KompetensiKeahlianFactory extends Factory
{
    protected $model = KompetensiKeahlian::class;

    public function definition(): array
    {
        $nama = fake()->unique()->randomElement([
            'Teknik Komputer dan Jaringan',
            'Rekayasa Perangkat Lunak',
            'Multimedia',
            'Akuntansi',
            'Otomatisasi Perkantoran',
        ]);

        return [
            'nama' => $nama,
            'singkatan' => strtoupper(substr($nama, 0, 3)),
            'keterangan' => null,
        ];
    }
}
