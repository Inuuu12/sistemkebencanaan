<?php

use App\Http\Controllers\PublicPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Kebencanaan Kabupaten Bogor (AKSARA BOGOR)
|--------------------------------------------------------------------------
| Portal Publik Kebencanaan Kabupaten Bogor
*/

Route::get('/', [PublicPortalController::class, 'index'])->name('public.home');
Route::get('/berita', [PublicPortalController::class, 'news'])->name('public.news');
Route::get('/berita/{id}', [PublicPortalController::class, 'newsDetail'])->name('public.news.detail');
Route::get('/lapor', [PublicPortalController::class, 'aduan'])->name('public.aduan');
Route::post('/lapor', [PublicPortalController::class, 'storeAduan'])->name('public.aduan.store');
Route::post('/lapor/otp/send', [PublicPortalController::class, 'sendAduanOtp'])->name('public.aduan.otp.send');
Route::post('/lapor/otp/verify', [PublicPortalController::class, 'verifyAduanOtp'])->name('public.aduan.otp.verify');

// Internal Data Helper API routes
Route::get('/api/kelurahan', [PublicPortalController::class, 'getKelurahan'])->name('public.api.kelurahan');
Route::get('/api/boundaries', [PublicPortalController::class, 'getBoundaries'])->name('public.api.boundaries');

// Auth Routes
Route::get('/login', [PublicPortalController::class, 'login'])->name('public.login');
Route::post('/login', [PublicPortalController::class, 'authenticate'])->name('public.login.store');
Route::post('/logout', [PublicPortalController::class, 'logout'])->name('public.logout');

// Admin Routes (Panel Admin Sederhana & Ringkas)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard']);
    Route::get('/laporan/validasi', [\App\Http\Controllers\AdminController::class, 'validasiAduan'])->name('laporan.validasi');
    Route::post('/laporan/{id}/status', [\App\Http\Controllers\AdminController::class, 'updateStatus'])->name('laporan.status');

    // Manajemen Data Korban
    Route::get('/korban', [\App\Http\Controllers\AdminController::class, 'dataKorban'])->name('korban.index');
    Route::post('/korban', [\App\Http\Controllers\AdminController::class, 'storeKorban'])->name('korban.store');
    Route::put('/korban/{id}', [\App\Http\Controllers\AdminController::class, 'updateKorban'])->name('korban.update');
    Route::delete('/korban/{id}', [\App\Http\Controllers\AdminController::class, 'destroyKorban'])->name('korban.destroy');

    // Manajemen Data Pengungsian
    Route::get('/pengungsian', [\App\Http\Controllers\AdminController::class, 'dataPengungsian'])->name('pengungsian.index');
    Route::post('/pengungsian', [\App\Http\Controllers\AdminController::class, 'storePengungsian'])->name('pengungsian.store');
    Route::put('/pengungsian/{id}', [\App\Http\Controllers\AdminController::class, 'updatePengungsian'])->name('pengungsian.update');
    Route::delete('/pengungsian/{id}', [\App\Http\Controllers\AdminController::class, 'destroyPengungsian'])->name('pengungsian.destroy');

    // Manajemen Data Kerusakan
    Route::get('/kerusakan', [\App\Http\Controllers\AdminController::class, 'dataKerusakan'])->name('kerusakan.index');
    Route::post('/kerusakan', [\App\Http\Controllers\AdminController::class, 'storeKerusakan'])->name('kerusakan.store');
    Route::put('/kerusakan/{id}', [\App\Http\Controllers\AdminController::class, 'updateKerusakan'])->name('kerusakan.update');
    Route::delete('/kerusakan/{id}', [\App\Http\Controllers\AdminController::class, 'destroyKerusakan'])->name('kerusakan.destroy');
});

