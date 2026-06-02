<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TahunPelajaranSemesterSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tahun_pelajaran')->insert([
            ['tahun' => '2024/2025', 'status' => 0],
            ['tahun' => '2025/2026', 'status' => 1],
        ]);

        DB::table('semester')->insert([
            ['nama' => 'Ganjil', 'urutan' => 1, 'status' => 0],
            ['nama' => 'Genap', 'urutan' => 2, 'status' => 1],
        ]);
    }
}
