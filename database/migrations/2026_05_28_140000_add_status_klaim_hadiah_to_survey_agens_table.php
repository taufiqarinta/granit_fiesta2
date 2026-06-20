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
        Schema::table('survey_agens', function (Blueprint $table) {
            $table->tinyInteger('status_klaim_hadiah')->default(0)->comment('0 = belum klaim, 1 = sudah klaim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_agens', function (Blueprint $table) {
            $table->dropColumn('status_klaim_hadiah');
        });
    }
};
