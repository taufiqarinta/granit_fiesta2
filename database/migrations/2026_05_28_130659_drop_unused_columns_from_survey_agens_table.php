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
            $table->dropColumn([
                'kota',
                'catatan',
                'ip_address',
                'user_agent',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_agens', function (Blueprint $table) {
            $table->string('kota', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
        });
    }
};
