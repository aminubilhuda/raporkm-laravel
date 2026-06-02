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
        Schema::create('nilai_proyek', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyek_kelas_id');
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('dimensi_id');
            $table->unsignedBigInteger('elemen_id')->nullable();
            $table->string('nilai', 30);
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('tahun_pelajaran_id');
            $table->unsignedBigInteger('semester_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('proyek_kelas_id')->references('id')->on('proyek_kelas');
            $table->foreign('siswa_id')->references('id')->on('siswa');
            $table->foreign('dimensi_id')->references('id')->on('dimensi');
            $table->foreign('elemen_id')->references('id')->on('elemen');
            $table->foreign('tahun_pelajaran_id')->references('id')->on('tahun_pelajaran');
            $table->foreign('semester_id')->references('id')->on('semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_proyek');
    }
};
