<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function create()
    {
        // Menampilkan UI kamera dan GPS
        return view('karyawan.presensi');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $hariIni = Carbon::now()->toDateString();
        $jamSekarang = Carbon::now()->toTimeString();

        // 1. Tangkap Foto Base64 (Sesuaikan nama 'foto_masuk' dengan yang dikirim dari AJAX)
        $fotoBase64 = $request->input('foto_masuk') ?? $request->input('foto_base64');

        // 2. Ubah Base64 Menjadi File Gambar Fisik
        $imageParts = explode(";base64,", $fotoBase64);
        $imageTypeAux = explode("image/", $imageParts[0]);
        $imageType = $imageTypeAux[1];
        $imageBase64 = base64_decode($imageParts[1]);
        
        // 3. Simpan Gambar ke Folder Storage Server
        $tipeAbsen = Absensi::where('user_id', $user->id)->where('tanggal', $hariIni)->exists() ? 'pulang' : 'masuk';
        $fileName = $user->nik . '_' . time() . '_' . $tipeAbsen . '.' . $imageType;
        
        // Simpan ke storage/app/public/absensi/
        Storage::disk('public')->put('absensi/' . $fileName, $imageBase64);
        
        // INI YANG DISIMPAN KE DB: Teks pendek
        $savedPath = 'absensi/' . $fileName; 

        // 4. Proses Simpan ke Database
        $absensi = Absensi::where('user_id', $user->id)
                          ->where('tanggal', $hariIni)
                          ->first();

        if (!$absensi) {
            // Proses Absen Masuk
            Absensi::create([
                'user_id' => $user->id,
                'tanggal' => $hariIni,
                'jam_masuk' => $jamSekarang,
                'koordinat_masuk' => $request->koordinat_masuk, 
                'foto_masuk' => $savedPath, 
                'status_masuk' => 'Tepat Waktu' 
            ]);
            return response()->json(['message' => 'Berhasil Absen Masuk!']);
        } else {
            // Proses Absen Pulang
            $absensi->update([
                'jam_pulang' => $jamSekarang,
                'koordinat_pulang' => $request->koordinat_pulang ?? $request->koordinat_masuk,
                'foto_pulang' => $savedPath, 
            ]);
            return response()->json(['message' => 'Berhasil Absen Pulang!']);
        }
        if (!$lembur) {
            Lembur::create([
                'user_id' => $user->id,
                'tanggal' => $hariIni,
                'jam_mulai' => $jamSekarang,
                'foto_mulai' => $savedPath,
                'koordinat_mulai' => $request->koordinat_masuk,
                'status' => 'Pending' // Bisa diubah jika lembur butuh approval
            ]);
            return response()->json(['message' => 'Berhasil Absen Masuk Lembur!']);
        }

        // ==========================================
        // LOGIKA 4: ABSEN PULANG LEMBUR
        // ==========================================
        if (!$lembur->jam_selesai) {
            $lembur->update([
                'jam_selesai' => $jamSekarang,
                'foto_selesai' => $savedPath,
                'koordinat_selesai' => $request->koordinat_masuk,
            ]);
            return response()->json(['message' => 'Berhasil Absen Pulang Lembur!']);
        }

        // Jika semua absen sudah penuh
        return response()->json(['message' => 'Anda sudah menyelesaikan semua absen reguler dan lembur hari ini.'], 400);
    }

    
}