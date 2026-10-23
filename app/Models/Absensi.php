<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Absensi extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'jadwal_id', 'pengajuan_izin_id', 'tanggal', 'jam_masuk', 'jam_pulang',
        'foto_masuk', 'foto_pulang', 'koordinat_masuk', 'koordinat_pulang',
        'status_masuk', 'menit_terlambat'
    ];

    protected $casts = [
        'menit_terlambat' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalKaryawan::class, 'jadwal_id');
    }

    public function pengajuanIzin()
    {
        return $this->belongsTo(PengajuanIzin::class, 'pengajuan_izin_id');
    }

    public function getStatusDisplayAttribute(): string
    {
        if ($this->jam_masuk && !$this->jam_pulang) {
            $shift = $this->jadwal?->shift;
            
            if ($shift && $shift->jam_pulang) {
                $isCrossDay = (bool) ($shift->lintas_hari || ($shift->jam_pulang < $shift->jam_masuk));
                $batasAkhirMenit = (int) ($shift->batas_akhir_pulang_menit ?? 180);
                
                // Tentukan target datetime berakhirnya shift
                $closingDateTime = \Carbon\Carbon::parse($this->tanggal)
                    ->setTimeFromTimeString($shift->jam_pulang)
                    ->addMinutes($batasAkhirMenit);

                if ($isCrossDay) {
                    $closingDateTime->addDay();
                }

                if (\Carbon\Carbon::now()->greaterThan($closingDateTime)) {
                    return 'Tidak Absen Pulang';
                }

                return 'Sedang Berlangsung';
            }

            // Jika tidak ada data shift, fallback batas akhir jam 23:59:59 hari tersebut
            $fallbackClosing = \Carbon\Carbon::parse($this->tanggal)->endOfDay();
            if (\Carbon\Carbon::now()->greaterThan($fallbackClosing)) {
                return 'Tidak Absen Pulang';
            }

            return 'Sedang Berlangsung';
        }
        return $this->status_masuk ?? 'Alpha';
    }

    public function getFormatJamKerjaAttribute(): string
    {
        if (!$this->jam_masuk && !$this->jam_pulang) {
            return '-';
        }
        $masuk = $this->jam_masuk ? \Carbon\Carbon::parse($this->jam_masuk)->format('H:i') : '-';
        if ($this->jam_masuk && !$this->jam_pulang) {
            if ($this->status_display === 'Tidak Absen Pulang') {
                return "{$masuk} - (Tidak Pulang)";
            }
            return "{$masuk} - ..";
        }
        $pulang = $this->jam_pulang ? \Carbon\Carbon::parse($this->jam_pulang)->format('H:i') : '-';
        return "{$masuk} - {$pulang}";
    }
}