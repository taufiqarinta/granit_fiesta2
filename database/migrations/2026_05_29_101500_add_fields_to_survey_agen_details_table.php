<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('survey_agen_details', function (Blueprint $table) {
            if (!Schema::hasColumn('survey_agen_details', 'no_hp')) {
                $table->string('no_hp', 50)->nullable()->after('nama_sales');
            }
            if (!Schema::hasColumn('survey_agen_details', 'area')) {
                $table->string('area', 255)->nullable()->after('no_hp');
            }
            if (!Schema::hasColumn('survey_agen_details', 'top_10_pareto')) {
                $table->text('top_10_pareto')->nullable()->after('area');
            }
            if (!Schema::hasColumn('survey_agen_details', 'target_penjualan')) {
                $table->string('target_penjualan', 100)->nullable()->after('top_10_pareto');
            }
            if (!Schema::hasColumn('survey_agen_details', 'brands')) {
                $table->string('brands', 255)->nullable()->after('target_penjualan');
            }
            if (!Schema::hasColumn('survey_agen_details', 'keliling_luar_kota')) {
                $table->text('keliling_luar_kota')->nullable()->after('brands');
            }
            if (!Schema::hasColumn('survey_agen_details', 'toko_butuh_support')) {
                $table->text('toko_butuh_support')->nullable()->after('keliling_luar_kota');
            }
            if (!Schema::hasColumn('survey_agen_details', 'saran_kobin')) {
                $table->text('saran_kobin')->nullable()->after('toko_butuh_support');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('survey_agen_details', function (Blueprint $table) {
            if (Schema::hasColumn('survey_agen_details', 'saran_kobin')) {
                $table->dropColumn('saran_kobin');
            }
            if (Schema::hasColumn('survey_agen_details', 'toko_butuh_support')) {
                $table->dropColumn('toko_butuh_support');
            }
            if (Schema::hasColumn('survey_agen_details', 'keliling_luar_kota')) {
                $table->dropColumn('keliling_luar_kota');
            }
            if (Schema::hasColumn('survey_agen_details', 'brands')) {
                $table->dropColumn('brands');
            }
            if (Schema::hasColumn('survey_agen_details', 'target_penjualan')) {
                $table->dropColumn('target_penjualan');
            }
            if (Schema::hasColumn('survey_agen_details', 'top_10_pareto')) {
                $table->dropColumn('top_10_pareto');
            }
            if (Schema::hasColumn('survey_agen_details', 'area')) {
                $table->dropColumn('area');
            }
            if (Schema::hasColumn('survey_agen_details', 'no_hp')) {
                $table->dropColumn('no_hp');
            }
        });
    }
};
