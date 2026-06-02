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
        Schema::create('nilai_mapel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('mapel_id');
            $table->unsignedBigInteger('tahun_pelajaran_id');
            $table->unsignedBigInteger('semester_id');
            $table->integer('nilai')->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('kktp', 10)->nullable();
            $table->string('predikat', 30)->nullable();

            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('mapel_id')->references('id')->on('mapel')->onDelete('cascade');
            $table->foreign('tahun_pelajaran_id')->references('id')->on('tahun_pelajaran')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semester')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_mapel');
    }
};
