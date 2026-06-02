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
        Schema::create('tingkat', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 10);
            $table->unsignedTinyInteger('angka');
            $table->string('fase', 5);
            $table->unsignedTinyInteger('urutan');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('angka');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tingkat');
    }
};
