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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tingkat_id');
            $table->unsignedBigInteger('kompetensi_keahlian_id');
            $table->string('nama_kelas', 50);
            $table->unsignedBigInteger('tahun_pelajaran_id')->nullable();
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tingkat_id')->references('id')->on('tingkat');
            $table->foreign('kompetensi_keahlian_id')->references('id')->on('kompetensi_keahlian');
            $table->foreign('tahun_pelajaran_id')->references('id')->on('tahun_pelajaran');
            $table->foreign('semester_id')->references('id')->on('semester');
            $table->index('tingkat_id');
            $table->index('kompetensi_keahlian_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
