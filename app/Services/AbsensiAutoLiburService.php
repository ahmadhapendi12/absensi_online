<?php

namespace App\Services;

use App\Models\User;
use App\Models\Absensi;
use App\Models\JadwalKaryawan;
use App\Models\PengajuanIzin;
use Carbon\Carbon;

class AbsensiAutoLiburService
{
    /**
     * Memeriksa dan mencatat status 'Libur' otomatis bagi karyawan yang tidak masuk
     * atau sudah melewati jam operasional shift kerja mereka.
     *
     * @param string|null $targetDate (default: hari ini)
     * @param User|null $targetUser (default: semua karyawan aktif)
     * @return int Jumlah record yang diperbarui/dibuat
     */
    public function syncLibur(?string $targetDate = null, ?User $targetUser = null): int
    {
        $today = Carbon::today()->toDateString();
        $date = $targetDate ?? $today;
        $now = Carbon::now();
        $isToday = ($date === $today);

        $query = User::role('karyawan');
        if ($targetUser) {
            $query->where('id', $targetUser->id);
        }

        $karyawans = $query->with('jadwal.shift')->get();
        $count = 0;

        foreach ($karyawans as $user) {
            $shift = $user->jadwal?->shift;
            if (!$shift) {
                continue;
            }

            // Cek apakah jam kerja shift untuk tanggal ini sudah berakhir
            $shiftEnded = false;

            if ($date < $today) {
                // Untuk hari-hari lampau, shift jelas sudah berakhir
                $shiftEnded = true;
            } elseif ($isToday) {
                if ($shift->lintas_hari) {
                    // Shift lintas hari (misal 23:00 - 07:00):
                    // Sesi yang dimulai HARI INI jam 23:00 baru akan berakhir BESOK jam 07:00.
                    $shiftEnded = false;
                } else {
                    // Shift normal (misal 07:00 - 15:00 atau 15:00 - 23:00):
                    // Berakhir jika jam sekarang >= jam_pulang shift
                    $jamPulangCarbon = Carbon::createFromTimeString($shift->jam_pulang);
                    if ($now->greaterThanOrEqualTo($jamPulangCarbon)) {
                        $shiftEnded = true;
                    }
                }
            }

            // Khusus shift lintas hari yang dimulai KEMARIN:
            // Jika kemarin dimulai jam 23:00, jam 07:00 pagi hari ini shiftnya berakhir.
            if ($isToday && $shift->lintas_hari) {
                $kemarin = Carbon::yesterday()->toDateString();
                $jamPulangPagi = Carbon::createFromTimeString($shift->jam_pulang); // misal 07:00
                if ($now->greaterThanOrEqualTo($jamPulangPagi)) {
                    if ($this->checkAndMarkLibur($user, $kemarin, $user->jadwal?->id)) {
                        $count++;
                    }
                }
            }

            if ($shiftEnded) {
                if ($this->checkAndMarkLibur($user, $date, $user->jadwal?->id)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Tandai karyawan sebagai Libur jika belum ada absensi atau izin pada tanggal tertentu.
     */
    public function checkAndMarkLibur(User $user, string $date, ?string $jadwalId): bool
    {
        $existing = Absensi::where('user_id', $user->id)
            ->where('tanggal', $date)
            ->first();

        // Jika sudah ada absensi dengan jam_masuk atau sudah ada status resmi (Hadir/Terlambat/Izin), lewati
        if ($existing && ($existing->jam_masuk || in_array($existing->status_masuk, ['Hadir', 'Terlambat', 'Izin', 'Cuti', 'Sakit']))) {
            return false;
        }

        // Cek apakah ada pengajuan izin yang disetujui pada tanggal ini
        $approvedIzin = PengajuanIzin::where('user_id', $user->id)
            ->where('status', 'Disetujui')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date)
            ->first();

        if ($approvedIzin) {
            Absensi::updateOrCreate(
                ['user_id' => $user->id, 'tanggal' => $date],
                [
                    'jadwal_id' => $jadwalId,
                    'pengajuan_izin_id' => $approvedIzin->id,
                    'status_masuk' => $approvedIzin->jenis,
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'menit_terlambat' => 0,
                ]
            );
            return true;
        }

        // Jika tidak ada izin dan tidak ada absensi masuk -> catat sebagai Libur
        Absensi::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => $date],
            [
                'jadwal_id' => $jadwalId,
                'status_masuk' => 'Libur',
                'jam_masuk' => null,
                'jam_pulang' => null,
                'menit_terlambat' => 0,
            ]
        );

        return true;
    }
}
