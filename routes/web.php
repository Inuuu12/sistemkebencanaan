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
    Route::post('/laporan/{id}/status', [\App\Http\Controllers\AdminController::class, 'updateStatus'])->name('laporan.status');
});

