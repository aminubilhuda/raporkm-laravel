<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi_guru_tu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('tanggal');
            $table->unsignedBigInteger('tahun_pelajaran_id');
            $table->unsignedBigInteger('semester_id');

            $table->time('check_in')->nullable();
            $table->decimal('latitude_in', 10, 7)->nullable();
            $table->decimal('longitude_in', 10, 7)->nullable();
            $table->string('foto_selfie_in')->nullable();
            $table->enum('status_check_in', ['tepat_waktu', 'terlambat', 'ditunda'])->nullable();

            $table->time('check_out')->nullable();
            $table->decimal('latitude_out', 10, 7)->nullable();
            $table->decimal('longitude_out', 10, 7)->nullable();
            $table->string('foto_selfie_out')->nullable();
            $table->enum('status_check_out', ['pulang_tepat', 'pulang_cepat'])->nullable();

            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'tanggal', 'tahun_pelajaran_id', 'semester_id'], 'pgtu_unique');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tahun_pelajaran_id')->references('id')->on('tahun_pelajaran');
            $table->foreign('semester_id')->references('id')->on('semester');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi_guru_tu');
    }
};
