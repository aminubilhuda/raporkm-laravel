<?php

namespace Database\Seeders;

use App\Models\Dimensi;
use App\Models\Elemen;
use App\Models\Eskul;
use App\Models\Kelas;
use App\Models\KelasWali;
use App\Models\KelompokMapel;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\PembinaEskul;
use App\Models\PiketHarian;
use App\Models\ProyekKelas;
use App\Models\ProyekTema;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\SubElemen;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UatDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1 Sekolah ──
        $sekolah = Sekolah::create([
            'nama_sekolah' => 'SMK Abdinegara',
            'alamat' => 'Jl. Raya Utama No. 1',
            'kota' => 'Pandeglang',
            'provinsi' => 'Banten',
            'kode_pos' => '42211',
            'telepon' => '0253-123456',
            'email' => 'info@smkabdinegara.sch.id',
            'website' => 'https://smkabdinegara.sch.id',
            'npsn' => '20224400',
            'nss' => '123456789012',
            'kurikulum' => 'Kurikulum Merdeka',
        ]);

        // ── 2 Tahun Pelajaran ──
        $tp1 = TahunPelajaran::create(['tahun' => '2024/2025', 'status' => false]);
        $tp2 = TahunPelajaran::create(['tahun' => '2025/2026', 'status' => true]);
        $sekolah->update(['tahun_aktif' => $tp2->id]);

        // ── 2 Semester ──
        $sem1 = Semester::create(['nama' => 'Ganjil', 'urutan' => 1, 'status' => false]);
        $sem2 = Semester::create(['nama' => 'Genap', 'urutan' => 2, 'status' => true]);
        $sekolah->update(['semester_aktif' => $sem2->id]);

        // ── 3 Tingkat ──
        $tingkatX = Tingkat::create(['nama_tingkat' => 'X', 'urutan' => 1]);
        $tingkatXI = Tingkat::create(['nama_tingkat' => 'XI', 'urutan' => 2]);
        $tingkatXII = Tingkat::create(['nama_tingkat' => 'XII', 'urutan' => 3]);

        // ── 2 Kompetensi Keahlian ──
        $tkj = KompetensiKeahlian::create(['kode' => 'TKJ', 'nama' => 'Teknik Komputer dan Jaringan']);
        $rpl = KompetensiKeahlian::create(['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak']);

        // ── 6 Kelas ──
        $kelasXTKJ1 = Kelas::create(['nama_kelas' => 'X TKJ 1', 'tingkat_id' => $tingkatX->id, 'kompetensi_keahlian_id' => $tkj->id]);
        $kelasXTKJ2 = Kelas::create(['nama_kelas' => 'X TKJ 2', 'tingkat_id' => $tingkatX->id, 'kompetensi_keahlian_id' => $tkj->id]);
        $kelasXITKJ1 = Kelas::create(['nama_kelas' => 'XI TKJ 1', 'tingkat_id' => $tingkatXI->id, 'kompetensi_keahlian_id' => $tkj->id]);
        $kelasXITKJ2 = Kelas::create(['nama_kelas' => 'XI TKJ 2', 'tingkat_id' => $tingkatXI->id, 'kompetensi_keahlian_id' => $tkj->id]);
        $kelasXIITKJ1 = Kelas::create(['nama_kelas' => 'XII TKJ 1', 'tingkat_id' => $tingkatXII->id, 'kompetensi_keahlian_id' => $tkj->id]);
        $kelasXIITKJ2 = Kelas::create(['nama_kelas' => 'XII TKJ 2', 'tingkat_id' => $tingkatXII->id, 'kompetensi_keahlian_id' => $tkj->id]);

        // ── 10 Mapel ──
        $mapelData = [
            ['nama_mapel' => 'Matematika', 'kelompok' => 'A'],
            ['nama_mapel' => 'Bahasa Indonesia', 'kelompok' => 'A'],
            ['nama_mapel' => 'Bahasa Inggris', 'kelompok' => 'A'],
            ['nama_mapel' => 'Pendidikan Agama', 'kelompok' => 'A'],
            ['nama_mapel' => 'PPKn', 'kelompok' => 'A'],
            ['nama_mapel' => 'PJOK', 'kelompok' => 'A'],
            ['nama_mapel' => 'Jaringan Komputer', 'kelompok' => 'C'],
            ['nama_mapel' => 'Sistem Operasi', 'kelompok' => 'C'],
            ['nama_mapel' => 'Pemrograman Web', 'kelompok' => 'C'],
            ['nama_mapel' => 'Basis Data', 'kelompok' => 'C'],
        ];

        $mapels = collect();
        foreach ($mapelData as $i => $md) {
            $mapels->push(Mapel::create([
                'nama_mapel' => $md['nama_mapel'],
                'kelompok_mapel' => $md['kelompok'],
                'urutan' => $i + 1,
            ]));
        }

        // ── 5 Kelompok Mapel ──
        KelompokMapel::create(['nama' => 'Muatan Nasional', 'kode' => 'A', 'urutan' => 1]);
        KelompokMapel::create(['nama' => 'Muatan Kewilayahan', 'kode' => 'B', 'urutan' => 2]);
        KelompokMapel::create(['nama' => 'Muatan Kejuruan', 'kode' => 'C', 'urutan' => 3]);
        KelompokMapel::create(['nama' => 'Muatan Pilihan', 'kode' => 'D', 'urutan' => 4]);
        KelompokMapel::create(['nama' => 'Muatan Budi Pekerti', 'kode' => 'E', 'urutan' => 5]);

        // ── Users: 1 TU, 1 Kepsek, 6 Guru ──
        $tu = User::create([
            'nama' => 'Operator TU',
            'username' => 'tu',
            'email' => 'tu@test.com',
            'password' => Hash::make('password'),
            'jabatan' => 2,
        ]);

        $kepsek = User::create([
            'nama' => 'Kepala Sekolah',
            'username' => 'kepsek',
            'email' => 'kepsek@test.com',
            'password' => Hash::make('password'),
            'jabatan' => 4,
        ]);

        $guruWaliX = User::create([
            'nama' => 'Pak Budi (Wali X)',
            'username' => 'guru.wali.x',
            'email' => 'guruwx@test.com',
            'password' => Hash::make('password'),
            'jabatan' => 3,
        ]);

        $guruWaliXI = User::create([
            'nama' => 'Bu Ani (Wali XI)',
            'username' => 'guru.wali.xi',
            'email' => 'guruwxi@test.com',
            'password' => Hash::make('password'),
            'jabatan' => 3,
        ]);

        $guruPengajar = User::create([
            'nama' => 'Pak Deni (Pengajar)',
            'username' => 'guru.pengajar',
            'email' => 'gurup@test.com',
            'password' => Hash::make('password'),
            'jabatan' => 3,
        ]);

        $guruEskul = User::create([
            'nama' => 'Bu Eka (Pembina Eskul)',
            'username' => 'guru.eskul',
            'email' => 'gurue@test.com',
            'password' => Hash::make('password'),
            'jabatan' => 3,
        ]);

        $guruPiket = User::create([
            'nama' => 'Pak Fajar (Piket)',
            'username' => 'guru.piket',
            'email' => 'guruf@test.com',
            'password' => Hash::make('password'),
            'jabatan' => 3,
        ]);

        $guruPKL = User::create([
            'nama' => 'Bu Hana (Pembimbing PKL)',
            'username' => 'guru.pkl',
            'email' => 'guruh@test.com',
            'password' => Hash::make('password'),
            'jabatan' => 3,
        ]);

        // ── Kelas Wali ──
        KelasWali::insert([
            ['kelas_id' => $kelasXTKJ1->id, 'user_id' => $guruWaliX->id, 'tahun_pelajaran_id' => $tp2->id, 'semester_id' => $sem2->id, 'created_at' => now(), 'updated_at' => now()],
            ['kelas_id' => $kelasXITKJ1->id, 'user_id' => $guruWaliXI->id, 'tahun_pelajaran_id' => $tp2->id, 'semester_id' => $sem2->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Mapel Kelas (Guru Pengajar) ──
        foreach ($mapels->slice(0, 6) as $mapel) {
            MapelKelas::create([
                'mapel_id' => $mapel->id,
                'kelas_id' => $kelasXTKJ1->id,
                'user_id' => $guruPengajar->id,
                'tahun_pelajaran_id' => $tp2->id,
                'semester_id' => $sem2->id,
            ]);
        }

        // ── Siswa (180 = 6 kelas × 30) ──
        $allKelas = [$kelasXTKJ1, $kelasXTKJ2, $kelasXITKJ1, $kelasXITKJ2, $kelasXIITKJ1, $kelasXIITKJ2];
        $siswaCount = 0;

        foreach ($allKelas as $kelas) {
            for ($i = 1; $i <= 30; $i++) {
                $siswaCount++;
                $siswa = Siswa::create([
                    'nama_siswa' => 'Siswa '.$siswaCount,
                    'nis' => sprintf('NIS%04d', $siswaCount),
                    'nisn' => sprintf('NISN%08d', $siswaCount),
                    'jk' => $i % 2 === 0 ? 1 : 2,
                ]);

                SiswaKelas::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $kelas->id,
                    'tahun_pelajaran_id' => $tp2->id,
                    'semester_id' => $sem2->id,
                ]);
            }
        }

        // ── Ekstrakurikuler ──
        $eskul1 = Eskul::create(['nama_eskul' => 'Pramuka', 'keterangan' => 'Gerakan Pramuka']);
        $eskul2 = Eskul::create(['nama_eskul' => 'Basket', 'keterangan' => 'Olahraga Basket']);
        PembinaEskul::create([
            'eskul_id' => $eskul1->id,
            'user_id' => $guruEskul->id,
            'tahun_pelajaran_id' => $tp2->id,
        ]);

        // ── Piket Harian ──
        for ($hari = 1; $hari <= 5; $hari++) {
            PiketHarian::create([
                'user_id' => $guruPiket->id,
                'hari_id' => $hari,
                'tahun_pelajaran_id' => $tp2->id,
                'semester_id' => $sem2->id,
            ]);
        }

        // ── Dimensi, Elemen, Sub-Elemen P5 ──
        $dimensi1 = Dimensi::create(['nama' => 'Beriman dan Bertakwa', 'keterangan' => 'Beriman, Bertakwa kepada Tuhan Yang Maha Esa, dan Berakhlak Mulia', 'urutan' => 1]);
        $dimensi2 = Dimensi::create(['nama' => 'Berkebinekaan Global', 'keterangan' => 'Mengenal dan menghargai budaya', 'urutan' => 2]);

        $elemen1 = Elemen::create(['dimensi_id' => $dimensi1->id, 'nama' => 'Akhlak Beragama', 'keterangan' => 'Praktik beribadah', 'urutan' => 1]);
        $elemen2 = Elemen::create(['dimensi_id' => $dimensi2->id, 'nama' => 'Kepedulian terhadap Kemanusiaan', 'urutan' => 1]);

        $sub1 = SubElemen::create(['elemen_id' => $elemen1->id, 'nama' => 'Beribadah', 'urutan' => 1]);
        $sub2 = SubElemen::create(['elemen_id' => $elemen1->id, 'nama' => 'Toleransi', 'urutan' => 2]);
        $sub3 = SubElemen::create(['elemen_id' => $elemen2->id, 'nama' => 'Peduli Sesama', 'urutan' => 1]);
        $sub4 = SubElemen::create(['elemen_id' => $elemen2->id, 'nama' => 'Gotong Royong', 'urutan' => 2]);

        // ── Tema & Proyek P5 ──
        $tema = ProyekTema::create([
            'nama_tema' => 'Gaya Hidup Berkelanjutan',
            'keterangan' => 'Proyek tentang lingkungan',
            'tahun_pelajaran_id' => $tp2->id,
            'semester_id' => $sem2->id,
        ]);

        ProyekKelas::create([
            'kelas_id' => $kelasXTKJ1->id,
            'proyek_tema_id' => $tema->id,
            'judul' => 'Bank Sampah Digital',
            'deskripsi' => 'Membuat aplikasi sederhana pengelolaan sampah',
            'user_id' => $guruWaliX->id,
            'tahun_pelajaran_id' => $tp2->id,
            'semester_id' => $sem2->id,
        ]);

        $this->command->info('UAT seeder completed: 1 sekolah, 6 kelas, 180 siswa, 10 mapel, 8 users');
    }
}
