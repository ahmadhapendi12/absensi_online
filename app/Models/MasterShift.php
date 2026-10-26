<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MasterShift extends Model
{
    use HasUuids;

    protected $fillable = [
        'cabang_id', 'nama_shift', 'jam_masuk', 'jam_pulang', 'buka_absen_menit',
        'toleransi_terlambat_menit', 'toleransi_pulang_menit', 'batas_akhir_pulang_menit',
        'lintas_hari'
    ];

    protected $casts = [
        'lintas_hari' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function ($shift) {
            // Otomatis deteksi shift lintas hari jika jam pulang lebih kecil dari jam masuk (melewati 00:00)
            if ($shift->jam_masuk && $shift->jam_pulang) {
                $masuk = substr($shift->jam_masuk, 0, 5);
                $pulang = substr($shift->jam_pulang, 0, 5);
                $shift->lintas_hari = ($pulang < $masuk);
            }
        });
    }

    public function getIsLintasHariAttribute(): bool
    {
        return (bool) ($this->lintas_hari || ($this->jam_masuk && $this->jam_pulang && $this->jam_pulang < $this->jam_masuk));
    }

    public function cabang()
    {
        return $this->belongsTo(CabangKantor::class, 'cabang_id');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalKaryawan::class, 'shift_id');
    }
}