<?php

/**
 * Quick browser smoke test - akses halaman2 kunci
 * Jalankan: php artisan tinker --execute file
 */

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use App\Models\User;

// Cek data master
echo "=== DATA MASTER ===\n";
echo 'Sekolah: '.(Sekolah::count() ?: '⚠️ KOSONG')."\n";
echo 'User (TU): '.User::where('jabatan', 2)->count()."\n";
echo 'User (Guru): '.User::whereIn('jabatan', [3, 4])->count()."\n";
echo 'Siswa: '.Siswa::where('aktif', 1)->count()."\n";
echo 'Kelas: '.Kelas::count()."\n";
echo 'Mapel: '.Mapel::count()."\n";
echo 'Tahun Pelajaran: '.TahunPelajaran::count()."\n";
echo 'Semester: '.Semester::count()."\n";

echo "\n=== OK SEMUA TERLOADING ===\n";
