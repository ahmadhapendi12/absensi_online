<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AgendaLiburToko;
use App\Models\JadwalKaryawan;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        $hariIni = Carbon::now()->toDateString();
        $jadwal = JadwalKaryawan::with('shift')->where('user_id', $user->id)->first();

        // 1. Cek Libur Rutin Mingguan
        $dayMap = [
            'sunday' => 'minggu',
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => 'sabtu',
        ];
        $hariIniId = $dayMap[strtolower(Carbon::now()->format('l'))] ?? 'minggu';
        $isHariLibur = false;
        $pesanLibur = null;
        $tipeLibur = null;

        if ($jadwal && strtolower($jadwal->hari_libur_rutin) === $hariIniId) {
            $isHariLibur = true;
            $tipeLibur = 'rutin';
            $pesanLibur = 'Hari ini adalah jadwal libur rutin mingguan Anda (' . ucfirst($jadwal->hari_libur_rutin) . '). Selamat beristirahat!';
        }

        // 2. Cek Agenda / Libur Toko (Jumsih, Kamsih, Libur Kios)
        $agendaToko = AgendaLiburToko::where('tanggal', $hariIni)
            ->where('status', '!=', 'dibatalkan')
            ->where(function ($q) use ($user) {
                $q->whereNull('cabang_id')
                  ->orWhere('cabang_id', $user->cabang_id);
            })
            ->first();

        if ($agendaToko && !$isHariLibur) {
            $isHariLibur = true;
            $tipeLibur = $agendaToko->jenis;
            $pesanLibur = trim("Hari ini agenda {$agendaToko->nama_agenda}. " . ($agendaToko->keterangan ?? ''));
        }

        return view('karyawan.presensi', compact('jadwal', 'isHariLibur', 'pesanLibur', 'tipeLibur', 'agendaToko'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $hariIni = Carbon::now()->toDateString();
        $jamSekarang = Carbon::now()->toTimeString();

        // Cek Libur Rutin atau Agenda Libur Toko
        $dayMap = [
            'sunday' => 'minggu',
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => 'sabtu',
        ];
        $hariIniId = $dayMap[strtolower(Carbon::now()->format('l'))] ?? 'minggu';
        $jadwal = JadwalKaryawan::with('shift')->where('user_id', $user->id)->first();

        $agendaToko = AgendaLiburToko::where('tanggal', $hariIni)
            ->where('status', '!=', 'dibatalkan')
            ->where(function ($q) use ($user) {
                $q->whereNull('cabang_id')
                  ->orWhere('cabang_id', $user->cabang_id);
            })
            ->first();

        if ($agendaToko && $agendaToko->jenis === 'libur_kios') {
            return response()->json([
                'message' => 'Hari ini outlet sedang libur/tutup (' . $agendaToko->nama_agenda . '). Tidak perlu presensi!'
            ], 422);
        }

        if ($jadwal && strtolower($jadwal->hari_libur_rutin) === $hariIniId) {
            return response()->json([
                'message' => 'Hari ini adalah jadwal libur rutin mingguan Anda (' . ucfirst($jadwal->hari_libur_rutin) . '). Selamat beristirahat!'
            ], 422);
        }

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
            // Ambil penetapan shift karyawan di belakang layar
            $jadwal = JadwalKaryawan::with('shift')->where('user_id', $user->id)->first();
            $statusMasuk = 'Hadir';
            $menitTerlambat = 0;

            if ($jadwal && $jadwal->shift) {
                $shift = $jadwal->shift;
                $jamMasukShift = Carbon::createFromTimeString($shift->jam_masuk);
                $jamMasukUser = Carbon::createFromTimeString($jamSekarang);
                $bukaAbsenMenit = (int) ($shift->buka_absen_menit ?? 30);
                $jamBukaAbsen = $jamMasukShift->copy()->subMinutes($bukaAbsenMenit);

                // Validasi 1: Belum buka absen (mencegah absen kepagian / ngakalin)
                if (!$shift->lintas_hari && $jamMasukUser->lessThan($jamBukaAbsen)) {
                    return response()->json([
                        'message' => 'Presensi masuk belum dibuka! Shift Anda (' . $shift->nama_shift . ') dimulai pukul ' . $jamMasukShift->format('H:i') . ' WIB. Presensi baru dibuka mulai pukul ' . $jamBukaAbsen->format('H:i') . ' WIB (' . $bukaAbsenMenit . ' menit sebelum shift).'
                    ], 422);
                }

                $toleransi = (int) ($shift->toleransi_terlambat_menit ?? 15);
                $batasToleransi = $jamMasukShift->copy()->addMinutes($toleransi);

                if ($jamMasukUser->greaterThan($batasToleransi)) {
                    $statusMasuk = 'Terlambat';
                    $menitTerlambat = $jamMasukShift->diffInMinutes($jamMasukUser);
                } else {
                    $statusMasuk = 'Hadir';
                }
            }

            // Proses Absen Masuk dengan Waktu Tepat (Exact Timestamp)
            Absensi::create([
                'user_id' => $user->id,
                'jadwal_id' => $jadwal?->id,
                'tanggal' => $hariIni,
                'jam_masuk' => $jamSekarang,
                'koordinat_masuk' => $request->koordinat_masuk, 
                'foto_masuk' => $savedPath, 
                'status_masuk' => $statusMasuk,
                'menit_terlambat' => $menitTerlambat,
            ]);
            return response()->json([
                'message' => 'Berhasil Absen Masuk pada ' . Carbon::parse($jamSekarang)->format('H:i') . ' WIB! (' . $statusMasuk . ')'
            ]);
        } else {
            // Cek apakah sudah absen pulang
            if ($absensi->jam_pulang) {
                return response()->json(['message' => 'Anda sudah menyelesaikan presensi masuk dan pulang hari ini.'], 400);
            }

            // Validasi 2: Pulang terlalu awal
            $jadwal = JadwalKaryawan::with('shift')->where('user_id', $user->id)->first();
            if ($jadwal && $jadwal->shift) {
                $shift = $jadwal->shift;
                $jamPulangShift = Carbon::createFromTimeString($shift->jam_pulang);
                $jamPulangUser = Carbon::createFromTimeString($jamSekarang);
                $toleransiPulang = (int) ($shift->toleransi_pulang_menit ?? 0);
                $batasBolehPulang = $jamPulangShift->copy()->subMinutes($toleransiPulang);

                if (!$shift->lintas_hari && $jamPulangUser->lessThan($batasBolehPulang)) {
                    return response()->json([
                        'message' => 'Belum saatnya presensi pulang! Shift Anda berakhir pukul ' . $jamPulangShift->format('H:i') . ' WIB. Mohon selesaikan jam kerja Anda terlebih dahulu.'
                    ], 422);
                }
            }

            // Proses Absen Pulang
            $absensi->update([
                'jam_pulang' => $jamSekarang,
                'koordinat_pulang' => $request->koordinat_pulang ?? $request->koordinat_masuk,
                'foto_pulang' => $savedPath, 
            ]);
            return response()->json([
                'message' => 'Berhasil Absen Pulang pada ' . Carbon::parse($jamSekarang)->format('H:i') . ' WIB! Terima kasih atas kerja keras Anda.'
            ]);
        }
    }

    
}