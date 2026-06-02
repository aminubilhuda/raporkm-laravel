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
        Schema::create('pembina_eskul', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('eskul_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tahun_pelajaran_id');

            $table->foreign('eskul_id')->references('id')->on('eskul')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tahun_pelajaran_id')->references('id')->on('tahun_pelajaran')->onDelete('cascade');

            $table->unique(['eskul_id', 'user_id', 'tahun_pelajaran_id']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembina_eskul');
    }
};
