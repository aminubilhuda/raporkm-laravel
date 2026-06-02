<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_siswa', 100);
            $table->string('nik_pd', 20)->nullable();
            $table->string('nkk', 20)->nullable();
            $table->string('nisn', 20);
            $table->string('nis', 20);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->unsignedTinyInteger('kelamin')->nullable();
            $table->unsignedTinyInteger('agama')->nullable();
            $table->string('kontak_siswa', 20)->nullable();
            $table->unsignedTinyInteger('hub_keluarga')->nullable();
            $table->integer('jumlah_saudara')->default(0);
            $table->integer('anak_ke')->default(1);
            $table->string('nama_ayah', 100)->nullable();
            $table->string('nik_ayah', 20)->nullable();
            $table->unsignedInteger('tahun_ayah')->nullable();
            $table->string('pendidikan_ayah', 20)->nullable();
            $table->string('pekerjaan_ayah', 30)->nullable();
            $table->string('kontak_ayah', 14)->nullable();
            $table->string('nama_ibu', 100)->nullable();
            $table->string('nik_ibu', 20)->nullable();
            $table->unsignedInteger('tahun_ibu')->nullable();
            $table->string('pendidikan_ibu', 20)->nullable();
            $table->string('pekerjaan_ibu', 30)->nullable();
            $table->string('kontak_ibu', 14)->nullable();
            $table->text('alamat')->nullable();
            $table->text('alamat_orang_tua')->nullable();
            $table->string('nama_wali', 100)->nullable();
            $table->text('alamat_wali')->nullable();
            $table->string('pekerjaan_wali', 30)->nullable();
            $table->string('kontak_wali', 14)->nullable();
            $table->unsignedTinyInteger('terima_tingkat')->nullable();
            $table->unsignedInteger('jurusan')->nullable();
            $table->text('sekolah_asal')->nullable();
            $table->date('terima_tanggal')->nullable();
            $table->string('terima_kelas', 10)->nullable();
            $table->string('foto', 255)->nullable();
            $table->unsignedTinyInteger('jenis_siswa')->default(1);
            $table->unsignedTinyInteger('aktif')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index('nisn');
            $table->index('nis');
            $table->index('aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
