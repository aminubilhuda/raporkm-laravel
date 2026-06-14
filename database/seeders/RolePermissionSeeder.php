<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'user.view', 'user.create', 'user.edit', 'user.delete',
            // Siswa management
            'siswa.view', 'siswa.create', 'siswa.edit', 'siswa.delete',
            // Kelas management
            'kelas.view', 'kelas.create', 'kelas.edit', 'kelas.delete',
            // Mapel management
            'mapel.view', 'mapel.create', 'mapel.edit', 'mapel.delete',
            // Nilai management
            'nilai.view', 'nilai.input', 'nilai.edit', 'nilai.delete',
            // P5BK / Kokurikuler
            'p5bk.view', 'p5bk.input', 'p5bk.edit', 'p5bk.delete',
            // Ekstrakurikuler
            'ekskul.view', 'ekskul.input', 'ekskul.edit', 'ekskul.delete',
            // Presensi
            'presensi.view', 'presensi.input', 'presensi.edit', 'presensi.delete',
            // Prakerin
            'prakerin.view', 'prakerin.input', 'prakerin.edit', 'prakerin.delete',
            // Rapor
            'rapor.view', 'rapor.generate', 'rapor.print',
            // Sekolah settings
            'sekolah.view', 'sekolah.edit',
            // Dapodik sync
            'dapodik.sync',
            // Reports
            'laporan.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions

        // TU (Tata Usaha) - Full admin access
        $tuRole = Role::firstOrCreate(['name' => 'TU', 'guard_name' => 'web']);
        $tuRole->syncPermissions($permissions);

        // Kepsek (Kepala Sekolah) - View all, limited edit
        $kepsekRole = Role::firstOrCreate(['name' => 'Kepsek', 'guard_name' => 'web']);
        $kepsekRole->syncPermissions([
            'user.view',
            'siswa.view',
            'kelas.view',
            'mapel.view',
            'nilai.view',
            'p5bk.view',
            'ekskul.view',
            'presensi.view',
            'prakerin.view',
            'rapor.view', 'rapor.generate', 'rapor.print',
            'sekolah.view',
            'laporan.view',
        ]);

        // Guru - Limited to teaching scope
        $guruRole = Role::firstOrCreate(['name' => 'Guru', 'guard_name' => 'web']);
        $guruRole->syncPermissions([
            'siswa.view',
            'kelas.view',
            'mapel.view',
            'nilai.view', 'nilai.input', 'nilai.edit',
            'p5bk.view', 'p5bk.input',
            'presensi.view', 'presensi.input',
            'prakerin.view', 'prakerin.input',
            'rapor.view',
            'laporan.view',
        ]);

        // Assign roles to existing users based on jabatan
        User::where('jabatan', 2)->each(fn (User $user) => $user->assignRole($tuRole));
        User::where('jabatan', 3)->each(fn (User $user) => $user->assignRole($guruRole));
        User::where('jabatan', 4)->each(fn (User $user) => $user->assignRole($kepsekRole));
    }
}
