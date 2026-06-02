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
        Schema::create('nilai_mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_kelas_id');
            $table->unsignedBigInteger('mapel_kelas_id');
            $table->integer('nilai')->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('predikat', 30)->nullable();

            $table->foreign('siswa_kelas_id')->references('id')->on('siswa_kelas')->onDelete('cascade');
            $table->foreign('mapel_kelas_id')->references('id')->on('mapel_kelas')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_mata_pelajaran');
    }
};
