<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanPresensiController;

// Import Controller Karyawan
use App\Http\Controllers\Karyawan\PresensiController;
use App\Http\Controllers\Karyawan\RiwayatController;
use App\Http\Controllers\Karyawan\PengajuanController as KaryawanPengajuan;
use App\Http\Controllers\Karyawan\ProfilController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

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

    // ==========================================
    // AREA KARYAWAN (Role: karyawan)
    // ==========================================
    Route::middleware(['role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
        
        // Kamera & GPS Presensi
        Route::get('/presensi', [PresensiController::class, 'create'])->name('presensi.create');
        Route::post('/presensi', [PresensiController::class, 'store'])->name('presensi.store');

        // Riwayat Presensi Mandiri
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');

        // Pengajuan Izin & Lembur
        Route::get('/pengajuan', [KaryawanPengajuan::class, 'index'])->name('pengajuan.index');
        Route::post('/pengajuan/izin', [KaryawanPengajuan::class, 'storeIzin'])->name('pengajuan.izin.store');
        Route::get('/pengajuan/lembur', [KaryawanPengajuan::class, 'lembur'])->name('pengajuan.lembur');
        Route::post('/pengajuan/lembur', [KaryawanPengajuan::class, 'storeLembur'])->name('pengajuan.lembur.store');

        // Profil Karyawan
        Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    });

});