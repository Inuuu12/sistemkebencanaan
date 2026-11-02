<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_laporan_bencana', function (Blueprint $table) {
            $table->unsignedInteger('jumlah_korban_luka_ringan')->default(0)->after('jumlah_korban_jiwa');
            $table->unsignedInteger('jumlah_korban_luka_sedang')->default(0)->after('jumlah_korban_luka_ringan');
            $table->unsignedInteger('jumlah_korban_luka_berat')->default(0)->after('jumlah_korban_luka_sedang');
        });
    }

    public function down(): void
    {
        Schema::table('app_laporan_bencana', function (Blueprint $table) {
            $table->dropColumn([
                'jumlah_korban_luka_ringan',
                'jumlah_korban_luka_sedang',
                'jumlah_korban_luka_berat',
            ]);
        });
    }
};
