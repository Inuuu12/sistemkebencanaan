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
        Schema::table('app_stok_bantuan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kecamatan')->nullable()->after('sumber');
            $table->unsignedBigInteger('id_kelurahan')->nullable()->after('id_kecamatan');

            $table->foreign('id_kecamatan')
                ->references('id_kecamatan')
                ->on('app_md_kecamatan')
                ->onDelete('set null');

            $table->foreign('id_kelurahan')
                ->references('id_kelurahan')
                ->on('app_md_kelurahan')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_stok_bantuan', function (Blueprint $table) {
            $table->dropForeign(['id_kecamatan']);
            $table->dropForeign(['id_kelurahan']);
            $table->dropColumn(['id_kecamatan', 'id_kelurahan']);
        });
    }
};
