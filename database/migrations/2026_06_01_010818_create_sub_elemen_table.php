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
        Schema::create('sub_elemen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('elemen_id');
            $table->text('nama');
            $table->text('capaian')->nullable();
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('elemen_id')->references('id')->on('elemen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_elemen');
    }
};
