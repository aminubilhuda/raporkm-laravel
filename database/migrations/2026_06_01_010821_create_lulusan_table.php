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
        Schema::create('lulusan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('tahun_pelajaran_id');
            $table->date('tanggal_lulus');
            $table->string('no_ijazah', 50)->nullable();
            $table->string('lanjut_ke', 200)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('siswa_id')->references('id')->on('siswa');
            $table->foreign('kelas_id')->references('id')->on('kelas');
            $table->foreign('tahun_pelajaran_id')->references('id')->on('tahun_pelajaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lulusan');
    }
};
