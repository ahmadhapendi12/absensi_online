<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Import Controller Superadmin
use App\Http\Controllers\Superadmin\KaryawanController;
use App\Http\Controllers\Superadmin\CabangController;
use App\Http\Controllers\Superadmin\ShiftController;
use App\Http\Controllers\Superadmin\JadwalKaryawanController;
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

// Redirect Default
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->middleware('throttle:5,1')->name('login.post');
});

// Authenticated Routes (Sudah Login)
Route::middleware(['auth'])->group(function () {

    // Logout & Dashboard Pengarah
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // AREA SUPERADMIN (Role: superadmin / super_admin)
    // ==========================================
    Route::middleware(['role:super_admin|superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        
        // Kelola Karyawan
        Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
        Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
        Route::put('/karyawan/{id}', [KaryawanController::class, 'update'])->name('karyawan.update');
        Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');
        
        // Kelola Cabang/Lokasi GPS
        Route::get('/cabang', [CabangController::class, 'index'])->name('cabang.index');
        Route::post('/cabang', [CabangController::class, 'store'])->name('cabang.store');
        Route::delete('/cabang/{id}', [CabangController::class, 'destroy'])->name('cabang.destroy');

        // Kelola Master Shift Kerja
        Route::get('/shift', [ShiftController::class, 'index'])->name('shift.index');
        Route::post('/shift', [ShiftController::class, 'store'])->name('shift.store');
        Route::put('/shift/{id}', [ShiftController::class, 'update'])->name('shift.update');
        Route::delete('/shift/{id}', [ShiftController::class, 'destroy'])->name('shift.destroy');

        // Kelola Jadwal Karyawan
        Route::get('/jadwal', [JadwalKaryawanController::class, 'index'])->name('jadwal.index');
        Route::post('/jadwal', [JadwalKaryawanController::class, 'store'])->name('jadwal.store');
        Route::delete('/jadwal/{id}', [JadwalKaryawanController::class, 'destroy'])->name('jadwal.destroy');

        // Approval Pengajuan Izin & Lembur
        Route::get('/pengajuan', [SuperadminPengajuan::class, 'index'])->name('pengajuan.index');
        Route::post('/pengajuan/izin/{id}/approve', [SuperadminPengajuan::class, 'approveIzin'])->name('pengajuan.izin.approve');
        Route::post('/pengajuan/izin/{id}/reject', [SuperadminPengajuan::class, 'rejectIzin'])->name('pengajuan.izin.reject');
        Route::post('/pengajuan/lembur/{id}/approve', [SuperadminPengajuan::class, 'approveLembur'])->name('pengajuan.lembur.approve');
        Route::post('/pengajuan/lembur/{id}/reject', [SuperadminPengajuan::class, 'rejectLembur'])->name('pengajuan.lembur.reject');

        // Laporan Rekap & Export PDF
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::post('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    });

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