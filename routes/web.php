<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Import Controller Superadmin
use App\Http\Controllers\Superadmin\KaryawanController;
use App\Http\Controllers\Superadmin\CabangController;
use App\Http\Controllers\Superadmin\PengajuanController as SuperadminPengajuan;
use App\Http\Controllers\Superadmin\LaporanController;

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

// Rute Default: Arahkan langsung ke halaman login (Breeze)
Route::get('/', function () {
    return redirect('/login');
});
use App\Http\Controllers\AuthController;

// Rute untuk Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
});

// Rute untuk Logout (Harus Login Dulu)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
// Grup Rute Wajib Login (Authentication)
Route::middleware(['auth'])->group(function () {
    
    // Otak pengarah otomatis saat baru login
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // AREA SUPERADMIN (Digembok Satpam Role: superadmin)
    // ==========================================
    Route::middleware(['role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        
        // Rute Karyawan
        Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
        Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
        
        // Rute Cabang/Lokasi GPS
        Route::get('/cabang', [CabangController::class, 'index'])->name('cabang.index');
        Route::post('/cabang', [CabangController::class, 'store'])->name('cabang.store');

        // Rute Approval Pengajuan & Lembur
        Route::get('/pengajuan', [SuperadminPengajuan::class, 'index'])->name('pengajuan.index');
        Route::post('/pengajuan/izin/{id}/approve', [SuperadminPengajuan::class, 'approveIzin'])->name('pengajuan.izin.approve');
        Route::post('/pengajuan/lembur/{id}/approve', [SuperadminPengajuan::class, 'approveLembur'])->name('pengajuan.lembur.approve');

        // Rute Laporan & Cetak PDF
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::post('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    });

    // ==========================================
    // AREA KARYAWAN (Digembok Satpam Role: karyawan)
    // ==========================================
    Route::middleware(['role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
        
        // Rute Inti: Kamera & GPS (Presensi)
        Route::get('/presensi', [PresensiController::class, 'create'])->name('presensi.create');
        Route::post('/presensi', [PresensiController::class, 'store'])->name('presensi.store');

        // Rute Riwayat Mandiri
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');

        // Rute Pengajuan
        Route::get('/pengajuan', [KaryawanPengajuan::class, 'index'])->name('pengajuan.index');
        Route::post('/pengajuan/izin', [KaryawanPengajuan::class, 'storeIzin'])->name('pengajuan.izin.store');

        // Rute Profil
        Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    });

});