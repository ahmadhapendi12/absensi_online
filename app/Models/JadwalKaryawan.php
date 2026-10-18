<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Carbon\Carbon;

class JadwalKaryawan extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'shift_id', 'hari_libur_rutin'
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
                'hari_libur_rutin' => $jadwal->hari_libur_rutin,
                'berlaku_dari' => $startDate,
                'berlaku_sampai' => null,
            ]);
        });

        static::updated(function (JadwalKaryawan $jadwal) {
            $today = Carbon::today()->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();

            // Jika shift kerja diubah, sinkronkan langsung ke semua riwayat jadwal karyawan
            if ($jadwal->isDirty('shift_id')) {
                RiwayatJadwalKaryawan::where('user_id', $jadwal->user_id)
                    ->update(['shift_id' => $jadwal->shift_id]);
            }

            // Jika hari libur rutin mingguan diubah, kunci periode lama dan buka periode baru
            if ($jadwal->isDirty('hari_libur_rutin')) {
                $activeHistory = RiwayatJadwalKaryawan::where('user_id', $jadwal->user_id)
                    ->whereNull('berlaku_sampai')
                    ->latest('berlaku_dari')
                    ->first();

                if ($activeHistory) {
                    if ($activeHistory->berlaku_dari && $activeHistory->berlaku_dari->toDateString() >= $today) {
                        $activeHistory->update([
                            'hari_libur_rutin' => $jadwal->hari_libur_rutin,
                        ]);
                        return;
                    }

                    $activeHistory->update([
                        'berlaku_sampai' => $yesterday,
                    ]);
                }

                RiwayatJadwalKaryawan::create([
                    'user_id' => $jadwal->user_id,
                    'shift_id' => $jadwal->shift_id,
                    'hari_libur_rutin' => $jadwal->hari_libur_rutin,
                    'berlaku_dari' => $today,
                    'berlaku_sampai' => null,
                ]);
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