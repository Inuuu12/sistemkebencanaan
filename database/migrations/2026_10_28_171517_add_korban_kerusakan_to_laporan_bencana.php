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
            $table->string('tingkat_korban', 10)->nullable()->after('jumlah_korban'); // Rendah, Sedang, Tinggi
            $table->unsignedInteger('jumlah_korban_jiwa')->default(0)->after('tingkat_korban');
            $table->unsignedInteger('jumlah_rumah_rusak_berat')->default(0)->after('jumlah_korban_jiwa');
            $table->unsignedInteger('jumlah_rumah_rusak_sedang')->default(0)->after('jumlah_rumah_rusak_berat');
            $table->unsignedInteger('jumlah_rumah_rusak_ringan')->default(0)->after('jumlah_rumah_rusak_sedang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_laporan_bencana', function (Blueprint $table) {
            $table->dropColumn([
                'tingkat_korban',
                'jumlah_korban_jiwa',
                'jumlah_rumah_rusak_berat',
                'jumlah_rumah_rusak_sedang',
                'jumlah_rumah_rusak_ringan',
            ]);
        });
    }
};
