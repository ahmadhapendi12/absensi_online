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

    // Endpoint Verifikasi Biometrik Wajah & Presensi Masuk / Pulang
    Route::post('/karyawan/presensi', function (\Illuminate\Http\Request $request, \App\Services\BiometricFaceVerificationService $service) {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            $fotoBase64 = $request->input('foto_base64') ?? $request->input('foto_masuk');
            $koordinat = $request->input('koordinat_masuk') ?? $request->input('koordinat') ?? '';
            $liveDescriptor = $request->input('face_descriptor');

            if (!$fotoBase64) {
                return response()->json(['message' => 'Foto wajah dari kamera tidak terkirim. Pastikan kamera menyala.'], 422);
            }

            $result = $service->prosesPresensi($user, $fotoBase64, $koordinat, $liveDescriptor);
            return response()->json($result, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return response()->json(['message' => implode(' ', $errors)], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    })->name('karyawan.presensi.store');

    // Endpoint Verifikasi Biometrik Wajah & Presensi Masuk / Pulang Lembur
    Route::post('/karyawan/lembur/presensi', function (\Illuminate\Http\Request $request, \App\Services\BiometricFaceVerificationService $service) {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            $lemburId = $request->input('lembur_id');

            if (!$lemburId) {
                return response()->json(['message' => 'ID Pengajuan lembur tidak ditemukan.'], 422);
            }

            $lembur = \App\Models\Lembur::where('id', $lemburId)
                ->where('user_id', $user->id)
                ->first();

            if (!$lembur) {
                return response()->json(['message' => 'Data pengajuan lembur tidak ditemukan.'], 404);
            }

            $fotoBase64 = $request->input('foto_base64');
            $koordinat = $request->input('koordinat') ?? '';
            $liveDescriptor = $request->input('face_descriptor');

            if (!$fotoBase64) {
                return response()->json(['message' => 'Foto wajah dari kamera tidak terkirim. Pastikan kamera menyala.'], 422);
            }

            $result = $service->prosesPresensiLembur($user, $lembur, $fotoBase64, $koordinat, $liveDescriptor);
            return response()->json($result, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return response()->json(['message' => implode(' ', $errors)], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    })->name('karyawan.lembur.presensi.store');

    // Shortcut Redirect untuk Lembur Karyawan (Mencegah 404)
    Route::get('/karyawan/lemburs', function () {
        return redirect()->to(\App\Filament\Karyawan\Resources\Lemburs\LemburKaryawanResource::getUrl('index', panel: 'karyawan'));
    });

});