<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RiwayatJadwalKaryawan extends Model
{
    use HasUuids;

    protected $table = 'riwayat_jadwal_karyawans';

    protected $fillable = [
        'user_id',
        'shift_id',
        'berlaku_dari',
        'berlaku_sampai',
    ];

    protected $casts = [
        'berlaku_dari' => 'date',
        'berlaku_sampai' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(MasterShift::class, 'shift_id');
    }
}
