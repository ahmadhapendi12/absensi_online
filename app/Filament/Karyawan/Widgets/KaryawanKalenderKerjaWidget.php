<?php

namespace App\Filament\Karyawan\Widgets;

use Filament\Widgets\Widget;
use App\Models\Absensi;
use App\Models\AgendaLiburToko;
use App\Models\JadwalKaryawan;
use App\Models\Lembur;
use App\Models\PengajuanIzin;
use App\Models\RiwayatJadwalKaryawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KaryawanKalenderKerjaWidget extends Widget
{
    protected string $view = 'filament.karyawan.widgets.karyawan-kalender-kerja-widget';

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public int $year;
    public int $month;
    public ?string $selectedDate = null;
    public ?array $selectedDateDetail = null;

    public function mount(): void
    {
        $this->year = Carbon::now()->year;
        $this->month = Carbon::now()->month;
    }

    public function prevMonth(): void
    {
        $dt = Carbon::createFromDate($this->year, $this->month, 1)->subMonth();
        $this->year = $dt->year;
        $this->month = $dt->month;
        $this->selectedDateDetail = null;
    }

    public function nextMonth(): void
    {
        $dt = Carbon::createFromDate($this->year, $this->month, 1)->addMonth();
        $this->year = $dt->year;
        $this->month = $dt->month;
        $this->selectedDateDetail = null;
    }

    public function goToToday(): void
    {
        $this->year = Carbon::now()->year;
        $this->month = Carbon::now()->month;
        $this->selectDate(Carbon::today()->toDateString());
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $user = Auth::user();

        // Tanggal mulai terdaftar / bergabung karyawan
        $tglMulaiKerja = $user->profile?->tanggal_bergabung 
            ? Carbon::parse($user->profile->tanggal_bergabung)->toDateString()
            : ($user->created_at ? Carbon::parse($user->created_at)->toDateString() : '2026-01-01');

        $isBeforeRegistered = $date < $tglMulaiKerja;
        $carbonDate = Carbon::parse($date);

        // Ambil riwayat jadwal historis
        $riwayatJadwals = RiwayatJadwalKaryawan::with('shift')
            ->where('user_id', $user->id)
            ->orderBy('berlaku_dari', 'asc')
            ->get();

        $jadwalCurrent = JadwalKaryawan::with('shift')->where('user_id', $user->id)->first();

        // Cari riwayat jadwal yang berlaku pada tanggal ini
        $history = $riwayatJadwals->first(function ($r) use ($date) {
            $dari = $r->berlaku_dari ? Carbon::parse($r->berlaku_dari)->toDateString() : null;
            $sampai = $r->berlaku_sampai ? Carbon::parse($r->berlaku_sampai)->toDateString() : null;
            return ($dari === null || $date >= $dari) && ($sampai === null || $date <= $sampai);
        });

        $effectiveShift = $history?->shift ?? $jadwalCurrent?->shift;
        $effectiveLiburRutinDay = $history?->hari_libur_rutin ?? $jadwalCurrent?->hari_libur_rutin;

        $dayMap = [
            'sunday' => 'minggu',
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => 'sabtu',
        ];
        $dayId = $dayMap[strtolower($carbonDate->format('l'))] ?? 'minggu';
        $isLiburRutin = !$isBeforeRegistered && $effectiveLiburRutinDay && strtolower($effectiveLiburRutinDay) === $dayId;

        // Agenda toko
        $agenda = AgendaLiburToko::where('tanggal', $date)
            ->where('status', '!=', 'dibatalkan')
            ->where(function ($q) use ($user) {
                $q->whereNull('cabang_id')->orWhere('cabang_id', $user->cabang_id);
            })
            ->first();

        // Absensi
        $absensi = Absensi::where('user_id', $user->id)->where('tanggal', $date)->first();

        // Izin
        $izin = PengajuanIzin::where('user_id', $user->id)
            ->where('status', 'Disetujui')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date)
            ->first();

        // Lembur
        $lembur = Lembur::where('user_id', $user->id)
            ->where('tanggal', $date)
            ->where('status_pengajuan', 'Disetujui')
            ->first();

        $this->selectedDateDetail = [
            'date' => $date,
            'formattedDate' => $carbonDate->translatedFormat('l, d F Y'),
            'dayName' => $carbonDate->translatedFormat('l'),
            'isToday' => $carbonDate->isToday(),
            'isPast' => $carbonDate->isPast() && !$carbonDate->isToday(),
            'isFuture' => $carbonDate->isFuture(),
            'isBeforeRegistered' => $isBeforeRegistered,
            'tglMulaiFormatted' => Carbon::parse($tglMulaiKerja)->translatedFormat('d F Y'),
            'isLiburRutin' => $isLiburRutin,
            'hariLiburRutin' => ucfirst($effectiveLiburRutinDay ?? 'Minggu'),
            'shift' => (!$isBeforeRegistered && $effectiveShift) ? [
                'nama' => $effectiveShift->nama_shift,
                'jam_masuk' => Carbon::parse($effectiveShift->jam_masuk)->format('H:i'),
                'jam_pulang' => Carbon::parse($effectiveShift->jam_pulang)->format('H:i'),
            ] : null,
            'agenda' => $agenda ? [
                'nama' => $agenda->nama_agenda,
                'jenis' => $agenda->jenis,
                'isTokoTutup' => $agenda->jenis === 'libur_kios',
                'keterangan' => $agenda->keterangan,
            ] : null,
            'absensi' => $absensi ? [
                'jam_masuk' => $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk)->format('H:i') : null,
                'jam_pulang' => $absensi->jam_pulang ? Carbon::parse($absensi->jam_pulang)->format('H:i') : null,
                'status_masuk' => $absensi->status_masuk,
                'menit_terlambat' => $absensi->menit_terlambat,
                'foto_masuk' => $absensi->foto_masuk,
                'foto_pulang' => $absensi->foto_pulang,
            ] : null,
            'izin' => $izin ? [
                'jenis' => $izin->jenis,
                'alasan' => $izin->alasan,
                'status' => $izin->status,
            ] : null,
            'lembur' => $lembur ? [
                'jam_mulai' => $lembur->jam_mulai_aktual ? Carbon::parse($lembur->jam_mulai_aktual)->format('H:i') : null,
                'jam_selesai' => $lembur->jam_selesai_aktual ? Carbon::parse($lembur->jam_selesai_aktual)->format('H:i') : null,
                'durasi' => round($lembur->durasi_menit / 60, 1),
                'tugas' => $lembur->alasan_lembur,
            ] : null,
        ];
    }

    public function closeDateDetail(): void
    {
        $this->selectedDateDetail = null;
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        $tglMulaiKerja = $user->profile?->tanggal_bergabung 
            ? Carbon::parse($user->profile->tanggal_bergabung)->toDateString()
            : ($user->created_at ? Carbon::parse($user->created_at)->toDateString() : '2026-01-01');

        $currentMonth = Carbon::createFromDate($this->year, $this->month, 1);
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // Week starts Monday (ISO)
        $startOfGrid = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfGrid = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $jadwalCurrent = JadwalKaryawan::with('shift')->where('user_id', $user->id)->first();
        $riwayatJadwals = RiwayatJadwalKaryawan::with('shift')
            ->where('user_id', $user->id)
            ->orderBy('berlaku_dari', 'asc')
            ->get();

        $dayMap = [
            'sunday' => 'minggu',
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => 'sabtu',
        ];

        // Fetch Agendas
        $agendas = AgendaLiburToko::whereBetween('tanggal', [$startOfGrid->toDateString(), $endOfGrid->toDateString()])
            ->where('status', '!=', 'dibatalkan')
            ->where(function ($q) use ($user) {
                $q->whereNull('cabang_id')->orWhere('cabang_id', $user->cabang_id);
            })
            ->get()
            ->keyBy(fn ($item) => Carbon::parse($item->tanggal)->toDateString());

        // Fetch Absensis
        $absensis = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfGrid->toDateString(), $endOfGrid->toDateString()])
            ->get()
            ->keyBy(fn ($item) => Carbon::parse($item->tanggal)->toDateString());

        // Fetch Approved Leaves
        $izins = PengajuanIzin::where('user_id', $user->id)
            ->where('status', 'Disetujui')
            ->whereDate('tanggal_mulai', '<=', $endOfGrid->toDateString())
            ->whereDate('tanggal_selesai', '>=', $startOfGrid->toDateString())
            ->get();

        // Fetch Approved Overtime
        $lemburs = Lembur::where('user_id', $user->id)
            ->where('status_pengajuan', 'Disetujui')
            ->whereBetween('tanggal', [$startOfGrid->toDateString(), $endOfGrid->toDateString()])
            ->get()
            ->keyBy(fn ($item) => Carbon::parse($item->tanggal)->toDateString());

        $days = [];
        $cursor = $startOfGrid->copy();

        while ($cursor->lte($endOfGrid)) {
            $dateStr = $cursor->toDateString();
            $isBeforeRegistered = $dateStr < $tglMulaiKerja;
            $dayOfWeekId = $dayMap[strtolower($cursor->format('l'))] ?? 'minggu';

            $agenda = $agendas->get($dateStr);
            $absensi = $absensis->get($dateStr);
            $lembur = $lemburs->get($dateStr);

            // Find overlapping izin
            $izin = $izins->first(function ($item) use ($dateStr) {
                return $dateStr >= $item->tanggal_mulai && $dateStr <= $item->tanggal_selesai;
            });

            // Jadwal historis yang berlaku pada hari ini
            $history = $riwayatJadwals->first(function ($r) use ($dateStr) {
                $dari = $r->berlaku_dari ? Carbon::parse($r->berlaku_dari)->toDateString() : null;
                $sampai = $r->berlaku_sampai ? Carbon::parse($r->berlaku_sampai)->toDateString() : null;
                return ($dari === null || $dateStr >= $dari) && ($sampai === null || $dateStr <= $sampai);
            });

            $effectiveShift = $history?->shift ?? $jadwalCurrent?->shift;
            $effectiveLiburRutinDay = $history?->hari_libur_rutin ?? $jadwalCurrent?->hari_libur_rutin;
            $isLiburRutin = !$isBeforeRegistered && $effectiveLiburRutinDay && strtolower($effectiveLiburRutinDay) === $dayOfWeekId;

            $events = [];

            // 1. Libur Toko (Kios Tutup / Agenda)
            if ($agenda) {
                if ($agenda->jenis === 'libur_kios') {
                    $events[] = [
                        'type' => 'danger',
                        'icon' => '🔴',
                        'label' => 'Toko Tutup: ' . Str::limit($agenda->nama_agenda, 16),
                        'tooltip' => 'Toko Tutup (' . $agenda->nama_agenda . ')',
                    ];
                } else {
                    $events[] = [
                        'type' => 'warning',
                        'icon' => '🧹',
                        'label' => 'Agenda: ' . Str::limit($agenda->nama_agenda, 16),
                        'tooltip' => $agenda->nama_agenda,
                    ];
                }
            }

            // HANYA JIKA KARYAWAN SUDAH BERGABUNG / TERDAFTAR:
            if (!$isBeforeRegistered) {
                // 2. Libur Rutin Mingguan (Berdasarkan jadwal historis pada tanggal ini!)
                if ($isLiburRutin && (!$agenda || $agenda->jenis !== 'libur_kios')) {
                    $events[] = [
                        'type' => 'emerald',
                        'icon' => '🏖️',
                        'label' => 'Libur Rutin',
                        'tooltip' => 'Hari Libur Rutin (' . ucfirst($effectiveLiburRutinDay) . ')',
                    ];
                }

                // 3. Izin / Cuti Disetujui
                if ($izin) {
                    $events[] = [
                        'type' => 'info',
                        'icon' => '🌴',
                        'label' => $izin->jenis . ' Disetujui',
                        'tooltip' => $izin->jenis . ': ' . $izin->alasan,
                    ];
                }

                // 4. Jadwal Shift Normal (hanya jika bukan libur rutin & bukan libur toko)
                if (!$isLiburRutin && (!$agenda || $agenda->jenis !== 'libur_kios') && !$izin && $effectiveShift) {
                    $events[] = [
                        'type' => 'amber',
                        'icon' => '🕒',
                        'label' => $effectiveShift->nama_shift . ' (' . Carbon::parse($effectiveShift->jam_masuk)->format('H:i') . ')',
                        'tooltip' => $effectiveShift->nama_shift . ' (' . Carbon::parse($effectiveShift->jam_masuk)->format('H:i') . ' - ' . Carbon::parse($effectiveShift->jam_pulang)->format('H:i') . ' WIB)',
                    ];
                }

                // 5. Lembur Disetujui
                if ($lembur) {
                    $events[] = [
                        'type' => 'purple',
                        'icon' => '⚡',
                        'label' => 'Lembur ' . round($lembur->durasi_menit / 60, 1) . ' Jam',
                        'tooltip' => 'Lembur: ' . $lembur->alasan_lembur,
                    ];
                }

                // 6. Riwayat Presensi Riil
                if ($absensi) {
                    $masuk = $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk)->format('H:i') : null;
                    $pulang = $absensi->jam_pulang ? Carbon::parse($absensi->jam_pulang)->format('H:i') : null;
                    $timeStr = $masuk ? ($masuk . ($pulang ? '-' . $pulang : '')) : '';
                    $timeSuffix = $timeStr ? " ({$timeStr})" : '';
                    
                    if ($absensi->status_masuk === 'Terlambat') {
                        $events[] = [
                            'type' => 'warning',
                            'icon' => '⚠️',
                            'label' => 'Terlambat' . $timeSuffix,
                            'tooltip' => 'Terlambat ' . ($absensi->menit_terlambat ?? 0) . ' menit',
                        ];
                    } elseif ($absensi->status_masuk === 'Hadir') {
                        $events[] = [
                            'type' => 'success',
                            'icon' => '✅',
                            'label' => 'Hadir' . $timeSuffix,
                            'tooltip' => 'Presensi Hadir: ' . ($timeStr ?: 'Tepat Waktu'),
                        ];
                    } elseif (in_array($absensi->status_masuk, ['Izin', 'Cuti', 'Sakit'])) {
                        if (!$izin) {
                            $events[] = [
                                'type' => 'info',
                                'icon' => '🌴',
                                'label' => $absensi->status_masuk,
                                'tooltip' => 'Status: ' . $absensi->status_masuk,
                            ];
                        }
                    }
                } elseif ($cursor->isPast() && !$cursor->isToday() && !$isLiburRutin && (!$agenda || $agenda->jenis !== 'libur_kios') && !$izin) {
                    // Past workday with no attendance -> Alpha / Tidak Hadir
                    $events[] = [
                        'type' => 'danger',
                        'icon' => '❌',
                        'label' => 'Tidak Hadir (Alpha)',
                        'tooltip' => 'Tidak ada catatan presensi pada hari kerja ini',
                    ];
                }
            }

            $days[] = [
                'date' => $dateStr,
                'dayNumber' => $cursor->day,
                'isCurrentMonth' => $cursor->month === $this->month,
                'isToday' => $cursor->isToday(),
                'isBeforeRegistered' => $isBeforeRegistered,
                'events' => $events,
            ];

            $cursor->addDay();
        }

        return [
            'monthTitle' => $currentMonth->translatedFormat('F Y'),
            'days' => $days,
            'shift' => $jadwalCurrent?->shift,
            'jadwal' => $jadwalCurrent,
            'tglMulaiKerja' => $tglMulaiKerja,
        ];
    }
}
