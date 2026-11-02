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
        if (!Schema::hasTable('app_posko_pengungsian')) {
            Schema::create('app_posko_pengungsian', function (Blueprint $table) {
                $table->id('id_posko');
                $table->unsignedBigInteger('id_laporan')->nullable();
                $table->unsignedBigInteger('id_kecamatan')->nullable();
                $table->unsignedBigInteger('id_kelurahan')->nullable();
                $table->string('nama_posko', 150);
                $table->string('jenis_posko', 100)->default('Posko Pengungsian & Dapur Umum');
                $table->text('alamat_lengkap')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->integer('kapasitas_maksimal')->default(100);
                $table->integer('jumlah_pengungsi')->default(0);
                $table->integer('jumlah_balita')->default(0);
                $table->integer('jumlah_lansia')->default(0);
                $table->text('fasilitas')->nullable();
                $table->string('pj_nama', 100)->nullable();
                $table->string('pj_kontak', 50)->nullable();
                $table->string('status', 30)->default('Aktif'); // Aktif, Penuh, Standby, Selesai/Ditutup
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_posko_pengungsian');
    }
};
