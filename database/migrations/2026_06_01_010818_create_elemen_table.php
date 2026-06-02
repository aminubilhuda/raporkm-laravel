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
        Schema::create('elemen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dimensi_id');
            $table->string('nama', 200);
            $table->text('keterangan')->nullable();
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dimensi_id')->references('id')->on('dimensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elemen');
    }
};
