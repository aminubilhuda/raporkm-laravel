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
        Schema::create('nilai_assesmen_subelemen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyek_kelas_id');
            $table->unsignedBigInteger('sub_elemen_id');
            $table->unsignedBigInteger('siswa_id');
            $table->string('nilai', 30);
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('tahun_pelajaran_id');
            $table->unsignedBigInteger('semester_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('proyek_kelas_id')->references('id')->on('proyek_kelas');
            $table->foreign('sub_elemen_id')->references('id')->on('sub_elemen');
            $table->foreign('siswa_id')->references('id')->on('siswa');
            $table->foreign('tahun_pelajaran_id')->references('id')->on('tahun_pelajaran');
            $table->foreign('semester_id')->references('id')->on('semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_assesmen_subelemen');
    }
};
