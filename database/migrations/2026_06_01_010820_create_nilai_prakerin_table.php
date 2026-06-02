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
        Schema::create('nilai_prakerin', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_prakerin_id');
            $table->unsignedBigInteger('mapel_id');
            $table->integer('nilai')->default(0);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('siswa_prakerin_id')->references('id')->on('siswa_prakerin');
            $table->foreign('mapel_id')->references('id')->on('mapel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_prakerin');
    }
};
