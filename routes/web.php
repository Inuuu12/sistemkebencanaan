<?php

use App\Http\Controllers\Admin\BencanaController;
use App\Http\Controllers\Admin\DaerahRawanController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KecamatanController;
use App\Http\Controllers\Admin\KelurahanController;
use App\Http\Controllers\Admin\KerusakanController;
use App\Http\Controllers\Admin\KorbanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\LogistikController;
use App\Http\Controllers\Admin\PengungsianController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\AduanController;
use App\Http\Controllers\Public\ApiController;
use App\Http\Controllers\Public\BeritaController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\KejadianController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Kebencanaan Kabupaten Bogor (AKSARA BOGOR)
|--------------------------------------------------------------------------
| Portal Publik Kebencanaan Kabupaten Bogor & Panel Administrasi
*/

// Public Portal Routes
Route::get('/', [HomeController::class, 'index'])->name('public.home');
Route::get('/kejadian', [KejadianController::class, 'index'])->name('public.kejadian');
Route::get('/kejadian/{id}', [KejadianController::class, 'detail'])->name('public.kejadian.detail');
Route::get('/berita', [BeritaController::class, 'news'])->name('public.news');
Route::get('/berita/{id}', [BeritaController::class, 'newsDetail'])->name('public.news.detail');
Route::get('/lapor', [AduanController::class, 'aduan'])->name('public.aduan');
Route::post('/lapor', [AduanController::class, 'storeAduan'])->name('public.aduan.store');
Route::post('/lapor/otp/send', [AduanController::class, 'sendAduanOtp'])->name('public.aduan.otp.send');
Route::post('/lapor/otp/verify', [AduanController::class, 'verifyAduanOtp'])->name('public.aduan.otp.verify');

// Internal Data Helper API routes
Route::get('/api/kelurahan', [ApiController::class, 'getKelurahan'])->name('public.api.kelurahan');
Route::get('/api/boundaries', [ApiController::class, 'getBoundaries'])->name('public.api.boundaries');

// Auth Routes
Route::get('/login', [AuthController::class, 'login'])->name('public.login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('public.login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('public.logout');

// Admin Routes (Panel Admin)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Validasi & Status Laporan
    Route::get('/laporan/validasi', [LaporanController::class, 'validasiAduan'])->name('laporan.validasi');
    Route::post('/laporan/{id}/status', [LaporanController::class, 'updateStatus'])->name('laporan.status');
    Route::delete('/laporan/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy');

    // Manajemen Data Korban
    Route::get('/korban', [KorbanController::class, 'dataKorban'])->name('korban.index');
    Route::post('/korban', [KorbanController::class, 'storeKorban'])->name('korban.store');
    Route::put('/korban/{id}', [KorbanController::class, 'updateKorban'])->name('korban.update');
    Route::delete('/korban/{id}', [KorbanController::class, 'destroyKorban'])->name('korban.destroy');

    // Manajemen Data Pengungsian
    Route::get('/pengungsian', [PengungsianController::class, 'dataPengungsian'])->name('pengungsian.index');
    Route::post('/pengungsian', [PengungsianController::class, 'storePengungsian'])->name('pengungsian.store');
    Route::put('/pengungsian/{id}', [PengungsianController::class, 'updatePengungsian'])->name('pengungsian.update');
    Route::delete('/pengungsian/{id}', [PengungsianController::class, 'destroyPengungsian'])->name('pengungsian.destroy');

    // Manajemen Data Kerusakan
    Route::get('/kerusakan', [KerusakanController::class, 'dataKerusakan'])->name('kerusakan.index');
    Route::post('/kerusakan', [KerusakanController::class, 'storeKerusakan'])->name('kerusakan.store');
    Route::put('/kerusakan/{id}', [KerusakanController::class, 'updateKerusakan'])->name('kerusakan.update');
    Route::delete('/kerusakan/{id}', [KerusakanController::class, 'destroyKerusakan'])->name('kerusakan.destroy');

    // Manajemen Logistik & Bantuan: Gudang & Stok Bantuan
    Route::get('/logistik/stok', [LogistikController::class, 'stokBantuan'])->name('logistik.stok');
    Route::post('/logistik/stok', [LogistikController::class, 'storeStokBantuan'])->name('logistik.stok.store');
    Route::put('/logistik/stok/{id}', [LogistikController::class, 'updateStokBantuan'])->name('logistik.stok.update');
    Route::delete('/logistik/stok/{id}', [LogistikController::class, 'destroyStokBantuan'])->name('logistik.stok.destroy');

    // Manajemen Logistik & Bantuan: Distribusi Bantuan
    Route::get('/logistik/distribusi', [LogistikController::class, 'distribusiBantuan'])->name('logistik.distribusi');
    Route::post('/logistik/distribusi', [LogistikController::class, 'storeDistribusiBantuan'])->name('logistik.distribusi.store');
    Route::put('/logistik/distribusi/{id}', [LogistikController::class, 'updateDistribusiBantuan'])->name('logistik.distribusi.update');
    Route::delete('/logistik/distribusi/{id}', [LogistikController::class, 'destroyDistribusiBantuan'])->name('logistik.distribusi.destroy');

    // Manajemen Master Data Jenis Bencana
    Route::get('/bencana', [BencanaController::class, 'dataBencana'])->name('bencana.index');
    Route::post('/bencana', [BencanaController::class, 'storeBencana'])->name('bencana.store');
    Route::put('/bencana/{id}', [BencanaController::class, 'updateBencana'])->name('bencana.update');
    Route::delete('/bencana/{id}', [BencanaController::class, 'destroyBencana'])->name('bencana.destroy');

    // Manajemen Master Data Desa & Kelurahan
    Route::get('/kelurahan', [KelurahanController::class, 'index'])->name('kelurahan.index');
    Route::post('/kelurahan', [KelurahanController::class, 'store'])->name('kelurahan.store');
    Route::put('/kelurahan/{id}', [KelurahanController::class, 'update'])->name('kelurahan.update');
    Route::delete('/kelurahan/{id}', [KelurahanController::class, 'destroy'])->name('kelurahan.destroy');

    // Manajemen Master Data Kecamatan
    Route::get('/kecamatan', [KecamatanController::class, 'index'])->name('kecamatan.index');
    Route::post('/kecamatan', [KecamatanController::class, 'store'])->name('kecamatan.store');
    Route::put('/kecamatan/{id}', [KecamatanController::class, 'update'])->name('kecamatan.update');
    Route::delete('/kecamatan/{id}', [KecamatanController::class, 'destroy'])->name('kecamatan.destroy');

    // Manajemen Zonasi Titik Rawan Bencana
    Route::get('/rawan', [DaerahRawanController::class, 'index'])->name('rawan.index');
    Route::post('/rawan', [DaerahRawanController::class, 'store'])->name('rawan.store');
    Route::put('/rawan/{id}', [DaerahRawanController::class, 'update'])->name('rawan.update');
    Route::delete('/rawan/{id}', [DaerahRawanController::class, 'destroy'])->name('rawan.destroy');
});
