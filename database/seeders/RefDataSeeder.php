<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ref_agama')->insert([
            ['nama' => 'Islam'],
            ['nama' => 'Kristen'],
            ['nama' => 'Katolik'],
            ['nama' => 'Hindu'],
            ['nama' => 'Buddha'],
            ['nama' => 'Konghucu'],
        ]);

        DB::table('ref_jenis_kelamin')->insert([
            ['nama' => 'Laki-laki'],
            ['nama' => 'Perempuan'],
        ]);

        DB::table('ref_hubungan_keluarga')->insert([
            ['nama' => 'Ayah'],
            ['nama' => 'Ibu'],
            ['nama' => 'Wali'],
        ]);

        DB::table('ref_jabatan')->insert([
            ['nama' => 'Tata Usaha'],
            ['nama' => 'Guru'],
            ['nama' => 'Kepala Sekolah'],
        ]);

        DB::table('ref_kepegawaian')->insert([
            ['nama' => 'PNS'],
            ['nama' => 'PPPK'],
            ['nama' => 'GTY'],
            ['nama' => 'GTT'],
            ['nama' => 'Honorer'],
        ]);

        DB::table('ref_pendidikan')->insert([
            ['nama' => 'SD'],
            ['nama' => 'SMP'],
            ['nama' => 'SMA/SMK'],
            ['nama' => 'D1'],
            ['nama' => 'D2'],
            ['nama' => 'D3'],
            ['nama' => 'S1/D4'],
            ['nama' => 'S2'],
            ['nama' => 'S3'],
        ]);

        DB::table('ref_tugas_tambahan')->insert([
            ['nama' => 'Wali Kelas'],
            ['nama' => 'Pembina Eskul'],
            ['nama' => 'Pembimbing Prakerin'],
        ]);

        DB::table('ref_hari')->insert([
            ['nama' => 'Senin', 'urutan' => 1],
            ['nama' => 'Selasa', 'urutan' => 2],
            ['nama' => 'Rabu', 'urutan' => 3],
            ['nama' => 'Kamis', 'urutan' => 4],
            ['nama' => 'Jumat', 'urutan' => 5],
            ['nama' => 'Sabtu', 'urutan' => 6],
        ]);

        DB::table('ref_bulan')->insert([
            ['nama' => 'Januari', 'urutan' => 1],
            ['nama' => 'Februari', 'urutan' => 2],
            ['nama' => 'Maret', 'urutan' => 3],
            ['nama' => 'April', 'urutan' => 4],
            ['nama' => 'Mei', 'urutan' => 5],
            ['nama' => 'Juni', 'urutan' => 6],
            ['nama' => 'Juli', 'urutan' => 7],
            ['nama' => 'Agustus', 'urutan' => 8],
            ['nama' => 'September', 'urutan' => 9],
            ['nama' => 'Oktober', 'urutan' => 10],
            ['nama' => 'November', 'urutan' => 11],
            ['nama' => 'Desember', 'urutan' => 12],
        ]);

        DB::table('ref_jenis_siswa')->insert([
            ['nama' => 'Reguler'],
            ['nama' => 'Mutasi Masuk'],
            ['nama' => 'Mutasi Keluar'],
        ]);

        DB::table('ref_jenis_keluar')->insert([
            ['nama' => 'Pindah Sekolah'],
            ['nama' => 'Dikeluarkan'],
            ['nama' => 'Lulus'],
            ['nama' => 'Meninggal Dunia'],
            ['nama' => 'Henti Tanpa Keterangan'],
        ]);

        DB::table('jenis_absen')->insert([
            ['nama' => 'Hadir', 'keterangan' => 'Kehadiran normal'],
            ['nama' => 'Sakit', 'keterangan' => 'Tidak hadir karena sakit'],
            ['nama' => 'Izin', 'keterangan' => 'Tidak hadir dengan izin'],
            ['nama' => 'Alpa', 'keterangan' => 'Tidak hadir tanpa keterangan'],
        ]);

        DB::table('kelompok_mapel')->insert([
            ['nama' => 'Kelompok A (Umum)', 'keterangan' => 'Mata pelajaran wajib umum'],
            ['nama' => 'Kelompok B (Umum)', 'keterangan' => 'Mata pelajaran wajib umum'],
            ['nama' => 'Kelompok C (Peminatan)', 'keterangan' => 'Mata pelajaran peminatan kejuruan'],
        ]);

        DB::table('tingkat')->insert([
            ['nama' => 'X', 'angka' => 10, 'fase' => 'E', 'urutan' => 1],
            ['nama' => 'XI', 'angka' => 11, 'fase' => 'F', 'urutan' => 2],
            ['nama' => 'XII', 'angka' => 12, 'fase' => 'F', 'urutan' => 3],
        ]);

        DB::table('kompetensi_keahlian')->insert([
            ['nama' => 'Teknik Komputer dan Jaringan', 'singkatan' => 'TKJ'],
            ['nama' => 'Rekayasa Perangkat Lunak', 'singkatan' => 'RPL'],
            ['nama' => 'Multimedia', 'singkatan' => 'MMD'],
        ]);

        DB::table('dimensi')->insert([
            ['nama' => 'Beriman, Bertakwa kepada Tuhan Yang Maha Esa, dan Berakhlak Mulia', 'urutan' => 1],
            ['nama' => 'Berkebhinekaan Global', 'urutan' => 2],
            ['nama' => 'Bergotong Royong', 'urutan' => 3],
            ['nama' => 'Mandiri', 'urutan' => 4],
            ['nama' => 'Bernalar Kritis', 'urutan' => 5],
            ['nama' => 'Kreatif', 'urutan' => 6],
        ]);

        DB::table('dimensi_kokurikuler')->insert([
            ['nama' => 'Nilai Karakter'],
            ['nama' => 'Nilai Kedisiplinan'],
            ['nama' => 'Nilai Tanggung Jawab'],
        ]);

        DB::table('ref_kurikulum')->insert([
            ['nama' => 'Kurikulum Merdeka', 'keterangan' => 'Kurikulum terbaru yang digunakan'],
        ]);

        DB::table('deskripsi_rapor')->insert([
            ['nama' => 'Sangat Baik', 'kktp' => 90, 'predikat' => 'Sangat Baik', 'deskripsi' => 'Peserta didik mampu melampaui capaian pembelajaran dengan sangat baik.'],
            ['nama' => 'Baik', 'kktp' => 75, 'predikat' => 'Baik', 'deskripsi' => 'Peserta didik mampu mencapai capaian pembelajaran dengan baik.'],
            ['nama' => 'Cukup', 'kktp' => 60, 'predikat' => 'Cukup', 'deskripsi' => 'Peserta didik mampu mencapai sebagian capaian pembelajaran.'],
            ['nama' => 'Perlu Bimbingan', 'kktp' => 0, 'predikat' => 'Perlu Bimbingan', 'deskripsi' => 'Peserta didik belum mampu mencapai capaian pembelajaran dan memerlukan bimbingan.'],
        ]);
    }
}
