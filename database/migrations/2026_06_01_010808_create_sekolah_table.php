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
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('npsn', 20);
            $table->string('nama_sekolah', 200);
            $table->unsignedTinyInteger('id_jenjang')->default(1);
            $table->unsignedTinyInteger('bentuk_sekolah')->default(1);
            $table->text('yayasan')->nullable();
            $table->string('website', 255)->nullable();
            $table->text('alamat')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('kontak', 20)->nullable();
            $table->string('desa', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kabupaten', 100)->nullable();
            $table->string('provinsi', 100)->nullable();
            $table->string('logo_prov', 255)->nullable();
            $table->string('logo', 255)->nullable();
            $table->string('gambar1', 255)->nullable();
            $table->unsignedInteger('lokasi')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->text('frame_peta')->nullable();
            $table->unsignedInteger('tahun_aktif')->nullable();
            $table->unsignedInteger('semester_aktif')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolah');
    }
};
