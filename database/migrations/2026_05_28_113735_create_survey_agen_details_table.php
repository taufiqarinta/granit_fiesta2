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
        Schema::create('survey_agen_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_agen_id')->constrained('survey_agens')->onDelete('cascade');
            $table->string('nama_sales', 255)->index();
            $table->string('distribusi_wilayah', 255)->nullable();
            $table->string('promo_dipakai', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_agen_details');
    }
};
