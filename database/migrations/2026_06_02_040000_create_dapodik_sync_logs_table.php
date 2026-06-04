<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dapodik_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint');
            $table->string('status');
            $table->integer('records_count')->default(0);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dapodik_sync_logs');
    }
};
