<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'jabatan' => 2,
            'nama' => 'Admin Tata Usaha',
            'username' => 'admin',
            'email' => 'admin@smkan.sch.id',
        ]);

        User::factory()->create([
            'jabatan' => 3,
            'nama' => 'Guru Demo',
            'username' => 'guru',
            'email' => 'guru@smkan.sch.id',
        ]);

        User::factory()->create([
            'jabatan' => 4,
            'nama' => 'Kepala Sekolah',
            'username' => 'kepsek',
            'email' => 'kepsek@smkan.sch.id',
        ]);
    }
}
