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
            $hariIni = \Carbon\Carbon::today()->toDateString();
            $tglAbsen = \Carbon\Carbon::parse($this->tanggal)->toDateString();

            // Jika tanggal absen sudah lewat dari hari ini
            if ($tglAbsen < $hariIni) {
                return 'Tidak Absen Pulang';
            }

            // Jika hari ini tapi sudah lewat dari batas wajar shift (3 jam setelah jam pulang)
            if ($this->jadwal?->shift) {
                $jamPulangShift = \Carbon\Carbon::createFromTimeString($this->jadwal->shift->jam_pulang);
                if (\Carbon\Carbon::now()->greaterThan($jamPulangShift->copy()->addHours(3))) {
                    return 'Tidak Absen Pulang';
                }
            }

            return 'Sedang Berlangsung';
        }
        return $this->status_masuk ?? 'Alpha';
    }

    public function getFormatJamKerjaAttribute(): string
    {
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