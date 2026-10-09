<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MasterShift extends Model
{
    use HasUuids;

    protected $fillable = [
        'nama_shift', 'jam_masuk', 'jam_pulang', 'toleransi_terlambat_menit', 'lintas_hari'
    ];

    public function jadwal()
    {
        return $this->hasMany(JadwalKaryawan::class, 'shift_id');
    }
}