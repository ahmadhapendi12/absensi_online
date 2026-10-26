<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Carbon\Carbon;

class JadwalKaryawan extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'shift_id'
    ];

    protected static function booted(): void
    {
        static::created(function (JadwalKaryawan $jadwal) {
            $user = $jadwal->user;
            $startDate = $user?->profile?->tanggal_bergabung 
                ? Carbon::parse($user->profile->tanggal_bergabung)->toDateString()
                : ($user?->created_at ? Carbon::parse($user->created_at)->toDateString() : Carbon::today()->toDateString());

            RiwayatJadwalKaryawan::create([
                'user_id' => $jadwal->user_id,
                'shift_id' => $jadwal->shift_id,
                'berlaku_dari' => $startDate,
                'berlaku_sampai' => null,
            ]);
        });

        static::updated(function (JadwalKaryawan $jadwal) {
            // Jika shift kerja diubah, sinkronkan ke riwayat jadwal karyawan aktif
            if ($jadwal->isDirty('shift_id')) {
                RiwayatJadwalKaryawan::where('user_id', $jadwal->user_id)
                    ->update(['shift_id' => $jadwal->shift_id]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(MasterShift::class, 'shift_id');
    }

    public function riwayats()
    {
        return $this->hasMany(RiwayatJadwalKaryawan::class, 'user_id', 'user_id')->orderBy('berlaku_dari', 'asc');
    }
}