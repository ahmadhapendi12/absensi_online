<?php

namespace App\Services;

use App\Models\User;
use App\Models\Absensi;
use App\Models\JadwalKaryawan;
use App\Models\AgendaLiburToko;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BiometricFaceVerificationService
{
    /**
     * Threshold jarak Euclidean maksimum untuk verifikasi kecocokan wajah.
     * Nilai 0.48 adalah standar ketat Face-API (jarak <= 0.48 menjamin kecocokan biometrik orang yang sama).
     */
    public const FACE_DISTANCE_THRESHOLD = 0.48;

    /**
     * Memproses verifikasi biometrik wajah dan mencatat presensi (masuk / pulang).
     *
     * @param User $user
     * @param string $fotoBase64
     * @param string $koordinat
     * @param string|null $liveDescriptorJson
     * @return array
     * @throws ValidationException
     */
    public function prosesPresensi(User $user, string $fotoBase64, string $koordinat, ?string $liveDescriptorJson = null): array
    {
        $hariIni = Carbon::today()->toDateString();
        $jamSekarang = Carbon::now()->toTimeString();

        // 1. Verifikasi Biometrik Wajah dan Lokasi Kantor secara Terpusat
        $this->verifikasiBiometrikDanLokasi($user, $koordinat, $liveDescriptorJson);

        // 2. Validasi Jadwal Libur Rutin dan Agenda Toko
        $this->validasiBukanHariLibur($user, $hariIni);

        // 5. Decode dan Simpan Foto Fisik Presensi Kamera ke Storage
        $savedPhotoPath = $this->simpanFotoFisik($fotoBase64, $user, $hariIni);

        // 6. Simpan / Perbarui Entity Absensi (Masuk atau Pulang)
        $result = $this->simpanKeEntityAbsensi($user, $hariIni, $jamSekarang, $koordinat, $savedPhotoPath);

        // 7. Pembersihan Otomatis Foto Fisik Lama (> 30 Hari / 1 Bulan)
        $this->pruneFotoLamaLebihDariSatuBulan();

        return $result;
    }

    /**
     * Hitung Jarak Euclidean antara dua vektor deskriptor wajah (128-dimensi).
     */
    public function calculateEuclideanDistance(array $vectorA, array $vectorB): float
    {
        $sum = 0.0;
        $count = count($vectorA);
        for ($i = 0; $i < $count; $i++) {
            $diff = (float)$vectorA[$i] - (float)$vectorB[$i];
            $sum += $diff * $diff;
        }
        return sqrt($sum);
    }

    /**
     * Hitung jarak meter geografis menggunakan rumus Haversine.
     */
    public function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Verifikasi biometrik vektor wajah terhadap master acuan profil karyawan dan validasi GPS kantor.
     * Digunakan bersama baik untuk Presensi Reguler Shift maupun Presensi Lembur agar akurasi 100% konsisten.
     *
     * @param User $user
     * @param string $koordinat
     * @param string|null $liveDescriptorJson
     * @return array
     * @throws ValidationException
     */
    public function verifikasiBiometrikDanLokasi(User $user, string $koordinat, ?string $liveDescriptorJson): array
    {
        // 1. Validasi Keberadaan Data Biometrik Master Karyawan
        $profile = $user->profile;
        if (!$profile || !$profile->foto_wajah_acuan) {
            throw ValidationException::withMessages([
                'biometrik' => 'Data biometrik wajah belum terdaftar. Silakan lakukan perekaman wajah pada menu Profil Karyawan terlebih dahulu.',
            ]);
        }

        if (empty($profile->face_descriptor)) {
            throw ValidationException::withMessages([
                'biometrik' => 'Data vektor biometrik acuan belum lengkap. Silakan rekam ulang wajah pada menu Profil Karyawan.',
            ]);
        }

        // 2. Evaluasi Kecocokan Biometrik Vektor Wajah Secara Ketat
        if (empty($liveDescriptorJson)) {
            throw ValidationException::withMessages([
                'biometrik' => 'Biometrik wajah tidak terdeteksi pada kamera saat presensi. Pastikan seluruh wajah Anda menghadap lurus ke kamera.',
            ]);
        }

        $masterVector = json_decode($profile->face_descriptor, true);
        $liveVector = json_decode($liveDescriptorJson, true);

        if (!is_array($masterVector) || !is_array($liveVector) || count($masterVector) !== 128 || count($liveVector) !== 128) {
            throw ValidationException::withMessages([
                'biometrik' => 'Format data biometrik tidak valid. Silakan ulangi pemindaian kamera.',
            ]);
        }

        $distance = $this->calculateEuclideanDistance($masterVector, $liveVector);

        // Jika jarak Euclidean melebihi threshold 0.48, tolak presensi
        if ($distance > self::FACE_DISTANCE_THRESHOLD) {
            throw ValidationException::withMessages([
                'biometrik' => 'Verifikasi biometrik gagal: Wajah tidak sesuai dengan data acuan terdaftar (Tingkat kemiripan di bawah standar ketat Lazatto).',
            ]);
        }

        // 3. Validasi Lokasi Koordinat GPS Terhadap Radius Kantor Cabang
        $this->validasiLokasiKantor($user, $koordinat);

        return [
            'distance' => round($distance, 4),
            'match' => true,
        ];
    }

    /**
     * Validasi lokasi koordinat GPS terhadap cabang penempatan.
     */
    protected function validasiLokasiKantor(User $user, string $koordinat): void
    {
        $cabang = $user->cabang;
        if (!$cabang || !$cabang->latitude || !$cabang->longitude) {
            return;
        }

        $coords = explode(',', trim($koordinat));
        if (count($coords) !== 2 || !is_numeric($coords[0]) || !is_numeric($coords[1])) {
            return;
        }

        $userLat = (float) $coords[0];
        $userLng = (float) $coords[1];
        $targetLat = (float) $cabang->latitude;
        $targetLng = (float) $cabang->longitude;
        $targetRadius = (int) ($cabang->radius_meter ?? 50);

        $jarakMeter = $this->calculateHaversineDistance($userLat, $userLng, $targetLat, $targetLng);

        // Beri toleransi deviasi akurasi GPS (+50 meter) pada perangkat mobile di dalam toko/gedung
        $toleransiRadius = $targetRadius + 50;

        // Pada environment local/testing, toleransi diizinkan agar memudahkan pengujian
        if (!app()->environment('local') && $jarakMeter > $toleransiRadius) {
            throw ValidationException::withMessages([
                'lokasi' => "Posisi Anda berada di luar radius kantor (" . round($jarakMeter) . " meter dari titik kantor). Presensi harus dilakukan dalam radius {$targetRadius} meter.",
            ]);
        }
    }

    /**
     * Validasi apakah hari ini bukan hari libur karyawan atau libur outlet.
     */
    protected function validasiBukanHariLibur(User $user, string $hariIni): void
    {
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

        // Cek Libur Rutin Mingguan
        if ($jadwal && strtolower($jadwal->hari_libur_rutin) === $hariIniId) {
            throw ValidationException::withMessages([
                'jadwal' => 'Hari ini merupakan hari libur mingguan Anda (' . ucfirst($jadwal->hari_libur_rutin) . '). Presensi tidak diperlukan.',
            ]);
        }

        // Cek Agenda Libur Toko
        $agendaToko = AgendaLiburToko::where('tanggal', $hariIni)
            ->where('status', '!=', 'dibatalkan')
            ->where(function ($q) use ($user) {
                $q->whereNull('cabang_id')
                  ->orWhere('cabang_id', $user->cabang_id);
            })
            ->first();

        if ($agendaToko && $agendaToko->jenis === 'libur_kios') {
            throw ValidationException::withMessages([
                'jadwal' => 'Hari ini outlet libur (' . $agendaToko->nama_agenda . '). Presensi tidak diperlukan.',
            ]);
        }
    }

    /**
     * Decode base64 foto kamera dan simpan berkas fisik ke disk public storage.
     */
    protected function simpanFotoFisik(string $fotoBase64, User $user, string $hariIni): string
    {
        if (!str_contains($fotoBase64, 'base64,')) {
            throw ValidationException::withMessages([
                'foto' => 'Format foto kamera tidak valid. Silakan ulangi pengambilan foto wajah.',
            ]);
        }

        $imageParts = explode(";base64,", $fotoBase64);
        $imageTypeAux = explode("image/", $imageParts[0]);
        $imageType = $imageTypeAux[1] ?? 'jpg';
        if ($imageType === 'jpeg') {
            $imageType = 'jpg';
        }
        $imageBase64 = base64_decode($imageParts[1]);

        $tipeAbsen = Absensi::where('user_id', $user->id)->where('tanggal', $hariIni)->exists() ? 'pulang' : 'masuk';
        $nik = $user->profile?->nik ?? $user->id;
        $fileName = 'presensi_' . $nik . '_' . time() . '_' . $tipeAbsen . '.' . $imageType;
        $savedPath = 'absensi/' . $fileName;

        Storage::disk('public')->put($savedPath, $imageBase64);

        return $savedPath;
    }

    /**
     * Simpan record absensi masuk atau absen pulang ke database.
     */
    protected function simpanKeEntityAbsensi(
        User $user, 
        string $hariIni, 
        string $jamSekarang, 
        string $koordinat, 
        string $savedPhotoPath
    ): array {
        $absensi = Absensi::where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->first();

        $jadwal = JadwalKaryawan::with('shift')->where('user_id', $user->id)->first();
        $shift = $jadwal?->shift;

        // KASUS 1: ABSEN MASUK
        if (!$absensi) {
            $statusMasuk = 'Hadir';
            $menitTerlambat = 0;

            if ($shift) {
                $jamMasukShift = Carbon::createFromTimeString($shift->jam_masuk);
                $jamMasukUser = Carbon::createFromTimeString($jamSekarang);
                $bukaAbsenMenit = (int) ($shift->buka_absen_menit ?? 30);
                $jamBukaAbsen = $jamMasukShift->copy()->subMinutes($bukaAbsenMenit);

                // Validasi belum buka absen
                if (!$shift->lintas_hari && $jamMasukUser->lessThan($jamBukaAbsen)) {
                    Storage::disk('public')->delete($savedPhotoPath);
                    throw ValidationException::withMessages([
                        'shift' => 'Presensi masuk belum dibuka. Shift ' . $shift->nama_shift . ' dapat dimulai pada pukul ' . $jamBukaAbsen->format('H:i') . ' WIB.',
                    ]);
                }

                $toleransi = (int) ($shift->toleransi_terlambat_menit ?? 15);
                $batasToleransi = $jamMasukShift->copy()->addMinutes($toleransi);

                if ($jamMasukUser->greaterThan($batasToleransi)) {
                    $statusMasuk = 'Terlambat';
                    // CAST DENGAN TEGAS KE INTEGER (Mencegah PostgreSQL invalid syntax integer "562.1")
                    $menitTerlambat = (int) round($jamMasukShift->diffInMinutes($jamMasukUser));
                } else {
                    $statusMasuk = 'Hadir';
                    $menitTerlambat = 0;
                }
            }

            $record = Absensi::create([
                'user_id' => $user->id,
                'jadwal_id' => $jadwal?->id,
                'tanggal' => $hariIni,
                'jam_masuk' => $jamSekarang,
                'koordinat_masuk' => $koordinat,
                'foto_masuk' => $savedPhotoPath,
                'status_masuk' => $statusMasuk,
                'menit_terlambat' => (int) $menitTerlambat,
            ]);

            return [
                'status' => 'success',
                'action' => 'masuk',
                'status_masuk' => $statusMasuk,
                'jam' => Carbon::parse($jamSekarang)->format('H:i') . ' WIB',
                'message' => "Presensi masuk berhasil dicatat ({$statusMasuk}).",
                'record' => $record,
            ];
        }

        // KASUS 2: ABSEN PULANG
        if ($absensi->jam_pulang) {
            Storage::disk('public')->delete($savedPhotoPath);
            throw ValidationException::withMessages([
                'absensi' => 'Presensi masuk dan pulang untuk hari ini telah selesai.',
            ]);
        }

        // Validasi waktu pulang
        if ($shift) {
            $jamPulangShift = Carbon::createFromTimeString($shift->jam_pulang);
            $jamPulangUser = Carbon::createFromTimeString($jamSekarang);
            $toleransiPulang = (int) ($shift->toleransi_pulang_menit ?? 0);
            $batasBolehPulang = $jamPulangShift->copy()->subMinutes($toleransiPulang);

            if (!$shift->lintas_hari && $jamPulangUser->lessThan($batasBolehPulang)) {
                Storage::disk('public')->delete($savedPhotoPath);
                throw ValidationException::withMessages([
                    'shift' => 'Belum memasuki jam pulang. Jam kerja shift berakhir pada pukul ' . $jamPulangShift->format('H:i') . ' WIB.',
                ]);
            }
        }

        $absensi->update([
            'jam_pulang' => $jamSekarang,
            'koordinat_pulang' => $koordinat,
            'foto_pulang' => $savedPhotoPath,
        ]);

        return [
            'status' => 'success',
            'action' => 'pulang',
            'jam' => Carbon::parse($jamSekarang)->format('H:i') . ' WIB',
            'message' => "Presensi pulang berhasil dicatat.",
            'record' => $absensi,
        ];
    }

    /**
     * Bersihkan berkas fisik foto presensi yang lebih lama dari 30 hari (1 bulan)
     * agar tidak membebani kapasitas disk server VPS.
     * Riwayat tanggal, jam kerja, status, dan menit lembur/terlambat tetap utuh.
     */
    public function pruneFotoLamaLebihDariSatuBulan(int $limit = 25): int
    {
        $cutoffDate = Carbon::now()->subDays(30)->toDateString();

        $oldRecords = Absensi::where('tanggal', '<', $cutoffDate)
            ->where(function ($q) {
                $q->whereNotNull('foto_masuk')
                  ->orWhereNotNull('foto_pulang');
            })
            ->limit($limit)
            ->get();

        $deletedCount = 0;
        foreach ($oldRecords as $record) {
            if ($record->foto_masuk) {
                if (Storage::disk('public')->exists($record->foto_masuk)) {
                    Storage::disk('public')->delete($record->foto_masuk);
                    $deletedCount++;
                }
                $record->update(['foto_masuk' => null]);
            }

            if ($record->foto_pulang) {
                if (Storage::disk('public')->exists($record->foto_pulang)) {
                    Storage::disk('public')->delete($record->foto_pulang);
                    $deletedCount++;
                }
                $record->update(['foto_pulang' => null]);
            }
        }

        return $deletedCount;
    }

    /**
     * Memproses verifikasi biometrik wajah dan GPS untuk presensi lembur (masuk / pulang).
     *
     * @param User $user
     * @param \App\Models\Lembur $lembur
     * @param string $fotoBase64
     * @param string $koordinat
     * @param string|null $liveDescriptorJson
     * @return array
     * @throws ValidationException
     */
    public function prosesPresensiLembur(
        User $user, 
        \App\Models\Lembur $lembur, 
        string $fotoBase64, 
        string $koordinat, 
        ?string $liveDescriptorJson = null
    ): array {
        if ($lembur->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'lembur' => 'Data pengajuan lembur ini bukan milik akun Anda.',
            ]);
        }

        if ($lembur->status_pengajuan !== 'Disetujui') {
            throw ValidationException::withMessages([
                'lembur' => 'Pengajuan lembur ini belum disetujui oleh atasan/admin.',
            ]);
        }

        if ($lembur->foto_mulai && $lembur->foto_selesai) {
            throw ValidationException::withMessages([
                'lembur' => 'Sesi lembur ini sudah selesai dan presensi masuk maupun pulang telah tercatat lengkap.',
            ]);
        }

        // 1. Verifikasi Biometrik Wajah dan Lokasi Kantor secara Terpusat (Standar Ketat 0.48)
        $this->verifikasiBiometrikDanLokasi($user, $koordinat, $liveDescriptorJson);

        // 4. Decode dan Simpan Foto Kamera ke Disk Storage
        if (!str_contains($fotoBase64, 'base64,')) {
            throw ValidationException::withMessages([
                'foto' => 'Format foto kamera tidak valid. Silakan ulangi pengambilan foto wajah.',
            ]);
        }

        $imageParts = explode(";base64,", $fotoBase64);
        $imageTypeAux = explode("image/", $imageParts[0]);
        $imageType = $imageTypeAux[1] ?? 'jpg';
        if ($imageType === 'jpeg') {
            $imageType = 'jpg';
        }
        $imageBase64 = base64_decode($imageParts[1]);

        $isPulang = !empty($lembur->foto_mulai);
        $tipeAbsen = $isPulang ? 'selesai' : 'mulai';
        $nik = $user->profile?->nik ?? $user->id;
        $fileName = 'lembur_' . $nik . '_' . time() . '_' . $tipeAbsen . '.' . $imageType;
        $savedPath = 'lembur/' . $fileName;

        Storage::disk('public')->put($savedPath, $imageBase64);

        $jamSekarang = Carbon::now()->format('H:i');
        $hariIni = Carbon::today()->toDateString();

        if (!$isPulang) {
            // Validasi 1: Tanggal Pelaksanaan Lembur
            if ($hariIni < $lembur->tanggal) {
                $tglFormatted = Carbon::parse($lembur->tanggal)->translatedFormat('d F Y');
                throw ValidationException::withMessages([
                    'jadwal' => "Presensi masuk lembur belum dibuka. Sesi lembur Anda dijadwalkan pada tanggal {$tglFormatted}.",
                ]);
            }

            // Validasi 2: Jam Mulai Lembur (Wajib tepat atau setelah jam mulai jadwal pengajuan, misal 10:00 wajib >= 10:00)
            if ($hariIni === $lembur->tanggal && $lembur->jam_mulai_aktual) {
                $jamMulaiRencana = substr($lembur->jam_mulai_aktual, 0, 5);
                if ($jamSekarang < $jamMulaiRencana) {
                    throw ValidationException::withMessages([
                        'jadwal' => "Presensi masuk lembur belum dapat dilakukan. Anda wajib melakukan presensi masuk mulai pukul {$jamMulaiRencana} WIB atau setelahnya (saat ini masih pukul {$jamSekarang} WIB).",
                    ]);
                }
            }

            // Presensi Masuk Lembur
            $lembur->update([
                'jam_mulai_aktual' => $jamSekarang,
                'foto_mulai' => $savedPath,
                'koordinat_mulai' => $koordinat,
            ]);

            return [
                'status' => 200,
                'tipe' => 'mulai',
                'jam' => $jamSekarang,
                'message' => "Presensi masuk lembur berhasil dicatat pada {$jamSekarang} WIB. Selamat bekerja!",
            ];
        } else {
            // Presensi Pulang Lembur
            $jamMulai = Carbon::parse($lembur->tanggal . ' ' . $lembur->jam_mulai_aktual);
            $jamSelesai = Carbon::now();
            if ($jamSelesai->lessThan($jamMulai)) {
                $jamSelesai->addDay();
            }
            $durasiMenit = max(0, (int) $jamMulai->diffInMinutes($jamSelesai));
            $durasiJam = round($durasiMenit / 60, 1);

            $lembur->update([
                'jam_selesai_aktual' => $jamSekarang,
                'foto_selesai' => $savedPath,
                'koordinat_selesai' => $koordinat,
                'durasi_menit' => $durasiMenit,
            ]);

            return [
                'status' => 200,
                'tipe' => 'selesai',
                'jam' => $jamSekarang,
                'durasi_menit' => $durasiMenit,
                'durasi_jam' => $durasiJam,
                'message' => "Presensi pulang lembur berhasil dicatat pada {$jamSekarang} WIB. Total durasi: {$durasiJam} Jam ({$durasiMenit} Menit). Terima kasih!",
            ];
        }
    }
}
