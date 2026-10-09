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
}