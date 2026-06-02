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
        Schema::create('mapel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kelompok_mapel_id');
            $table->string('kode', 20)->nullable();
            $table->string('nama_mapel', 200);
            $table->integer('kkm')->default(0);
            $table->unsignedBigInteger('kurikulum_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('kelompok_mapel_id')->references('id')->on('kelompok_mapel');
            $table->foreign('kurikulum_id')->references('id')->on('ref_kurikulum');
            $table->index('kelompok_mapel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapel');
    }
};
