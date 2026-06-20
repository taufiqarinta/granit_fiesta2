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
        Schema::create('survey_agens', function (Blueprint $table) {
            $table->id();
            $table->string('kode_survey', 50)->unique()->index(); // Nomor pengisian survey
            $table->string('nama_agen', 255);
            $table->string('kode_agen', 50)->nullable();
            $table->string('kota', 100)->nullable();
            $table->integer('jumlah_sales')->default(0);
            $table->text('catatan')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_agens');
    }
};
