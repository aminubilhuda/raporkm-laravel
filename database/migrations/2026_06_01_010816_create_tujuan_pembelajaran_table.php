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
        Schema::create('tujuan_pembelajaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mapel_id');
            $table->unsignedBigInteger('kelas_id');
            $table->string('kode_tp', 30)->nullable();
            $table->text('nama_tp');
            $table->unsignedBigInteger('tahun_pelajaran_id');
            $table->unsignedBigInteger('semester_id');

            $table->foreign('mapel_id')->references('id')->on('mapel')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('tahun_pelajaran_id')->references('id')->on('tahun_pelajaran')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semester')->onDelete('cascade');

            $table->index(['mapel_id', 'kelas_id']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tujuan_pembelajaran');
    }
};
