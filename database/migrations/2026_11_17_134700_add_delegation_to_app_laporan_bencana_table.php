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
        Schema::table('app_laporan_bencana', function (Blueprint $table) {
            $table->string('status', 50)->default('Pending')->change();
            $table->string('diteruskan_ke', 50)->nullable()->after('status'); // KECAMATAN, BPBD
            $table->text('catatan_kelurahan')->nullable()->after('diteruskan_ke');
        });

        Schema::table('app_penanganan', function (Blueprint $table) {
            $table->string('status_baru', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_penanganan', function (Blueprint $table) {
            $table->string('status_baru', 20)->change();
        });

        Schema::table('app_laporan_bencana', function (Blueprint $table) {
            $table->dropColumn(['diteruskan_ke', 'catatan_kelurahan']);
            $table->string('status', 20)->default('MENUNGGU')->change();
        });
    }
};
