<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanPresensiController;

// Redirect Default
Route::get('/', function () {
    return redirect()->route('login');
});

// Redirect /admin/login ke satu wadah /login
Route::get('/admin/login', function () {
    return redirect()->route('login');
});

// Guest Routes (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->middleware('throttle:10,1')->name('login.post');
});

// Authenticated Routes (Sudah Login)
Route::middleware(['auth'])->group(function () {

    // Logout & Dashboard Pengarah
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Export Laporan Rekap Presensi PDF (Bisa diakses Superadmin & Karyawan)
    Route::get('/laporan/export-pdf', [LaporanPresensiController::class, 'exportPdf'])->name('laporan.export-pdf');

});