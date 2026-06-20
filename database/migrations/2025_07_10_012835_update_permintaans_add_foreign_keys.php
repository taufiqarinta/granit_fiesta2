<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom baru, nullable dulu
        Schema::table('permintaans', function (Blueprint $table) {
            $table->unsignedBigInteger('merk_id')->nullable()->after('tanggal');
            $table->unsignedBigInteger('ukuran_id')->nullable()->after('merk_id');
        });

        // 2. Pindahkan data dari kolom lama ke kolom relasi
        DB::table('permintaans')->get()->each(function ($permintaan) {
            $merk = DB::table('merks')->where('name', $permintaan->merk)->first();
            $ukuran = DB::table('ukurans')->where('name', $permintaan->ukuran)->first();

            DB::table('permintaans')->where('id', $permintaan->id)->update([
                'merk_id' => $merk ? $merk->id : null,
                'ukuran_id' => $ukuran ? $ukuran->id : null,
            ]);
        });

        // 3. Setelah data valid, pasang foreign key & hapus kolom lama
        Schema::table('permintaans', function (Blueprint $table) {
            $table->dropColumn(['merk', 'ukuran']);

            $table->foreign('merk_id')->references('id')->on('merks')->onDelete('cascade');
            $table->foreign('ukuran_id')->references('id')->on('ukurans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permintaans', function (Blueprint $table) {
            // Balikkan ke kolom lama
            $table->string('merk')->after('tanggal');
            $table->string('ukuran')->after('merk');

            // Drop foreign key & kolom relasi
            $table->dropForeign(['merk_id']);
            $table->dropForeign(['ukuran_id']);
            $table->dropColumn(['merk_id', 'ukuran_id']);
        });
    }
};
